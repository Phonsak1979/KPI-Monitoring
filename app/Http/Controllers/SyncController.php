<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ranking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use Throwable; // [เพิ่มใหม่] ดักจับ Error ขั้นสูงสุดรวมถึงแรมเต็ม

class SyncController extends Controller
{
    /**
     * สูตร SQL สำหรับการเรียงลำดับรหัสแบบ Natural Sort (เช่น 20.1, 20.2, ..., 20.11, 20.12)
     */
    private $naturalSortSql = "
        CAST(SUBSTRING_INDEX(ranking_code, '.', 1) AS UNSIGNED) ASC, 
        CASE WHEN LOCATE('.', ranking_code) > 0 
             THEN CAST(SUBSTRING_INDEX(ranking_code, '.', -1) AS UNSIGNED) 
             ELSE 0 
        END ASC
    ";

    public function index()
    {
        $kpiList = Ranking::orderByRaw($this->naturalSortSql)->paginate(50);
        $totalKpis = $kpiList->total();

        $kpiList->getCollection()->transform(function ($kpi) {
            $tableName = $kpi->table_name;
            $lastUpdated = null;

            if (!empty($tableName) && Schema::hasTable($tableName)) {
                $lastRecord = DB::table($tableName)->orderBy('updated_at', 'desc')->first();
                if ($lastRecord && isset($lastRecord->updated_at)) {
                    $lastUpdated = Carbon::parse($lastRecord->updated_at)->format('d/m/Y H:i');
                }
            }

            $kpi->updated_at_formatted = $lastUpdated;
            return $kpi;
        });

        return view('sync.index', ['rankings' => $kpiList, 'totalKpis' => $totalKpis]);
    }

    public function sync(Request $request, $id)
    {
        $ranking = Ranking::findOrFail($id);
        $success = $this->syncSingleRanking($ranking);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => $success,
                'message' => $success
                    ? 'Sync ข้อมูล ' . $ranking->ranking_code . ' สำเร็จ'
                    : 'เกิดข้อผิดพลาดในการ Sync ข้อมูลหรือการเชื่อมต่อ API สำหรับ ' . $ranking->ranking_code,
                'ranking_code' => $ranking->ranking_code
            ]);
        }

        if ($success) {
            return back()->with('success', 'Sync ข้อมูล : R' . $ranking->ranking_code . ' สำเร็จ');
        }

        return back()->with('error', 'เกิดข้อผิดพลาดในการ Sync ข้อมูลหรือการเชื่อมต่อ API สำหรับ : R' . $ranking->ranking_code);
    }

    public function getSyncList()
    {
        $rankings = Ranking::select('id', 'ranking_code', 'table_name')
            ->orderByRaw($this->naturalSortSql)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $rankings
        ]);
    }

    public function syncAll(Request $request)
    {
        $rankings = Ranking::all();
        $successCount = 0;
        $failCount = 0;
        $syncedTables = [];

        foreach ($rankings as $ranking) {
            $tableName = trim($ranking->table_name);

            if (empty($tableName)) {
                $failCount++;
                continue;
            }

            if (in_array($tableName, $syncedTables)) {
                $successCount++;
                continue;
            }

            if ($this->syncSingleRanking($ranking)) {
                $successCount++;
                $syncedTables[] = $tableName;
            } else {
                $failCount++;
            }
        }

        if ($failCount === 0) {
            return back()->with('success', "Sync ข้อมูลสำเร็จทั้งหมด {$successCount} ตัวชี้วัด");
        } elseif ($successCount > 0) {
            return back()->with('warning', "Sync ข้อมูลสำเร็จ {$successCount} ตัวชี้วัด และล้มเหลว {$failCount} ตัวชี้วัด");
        }

        return back()->with('error', "เกิดข้อผิดพลาดในการ Sync ข้อมูลทั้งหมด ({$failCount} ตัวชี้วัด)");
    }

    /**
     * ฟังก์ชันแกนหลัก (Core Functional): หน้าที่ยิง API ไปยัง HDC กลางแล้วจัดการข้อมูลเอาลงตาราง
     */
    private function syncSingleRanking(Ranking $ranking)
    {
        ini_set('max_execution_time', 0);
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $tableName = trim($ranking->table_name); // [แก้ไข] ป้องกัน Error กรณีตารางมีเว้นวรรค

        if (empty($tableName)) {
            \Illuminate\Support\Facades\Log::error("ไม่มีการระบุชื่อตารางสำหรับรหัสตัวชี้วัด: " . $ranking->ranking_code);
            return false;
        }

        $year = '2569';
        $province = '34';

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
                $data = $response->json();

                if ($data && is_array($data)) {

                    \Illuminate\Support\Facades\Log::info('ดึงข้อมูลตาราง ' . $tableName . ' ได้ทั้งหมด: ' . count($data) . ' แถว');

                    if (!Schema::hasTable($tableName)) {
                        \Illuminate\Support\Facades\Log::error("ไม่พบตารางในฐานข้อมูล: " . $tableName);
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

                    // กำหนด Unique Key
                    $uniqueFields = ['hospcode', 'areacode', 'b_year'];
                    if ($hasMonthly) {
                        $uniqueFields[] = 'monthly';
                    }
                    $uniqueBy = isset($columns['id']) ? ['id'] : $uniqueFields;

                    $insertData = [];

                    // ==============================================================
                    // [ระบบใหม่] วนลูปและประมวลผลแบบประหยัด RAM (Inline Chunking)
                    // ==============================================================
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

                        // เคลียร์ข้อมูลที่ถูกวนลูปแล้วทิ้งทันที เพื่อคืนพื้นที่ RAM!
                        unset($data[$index]);

                        // บันทึกย่อยทุกๆ 500 แถว แล้วเคลียร์แรม
                        if (count($insertData) >= 500) {
                            DB::table($tableName)->upsert($insertData, $uniqueBy, $updateColumns);
                            $insertData = []; // รีเซ็ตชุดข้อมูล
                        }
                    }

                    // บันทึกข้อมูลเศษที่เหลือจาก 500 แถวสุดท้าย
                    if (count($insertData) > 0) {
                        DB::table($tableName)->upsert($insertData, $uniqueBy, $updateColumns);
                    }

                    return true;
                }
            } else {
                \Illuminate\Support\Facades\Log::warning("API HDC ไม่ตอบสนอง (ตาราง {$tableName}): HTTP สถานะ " . $response->status());
            }

            // เปลี่ยนมาใช้ Throwable จะดักจับ Error ได้ทุกสายพันธุ์รวมถึงแรมเต็ม
        } catch (Throwable $e) {
            \Illuminate\Support\Facades\Log::error("API Sync Failed สำหรับตาราง {$tableName}: " . $e->getMessage());
            return false;
        }

        return false;
    }
}
