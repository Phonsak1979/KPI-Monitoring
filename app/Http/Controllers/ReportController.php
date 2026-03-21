<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Ranking;
use App\Models\Hospital;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class ReportController extends Controller
{
    /**
     * ดึงข้อมูลรายงาน สามารถกรองข้อมูลตามหน่วยบริการ (hospcode)
     */
    public function index(Request $request)
    {
        // 1. นำเข้าข้อมูลพื้นฐาน
        $rankings = Ranking::all()->sortBy('ranking_code', SORT_NATURAL)->values();
        
        // ข้อมูลหน่วยบริการ (เพื่อไปแสดงใน Dropdown) เรียงตาม รหัส รพ.
        $hospitalsList = Hospital::orderBy('hospital_code')->get();
        // Collection ข้อมูลหน่วยบริการ เพื่อใช้แมปชื่อ
        $hospitalsMap = $hospitalsList->keyBy('hospital_code');

        // รับค่าการกรอง από Request
        $selectedHospcode = $request->input('hospcode');
        
        // ถ้ามีการเลือกหน่วยบริการ ให้กรองเฉพาะรหัสที่เลือก ถ้าไม่มีก็ดึงทั้งหมดเหมือน Dashboard Default 
        if ($selectedHospcode) {
            $hospitalCodes = [$selectedHospcode];
        } else {
            $hospitalCodes = $hospitalsList->pluck('hospital_code')->toArray();
        }

        // 2. วนลูปเพื่อประมวลผลและคำนวณค่า target, result, เปอร์เซ็นต์ ของตัวชี้วัดแต่ละตัว
        foreach ($rankings as $ranking) {
            $ranking->total_target = 0;
            $ranking->total_result = 0;
            $ranking->percent = 0;

            if ($ranking->table_name && \Illuminate\Support\Facades\Schema::hasTable($ranking->table_name)) {

                // =========================================================================
                // กำหนดสูตรคำนวณ target และ result
                // =========================================================================
                $selectRawSql = 'hospcode, SUM(target) as target, SUM(result) as result';
                $code = str_replace(['R', 'r'], '', trim($ranking->ranking_code));

                // กลุ่มตาราง s_childdev_specialpp
                if ($ranking->table_name === 's_childdev_specialpp') {
                    if ($code === '26.1') {
                        $selectRawSql = "
                            hospcode,
                            SUM(IFNULL(target_9, 0) + IFNULL(target_18, 0) + IFNULL(target_30, 0) + IFNULL(target_42, 0) + IFNULL(target_60, 0)) AS target,
                            SUM(IFNULL(result_9, 0) + IFNULL(result_18, 0) + IFNULL(result_30, 0) + IFNULL(result_42, 0) + IFNULL(result_60, 0)) AS result
                        ";
                    } elseif ($code === '26.2') {
                        $selectRawSql = "
                            hospcode,
                            SUM(IFNULL(result_9, 0) + IFNULL(result_18, 0) + IFNULL(result_30, 0) + IFNULL(result_42, 0) + IFNULL(result_60, 0)) AS target,
                            SUM(
                                IFNULL(1b261_9, 0) + IFNULL(1b261_18, 0) + IFNULL(1b261_30, 0) + IFNULL(1b261_42, 0) + IFNULL(1b261_60, 0) + 
                                IFNULL(1b262_9, 0) + IFNULL(1b262_18, 0) + IFNULL(1b262_30, 0) + IFNULL(1b262_42, 0) + IFNULL(1b262_60, 0)
                            ) AS result
                        ";
                    } elseif ($code === '26.3') {
                        $selectRawSql = "
                            hospcode,
                            SUM(IFNULL(result_9, 0) + IFNULL(result_18, 0) + IFNULL(result_30, 0) + IFNULL(result_42, 0) + IFNULL(result_60, 0)) AS target,
                            SUM(IFNULL(1b260_1_9, 0) + IFNULL(1b260_1_18, 0) + IFNULL(1b260_1_30, 0) + IFNULL(1b260_1_42, 0) + IFNULL(1b260_1_60, 0)) AS result
                        ";
                    } elseif ($code === '26.4') {
                        $selectRawSql = "
                            hospcode,
                            SUM(IFNULL(1b261_9, 0) + IFNULL(1b261_18, 0) + IFNULL(1b261_30, 0) + IFNULL(1b261_42, 0) + IFNULL(1b261_60, 0)) AS target,
                            SUM(IFNULL(1b260_2_9, 0) + IFNULL(1b260_2_18, 0) + IFNULL(1b260_2_30, 0) + IFNULL(1b260_2_42, 0) + IFNULL(1b260_2_60, 0)) AS result
                        ";
                    }
                }
                // กลุ่มคัดกรองบุหรี่และสุรา
                elseif ($code === '28.1') {
                    $selectRawSql = "
                        hospcode,
                        SUM(IFNULL(target, 0)) AS target,
                        SUM(IFNULL(`1B5`, 0)) AS result
                    ";
                } elseif ($code === '28.2') {
                    $selectRawSql = "
                        hospcode,
                        SUM(IFNULL(target, 0)) AS target,
                        SUM(IFNULL(`1B6`, 0)) AS result
                    ";
                }

                // =========================================================================
                // 3. ดึงข้อมูลและคำนวณผลงานของแต่ละโรงพยาบาลที่เรา filter มา
                $details = DB::table($ranking->table_name)
                    ->whereIn('hospcode', $hospitalCodes) // กรองเฉพาะรายการ รพ. ที่จำกัดไว้
                    ->selectRaw($selectRawSql) 
                    ->groupBy('hospcode')
                    ->orderBy('hospcode')
                    ->get()
                    ->map(function ($item) use ($hospitalsMap, $ranking) {
                        $item->hospital_name = isset($hospitalsMap[$item->hospcode]) ? $hospitalsMap[$item->hospcode]->hospital_name : 'ไม่ทราบชื่อ';
                        $item->percent = 0;
                        if ($item->target > 0) {
                            $item->percent = ($item->result / $item->target) * 100;
                        }
                        $item->rank = $this->calculateDynamicRank($item->percent, $ranking);
                        return $item;
                    });

                // เก็บ details กรณีนำไปโชว์ใน Modal
                $ranking->details = $details;

                // 4. คำนวณผลรวมตามที่เลือก (ถ้าเลือก 1 รพ. ก็ได้ค่าของ 1 รพ.)
                $ranking->total_target = $details->sum('target');
                $ranking->total_result = $details->sum('result');

                if ($ranking->total_target > 0) {
                    $ranking->percent = ($ranking->total_result / $ranking->total_target) * 100;
                }

                $rank = $this->calculateDynamicRank($ranking->percent, $ranking);
                $score_total = ($rank / 5) * ($ranking->weight ?? 0);

                $ranking->rank = $rank;
                $ranking->score_total = $score_total;
            }
        }

        // 5. สรุปภาพรวมสำหรับ Small Box
        $totalHospitals = count($hospitalCodes); // จำนวน รพ. ที่ใช้คิด (ถ้าเลือกอันเดียวก็โชว์ 1)
        $totalRankings = $rankings->count();
        $passedRankings = 0;
        $failedRankings = 0;
        $totalWeight = 0;
        $totalScore = 0;

        foreach ($rankings as $ranking) {
            if ($ranking->percent >= $ranking->target_value) {
                $passedRankings++;
            } else {
                $failedRankings++;
            }

            $totalWeight += $ranking->weight ?? 0;
            $totalScore += $ranking->score_total ?? 0;
        }

        $percentScore = 0;
        if ($totalWeight > 0) {
            $percentScore = ($totalScore / $totalWeight) * 100;
        }

        // 5.5 Filter Rankings for the table View
        $filterStatus = $request->input('filterStatus', 'all');
        $filteredRankings = $rankings;
        if ($filterStatus === 'passed') {
            $filteredRankings = $rankings->filter(function ($ranking) {
                return $ranking->percent >= $ranking->target_value;
            });
        } elseif ($filterStatus === 'failed') {
            $filteredRankings = $rankings->filter(function ($ranking) {
                return $ranking->percent < $ranking->target_value;
            });
        }

        // 6. Pagination
        $page = Paginator::resolveCurrentPage() ?: 1;
        $perPage = 50;
        $paginatedRankings = new LengthAwarePaginator(
            $filteredRankings->forPage($page, $perPage)->values(),
            $filteredRankings->count(),
            $perPage,
            $page,
            ['path' => Paginator::resolveCurrentPath()]
        );
        $rankings = $paginatedRankings;

        // 7. ส่งไปที่ View ใหม่ (เราจะสร้างไฟล์ resource/views/report/index.blade.php)
        return view('report.index', compact(
            'rankings',
            'hospitalsList',
            'selectedHospcode',
            'totalHospitals',
            'totalRankings',
            'passedRankings',
            'failedRankings',
            'totalWeight',
            'totalScore',
            'percentScore',
            'filterStatus'
        ));
    }

    /**
     * ฟังก์ชันย่อยสำหรับคำนวณคะแนน Rank
     */
    private function calculateDynamicRank($percent, $ranking)
    {
        $criteria = [
            ['rank' => 5,   'val' => $ranking->score_5,   'op' => '>='],
            ['rank' => 4,   'val' => $ranking->score_4,   'op' => '>='],
            ['rank' => 3,   'val' => $ranking->score_3,   'op' => '>='],
            ['rank' => 2.5, 'val' => $ranking->score_2_5, 'op' => '>='],
            ['rank' => 2,   'val' => $ranking->score_2,   'op' => '>='],
            ['rank' => 1,   'val' => $ranking->score_1,   'op' => $ranking->score_1_operator ?? '>='],
            ['rank' => 0,   'val' => $ranking->score_0,   'op' => '<'],
        ];

        foreach ($criteria as $check) {
            if ($check['val'] === null || $check['val'] === '') {
                continue;
            }
            $threshold = (float) $check['val'];
            $operator = $check['op'];
            $isMatched = false;

            switch ($operator) {
                case '>=': $isMatched = ($percent >= $threshold); break;
                case '>':  $isMatched = ($percent > $threshold); break;
                case '<=': $isMatched = ($percent <= $threshold); break;
                case '<':  $isMatched = ($percent < $threshold); break;
                case '=':  $isMatched = ($percent == $threshold); break;
            }

            if ($isMatched) {
                return $check['rank'];
            }
        }
        return 0;
    }
}
