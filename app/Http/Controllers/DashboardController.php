<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * ดึงข้อมูลและประมวลผลเพื่อนำไปแสดงผลในหน้า Dashboard
     */
    public function index()
    {
        // 1. นำเข้าข้อมูลพื้นฐาน
        // ดึงข้อมูลตัวชี้วัดทั้งหมดและเรียงลำดับตามรหัสตัวชี้วัด (รหัสที่เป็นตัวเลข/ตัวอักษร)
        $rankings = \App\Models\Ranking::all()->sortBy('ranking_code', SORT_NATURAL)->values();
        // Array รายชื่อรหัสหน่วยบริการ (โรงพยาบาล) ทั้งหมด เพื่อนำไปกรองข้อมูล
        $hospitalCodes = \App\Models\Hospital::pluck('hospital_code')->toArray();
        // Collection ข้อมูลหน่วยบริการ โดยให้ key คือ รหัสหน่วยบริการ เพื่อง่ายต่อการเอาค่าไปแมปแสดงผล
        $hospitals = \App\Models\Hospital::get()->keyBy('hospital_code');

        // เตรียมตัวแปรสำหรับเก็บข้อมูลรายโรงพยาบาล
        $hospitalStats = [];
        foreach ($hospitals as $hcode => $h) {
            $hospitalStats[$hcode] = [
                'hospcode' => $hcode,
                'hospital_name' => $h->hospital_name,
                'passed_kpi' => 0,
                'failed_kpi' => 0,
                'total_weight' => 0,
                'total_score' => 0,
            ];
        }

        // 2. วนลูปเพื่อประมวลผลและคำนวณค่า target, result, เปอร์เซ็นต์ ของตัวชี้วัดแต่ละตัว
        foreach ($rankings as $ranking) {
            // ตั้งค่าเริ่มต้นของผลลัพธ์รวมภาพรวม (ระดับบน) ให้เป็น 0 ไว้ก่อน
            $ranking->total_target = 0;
            $ranking->total_result = 0;
            $ranking->percent = 0;

            // ตรวจสอบว่ามีการระบุตารางเก็บข้อมูลผลงานหรือไม่ และตารางนั้นมีอยู่จริงในฐานข้อมูลรึเปล่า
            if ($ranking->table_name && \Illuminate\Support\Facades\Schema::hasTable($ranking->table_name)) {

                // =========================================================================
                // [ส่วนที่แก้ไข] กำหนดสูตรคำนวณ target และ result ให้ยืดหยุ่นตามตัวชี้วัด
                // =========================================================================
                // ตั้งค่า Default สำหรับตาราง KPI ทั่วไป (ดึงจากคอลัมน์ target, result ตรงๆ)
                $selectRawSql = 'hospcode, SUM(target) as target, SUM(result) as result';

                // [⭐ ต้องมีบรรทัดนี้ครับ ⭐] ตัดตัว R ออกให้เหลือแต่ตัวเลข เพื่อเอาไปใช้เข้าเงื่อนไข
                $code = str_replace(['R', 'r'], '', trim($ranking->ranking_code));

                // 1. กลุ่มตาราง s_childdev_specialpp
                // ถ้าเป็นตาราง s_childdev_specialpp ให้เปลี่ยนไปใช้สูตรบวกเลขจากข้อมูลดิบ
                if ($ranking->table_name === 's_childdev_specialpp') {

                    // R26.1: คัดกรองพัฒนาการ
                    if ($code === '26.1') {
                        $selectRawSql = "
                            hospcode,
                            SUM(IFNULL(target_9, 0) + IFNULL(target_18, 0) + IFNULL(target_30, 0) + IFNULL(target_42, 0) + IFNULL(target_60, 0)) AS target,
                            SUM(IFNULL(result_9, 0) + IFNULL(result_18, 0) + IFNULL(result_30, 0) + IFNULL(result_42, 0) + IFNULL(result_60, 0)) AS result
                        ";
                    }
                    // R26.2: พัฒนาการสงสัยล่าช้า
                    elseif ($code === '26.2') {
                        $selectRawSql = "
                            hospcode,
                            SUM(IFNULL(result_9, 0) + IFNULL(result_18, 0) + IFNULL(result_30, 0) + IFNULL(result_42, 0) + IFNULL(result_60, 0)) AS target,
                            SUM(
                                IFNULL(1b261_9, 0) + IFNULL(1b261_18, 0) + IFNULL(1b261_30, 0) + IFNULL(1b261_42, 0) + IFNULL(1b261_60, 0) + 
                                IFNULL(1b262_9, 0) + IFNULL(1b262_18, 0) + IFNULL(1b262_30, 0) + IFNULL(1b262_42, 0) + IFNULL(1b262_60, 0)
                            ) AS result
                        ";
                    }
                    // R26.3: เด็กพัฒนาการสมวัย
                    elseif ($code === '26.3') {
                        $selectRawSql = "
                            hospcode,
                            SUM(IFNULL(result_9, 0) + IFNULL(result_18, 0) + IFNULL(result_30, 0) + IFNULL(result_42, 0) + IFNULL(result_60, 0)) AS target,
                            SUM(IFNULL(1b260_1_9, 0) + IFNULL(1b260_1_18, 0) + IFNULL(1b260_1_30, 0) + IFNULL(1b260_1_42, 0) + IFNULL(1b260_1_60, 0)) AS result
                        ";
                    }
                    // R26.4: เด็กพัฒนาการล่าช้าได้รับการกระตุ้นจนสมวัย
                    elseif ($code === '26.4') {
                        $selectRawSql = "
                            hospcode,
                            SUM(IFNULL(1b261_9, 0) + IFNULL(1b261_18, 0) + IFNULL(1b261_30, 0) + IFNULL(1b261_42, 0) + IFNULL(1b261_60, 0)) AS target,
                            SUM(IFNULL(1b260_2_9, 0) + IFNULL(1b260_2_18, 0) + IFNULL(1b260_2_30, 0) + IFNULL(1b260_2_42, 0) + IFNULL(1b260_2_60, 0)) AS result
                        ";
                    }
                } // <--- ต้องมีปีกกาปิดของกลุ่ม s_childdev_specialpp ตรงนี้ก่อนครับ!

                // =========================================================
                // 2. กลุ่มตารางอื่นๆ (คัดกรองยาสูบ R28.1) ให้ขึ้น if ใหม่เลย
                // =========================================================

                // R28.1: ประชาชนอายุ 15 ปีขึ้นไปได้รับการคัดกรองผลิตภัณฑ์ยาสูบ
                elseif ($code === '28.1') {
                    $selectRawSql = "
                        hospcode,
                        SUM(IFNULL(target, 0)) AS target,
                        SUM(IFNULL(`1B5`, 0)) AS result
                    ";
                }

                // R28.2: ประชาชนอายุ 15 ปีขึ้นไปได้รับการคัดกรองเครื่องดื่มแอลกอฮอล์
                elseif ($code === '28.2') {
                    $selectRawSql = "
                        hospcode,
                        SUM(IFNULL(target, 0)) AS target,
                        SUM(IFNULL(`1B6`, 0)) AS result
                    ";
                }

                // =========================================================================

                // 3. คำนวณผลงานของแต่ละโรงพยาบาลในตัวชี้วัดนั้น ๆ (สำหรับแสดงใน Modal)
                $details = \Illuminate\Support\Facades\DB::table($ranking->table_name)
                    ->whereIn('hospcode', $hospitalCodes) // คัดกรองเอาเฉพาะโรงพยาบาลในระบบ
                    ->selectRaw($selectRawSql) // นำตัวแปร $selectRawSql มาใส่ตรงนี้แทนของเดิม
                    ->groupBy('hospcode') // จัดกลุ่มตามรหัสหน่วยบริการ
                    ->orderBy('hospcode')
                    ->get()
                    ->map(function ($item) use ($hospitals, $ranking) {
                        // แมปชื่อโรงพยาบาล โดยอ้างอิงจาก $hospitals ถ้าไม่พบให้แสดง 'ไม่ทราบชื่อ'
                        $item->hospital_name = isset($hospitals[$item->hospcode]) ? $hospitals[$item->hospcode]->hospital_name : 'ไม่ทราบชื่อ';

                        // คำนวณเปอร์เซ็นต์ผลงาน (ร้อยละ) ของแต่ละโรงพยาบาล
                        $item->percent = 0;
                        if ($item->target > 0) {
                            $item->percent = ($item->result / $item->target) * 100;
                        }

                        // คำนวณและประเมินระดับคะแนน (Rank) ด้วย Dynamic Function
                        $item->rank = $this->calculateDynamicRank($item->percent, $ranking);

                        return $item;
                    });

                // เก็บรายละเอียดรายโรงพยาบาลใส่กลับในตัวชี้วัดนั้นๆ เผื่อเรียกใช้ในจุดอื่น (เช่น แสดงใน Modal แบบละเอียด)
                $ranking->details = $details;

                // สร้าง lookup จาก $details เพื่อค้นหาข้อมูลของแต่ละโรงพยาบาลได้รวดเร็ว
                $detailsByHospcode = $details->keyBy('hospcode');

                // สะสมคะแนนรายโรงพยาบาล โดยลูปทุกโรงพยาบาลเพื่อให้จำนวนตัวชี้วัดที่ประเมินครบถ้วน
                foreach ($hospitalStats as $hospcode => $stat) {
                    $item = $detailsByHospcode->get($hospcode);

                    if ($item) {
                        $percent = $item->percent;
                        $rank = $item->rank;
                    } else {
                        // กรณีที่โรงพยาบาลไม่มีข้อมูลในตารางผลงาน ยึดตามผลงาน 0
                        $percent = 0;
                        $rank = $this->calculateDynamicRank(0, $ranking);
                    }

                    if ($percent >= $ranking->target_value) {
                        $hospitalStats[$hospcode]['passed_kpi']++;
                    } else {
                        $hospitalStats[$hospcode]['failed_kpi']++;
                    }

                    $weight = $ranking->weight ?? 0;
                    $score = ($rank / 5) * $weight;

                    $hospitalStats[$hospcode]['total_weight'] += $weight;
                    $hospitalStats[$hospcode]['total_score'] += $score;
                }

                // 4. คำนวณผลงานรวมภาพรวม (นำผลของโรงพยาบาลย่อยมารวมกัน)
                $ranking->total_target = $details->sum('target');
                $ranking->total_result = $details->sum('result');

                // คำนวณเปอร์เซ็นต์ภาพรวม ป้องกันการหารด้วย 0 (Division by Zero)
                if ($ranking->total_target > 0) {
                    $ranking->percent = ($ranking->total_result / $ranking->total_target) * 100;
                }

                // 5. ประเมินระดับคะแนน (Rank) ในส่วนผลงานภาพรวม ด้วย Dynamic Function
                $rank = $this->calculateDynamicRank($ranking->percent, $ranking);

                // 6. คำนวณคะแนนรวมที่ได้จากการถ่วงน้ำหนัก (Weighted Score)
                // สูตร: (เกรดที่ได้ / 5) * ค่าน้ำหนักตัวชี้วัด
                $score_total = ($rank / 5) * ($ranking->weight ?? 0);

                // นำคะแนน (Rank) 1-5 และคะแนนถ่วงน้ำหนักภาพรวม ไปอัปเดตบันทึกลงในตารางหลัก Rankings
                \Illuminate\Support\Facades\DB::table('rankings')
                    ->where('id', $ranking->id)
                    ->update([
                        'rank' => $rank,
                        'score_total' => $score_total
                    ]);

                // กำหนดตัวแปรไว้ใน Object เพื่อเตรียมส่งไปแสดงผลยังระดับ View (Blade)
                $ranking->rank = $rank;
                $ranking->score_total = $score_total;
            }
        }

        // 7. สรุปข้อมูลตัวเลขภาพรวมเพื่อนำไปแสดงผลด้านบนสุดของหน้า Dashboard (ส่วนกล่องสถานะ Small Box)
        $totalHospitals = count($hospitalCodes);
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

        // 8. คำนวณร้อยละของแต่ละโรงพยาบาล และสร้าง Pagination สำหรับตาราง
        foreach ($hospitalStats as &$stat) {
            if ($stat['total_weight'] > 0) {
                $stat['percent_score'] = ($stat['total_score'] / $stat['total_weight']) * 100;
            } else {
                $stat['percent_score'] = 0;
            }
        }
        unset($stat);

        $hospitalStatsCollection = collect(array_values($hospitalStats))->sortBy('hospcode')->values();

        // เตรียมข้อมูลสำหรับแสดงกราฟแท่ง (Bar Chart)
        $chartLabels = [];
        $chartData = [];
        $chartColors = [];

        foreach ($hospitalStatsCollection as $stat) {
            $chartLabels[] = "{$stat['hospcode']}";
            $chartData[] = round($stat['percent_score'], 2);

            if ($stat['percent_score'] >= 80) {
                $chartColors[] = 'rgba(40, 167, 69, 0.8)'; // success
            } elseif ($stat['percent_score'] >= 50) {
                $chartColors[] = 'rgba(255, 193, 7, 0.8)'; // warning
            } else {
                $chartColors[] = 'rgba(220, 53, 69, 0.8)'; // danger
            }
        }



        $page = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 50;

        $paginatedHospitals = new \Illuminate\Pagination\LengthAwarePaginator(
            $hospitalStatsCollection->forPage($page, $perPage)->values(),
            $hospitalStatsCollection->count(),
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        // 9. ส่งตัวแปรทั้งหมดไปประมวลผลแสดงหน้าจอบน View
        return view('dashboard', compact(
            'paginatedHospitals',
            'totalHospitals',
            'totalRankings',
            'passedRankings',
            'failedRankings',
            'totalWeight',
            'totalScore',
            'percentScore',
            'chartLabels',
            'chartData',
            'chartColors'
        ));
    }

    /**
     * ฟังก์ชันย่อยสำหรับคำนวณคะแนน Rank แบบยืดหยุ่น (Dynamic)
     * ข้ามเงื่อนไขที่เป็น null โดยอัตโนมัติ
     */
    private function calculateDynamicRank($percent, $ranking)
    {
        // จัดเรียงเงื่อนไขจากคะแนนสูงสุดไปต่ำสุด
        $criteria = [
            ['rank' => 5,   'val' => $ranking->score_5,   'op' => '>='],
            ['rank' => 4,   'val' => $ranking->score_4,   'op' => '>='],
            ['rank' => 3,   'val' => $ranking->score_3,   'op' => '>='],
            ['rank' => 2.5, 'val' => $ranking->score_2_5, 'op' => '>='],
            ['rank' => 2,   'val' => $ranking->score_2,   'op' => '>='],
            ['rank' => 1,   'val' => $ranking->score_1,   'op' => $ranking->score_1_operator ?? '>='],
            ['rank' => 0,   'val' => $ranking->score_0,   'op' => '<'], // Rank 0 ใช้ operator เป็นน้อยกว่าเสมอ
        ];

        foreach ($criteria as $check) {
            // ถ้า Database เป็น null หรือ string ว่าง (คือผู้ใช้ไม่ได้กรอกค่านี้) ให้ข้าม (Skip)
            if ($check['val'] === null || $check['val'] === '') {
                continue;
            }

            $threshold = (float) $check['val'];
            $operator = $check['op'];
            $isMatched = false;

            switch ($operator) {
                case '>=':
                    $isMatched = ($percent >= $threshold);
                    break;
                case '>':
                    $isMatched = ($percent > $threshold);
                    break;
                case '<=':
                    $isMatched = ($percent <= $threshold);
                    break;
                case '<':
                    $isMatched = ($percent < $threshold);
                    break;
                case '=':
                    $isMatched = ($percent == $threshold);
                    break;
            }

            // ทันทีที่เข้าเงื่อนไข (เพราะเราเรียงจากคะแนนสูงลงต่ำแล้ว) ให้คืนค่า Rank ทันที
            if ($isMatched) {
                return $check['rank'];
            }
        }

        return 0; // กรณีหลุดเงื่อนไขทั้งหมด (Fallback) ให้ได้ 0
    }
}
