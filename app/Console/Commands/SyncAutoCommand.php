<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ranking;
use App\Models\SyncSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Throwable;

class SyncAutoCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sync:auto';

    /**
     * The console command description.
     */
    protected $description = 'Sync ข้อมูล MOPH Open-Data อัตโนมัติตามเวลาที่ตั้งไว้';

    /**
     * สูตร Natural Sort สำหรับเรียงลำดับรหัสตัวชี้วัด
     */
    private $naturalSortSql = "
        CAST(SUBSTRING_INDEX(ranking_code, '.', 1) AS UNSIGNED) ASC, 
        CASE WHEN LOCATE('.', ranking_code) > 0 
             THEN CAST(SUBSTRING_INDEX(ranking_code, '.', -1) AS UNSIGNED) 
             ELSE 0 
        END ASC
    ";

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $currentTime = Carbon::now()->format('H:i');

        // ตรวจสอบว่ามี schedule ที่ active และตรงกับเวลาปัจจุบัน
        $schedules = SyncSchedule::where('is_active', true)
            ->where('sync_time', $currentTime)
            ->get();

        if ($schedules->isEmpty()) {
            return 0; // ไม่มี schedule ที่ตรงเวลา
        }

        $this->info("========================================");
        $this->info("เริ่มต้น Sync อัตโนมัติ เวลา {$currentTime}");
        $this->info("========================================");

        Log::info("Sync Auto: เริ่มต้น Sync อัตโนมัติ เวลา {$currentTime}");

        $rankings = Ranking::orderByRaw($this->naturalSortSql)->get();
        $successCount = 0;
        $failCount = 0;
        $syncedTables = [];
        $resultDetails = []; // เก็บรายละเอียดผลลัพธ์แต่ละตัวชี้วัด

        foreach ($rankings as $ranking) {
            $tableName = trim($ranking->table_name);

            if (empty($tableName)) {
                $failCount++;
                $resultDetails[] = [
                    'ranking_code' => $ranking->ranking_code,
                    'ranking_name' => $ranking->ranking_name,
                    'status' => 'fail',
                    'reason' => 'ไม่มีชื่อตาราง',
                ];
                $this->warn("ข้าม R{$ranking->ranking_code} (ไม่มีชื่อตาราง)");
                continue;
            }

            // ข้ามตารางที่ sync ไปแล้ว (ป้องกันการดึงซ้ำ)
            if (in_array($tableName, $syncedTables)) {
                $successCount++;
                $resultDetails[] = [
                    'ranking_code' => $ranking->ranking_code,
                    'ranking_name' => $ranking->ranking_name,
                    'status' => 'success',
                    'reason' => 'ตารางซ้ำ (sync แล้ว)',
                ];
                continue;
            }

            $this->info("กำลัง Sync: R{$ranking->ranking_code} (ตาราง: {$tableName})");

            $syncController = app(\App\Http\Controllers\SyncController::class);
            if ($syncController->syncSingleRanking($ranking)) {
                $successCount++;
                $syncedTables[] = $tableName;
                $resultDetails[] = [
                    'ranking_code' => $ranking->ranking_code,
                    'ranking_name' => $ranking->ranking_name,
                    'status' => 'success',
                    'reason' => 'สำเร็จ',
                ];
                $this->info("✓ R{$ranking->ranking_code} - สำเร็จ");
            } else {
                $failCount++;
                $resultDetails[] = [
                    'ranking_code' => $ranking->ranking_code,
                    'ranking_name' => $ranking->ranking_name,
                    'status' => 'fail',
                    'reason' => 'API ไม่ตอบสนอง',
                ];
                $this->error("✗ R{$ranking->ranking_code} - ล้มเหลว");
            }

            // หน่วงเวลา 5 วินาที ป้องกัน API ต้นทางบล็อก
            sleep(5);
        }

        // สรุปผลลัพธ์
        $runResult = [
            'success_count' => $successCount,
            'fail_count' => $failCount,
            'total' => $successCount + $failCount,
            'started_at' => Carbon::now()->format('d/m/Y H:i'),
            'details' => $resultDetails,
        ];

        // อัปเดต last_run_at และ last_run_result สำหรับ schedule ที่ตรงเวลา
        foreach ($schedules as $schedule) {
            $schedule->update([
                'last_run_at' => now(),
                'last_run_result' => $runResult,
            ]);
        }

        $summary = "Sync Auto เสร็จสิ้น: สำเร็จ {$successCount} / ล้มเหลว {$failCount}";
        $this->info("========================================");
        $this->info($summary);
        $this->info("========================================");

        Log::info("Sync Auto: {$summary}");

        return 0;
    }


}
