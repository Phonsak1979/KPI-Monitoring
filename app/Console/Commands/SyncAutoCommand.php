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

            if ($this->syncSingleRanking($ranking)) {
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

    /**
     * Sync ข้อมูลเดี่ยว (คัดลอกมาจาก SyncController::syncSingleRanking)
     */
    private function syncSingleRanking(Ranking $ranking)
    {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $tableName = trim($ranking->table_name);

        if (empty($tableName)) {
            Log::error("ไม่มีการระบุชื่อตารางสำหรับรหัสตัวชี้วัด: " . $ranking->ranking_code);
            return false;
        }

        $year = '2569';
        $province = '34';

        try {
            $maxRetries = 4;
            $attempt = 0;
            $response = null;

            while ($attempt < $maxRetries) {
                try {
                    $response = Http::withoutVerifying()
                        ->timeout(600)
                        ->post('https://opendata.moph.go.th/api/report_data', [
                            'tableName' => $tableName,
                            'year' => $year,
                            'province' => $province,
                            'type' => 'json'
                        ]);

                    if ($response->successful()) {
                        break;
                    }

                    Log::warning("API MOPH Open-Data ไม่ตอบสนอง (ตาราง {$tableName}): HTTP สถานะ " . $response->status() . " (ความพยายามที่ " . ($attempt + 1) . "/{$maxRetries})");
                } catch (\Exception $e) {
                    Log::warning("API MOPH Open-Data Error (ตาราง {$tableName}): " . $e->getMessage() . " (ความพยายามที่ " . ($attempt + 1) . "/{$maxRetries})");
                }

                $attempt++;
                if ($attempt < $maxRetries) {
                    sleep(6);
                }
            }

            if ($response && $response->successful()) {
                $data = $response->json();

                if ($data && is_array($data)) {
                    Log::info('ดึงข้อมูลตาราง ' . $tableName . ' ได้ทั้งหมด: ' . count($data) . ' แถว');

                    if (!Schema::hasTable($tableName)) {
                        Log::error("ไม่พบตารางในฐานข้อมูล: " . $tableName);
                        return false;
                    }

                    $columns = array_flip(Schema::getColumnListing($tableName));
                    $updateColumns = array_diff(array_keys($columns), ['id', 'created_at']);

                    $dbColumns = DB::select("SHOW COLUMNS FROM {$tableName}");
                    $colTypes = [];
                    foreach ($dbColumns as $col) {
                        $colTypes[$col->Field] = strtolower($col->Type);
                    }

                    $hasMonthly = isset($columns['monthly']);

                    $uniqueFields = ['hospcode', 'areacode', 'b_year'];
                    if ($hasMonthly) {
                        $uniqueFields[] = 'monthly';
                    }
                    $uniqueBy = isset($columns['id']) ? ['id'] : $uniqueFields;

                    $insertData = [];

                    foreach ($data as $index => $row) {
                        $conditions = [
                            'hospcode' => $row['hospcode'] ?? '',
                            'areacode' => $row['areacode'] ?? '',
                            'b_year'   => $row['b_year'] ?? $year,
                        ];

                        if ($hasMonthly && isset($row['monthly'])) {
                            $conditions['monthly'] = $row['monthly'];
                        }

                        $values = [];
                        foreach ($row as $key => $value) {
                            if ($key === 'id') {
                                continue;
                            }

                            if (isset($columns[$key]) && !array_key_exists($key, $conditions)) {
                                $type = $colTypes[$key] ?? 'varchar';
                                $isNumeric = (strpos($type, 'int') !== false || strpos($type, 'float') !== false || strpos($type, 'double') !== false || strpos($type, 'decimal') !== false);

                                if ($value === '' || $value === null) {
                                    $values[$key] = $isNumeric ? 0 : '';
                                } else {
                                    $values[$key] = $value;
                                }
                            }
                        }

                        if ($tableName === 's_kpi_dental61') {
                            $target1 = isset($values['target1']) ? (float)$values['target1'] : 0;
                            $target2 = isset($values['target2']) ? (float)$values['target2'] : 0;
                            $result1 = isset($values['result1']) ? (float)$values['result1'] : 0;
                            $result2 = isset($values['result2']) ? (float)$values['result2'] : 0;

                            $values['target'] = $target1 + $target2;
                            $values['result'] = $result1 + $result2;
                        }

                        if ($tableName === 's_epi2') {
                            $months = ['10', '11', '12', '01', '02', '03', '04', '05', '06', '07', '08', '09'];
                            $target = 0;
                            $result = 0;
                            foreach ($months as $month) {
                                $target += isset($values["target{$month}"]) ? (float)$values["target{$month}"] : 0;
                                $result += isset($values["mmr2_{$month}"]) ? (float)$values["mmr2_{$month}"] : 0;
                            }
                            $values['target'] = $target;
                            $values['result'] = $result;
                        }

                        $values['updated_at'] = now();
                        $values['created_at'] = now();

                        $rowData = array_merge($conditions, $values);

                        if (isset($columns['id'])) {
                            $rowData['id'] = md5(implode('', $conditions) . $tableName);
                        }

                        $insertData[] = $rowData;

                        unset($data[$index]);

                        if (count($insertData) >= 500) {
                            DB::table($tableName)->upsert($insertData, $uniqueBy, $updateColumns);
                            $insertData = [];
                        }
                    }

                    if (count($insertData) > 0) {
                        DB::table($tableName)->upsert($insertData, $uniqueBy, $updateColumns);
                    }

                    return true;
                }
            } else {
                $status = $response ? $response->status() : 'Unknown (Connection Failed)';
                Log::error("API MOPH Open-Data ล้มเหลว (ตาราง {$tableName}) ตลอดการลอง {$maxRetries} ครั้ง HTTP สถานะ: {$status}");
            }

        } catch (Throwable $e) {
            Log::error("Sync Auto Failed สำหรับตาราง {$tableName}: " . $e->getMessage());
            return false;
        }

        return false;
    }
}
