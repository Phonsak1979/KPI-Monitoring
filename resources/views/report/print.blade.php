<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>พิมพ์รายงานรายงานผลการดำเนินงาน KPI</title>
    @include('layouts.head')
    <style>
        body { background: #fff; color: #000; padding: 20px; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; margin: 0; }
            .card { border: none !important; box-shadow: none !important; }
            .table-bordered th, .table-bordered td { border: 1px solid #000 !important; }
            .badge { border: 1px solid #000; color: #000; background: transparent !important; }
            a { text-decoration: none; color: #000; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
            thead { display: table-header-group; }
            tfoot { display: table-footer-group; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h2>รายงานผลการดำเนินงาน KPI HDC</h2>
                <h4>
                    @if ($selectedHospcode)
                        หน่วยบริการ : {{ $selectedHospcode }}
                    @else
                        รวมทุกหน่วยบริการ
                    @endif
                </h4>
                @if (($filterStatus ?? 'all') === 'passed')
                    <h5>(เฉพาะผ่านเกณฑ์)</h5>
                @elseif (($filterStatus ?? 'all') === 'failed')
                    <h5>(เฉพาะไม่ผ่านเกณฑ์)</h5>
                @endif
            </div>
        </div>

        <div class="row mb-3 no-print">
            <div class="col-12 text-right">
                <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print mr-2"></i>พิมพ์หน้านี้ / บันทึกเป็น PDF</button>
                <button onclick="window.close()" class="btn btn-secondary ml-2">ปิดหน้าต่าง</button>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <table class="table table-bordered table-sm">

                    <tbody>
                        @php $currentDept = null; $index = 1; @endphp
                        @forelse ($rankings as $ranking)
                            @php
                                $deptName = $ranking->department->department_name ?? 'ไม่มีกลุ่มงาน';
                                $percent = $ranking->percent;
                            @endphp

                            @if ($currentDept !== $deptName)
                                <tr style="border-top: 2px solid #000; border-bottom: 2px solid #000;">
                                    <td colspan="7" class="py-2 bg-white">
                                        <h5 class="mb-0 font-weight-bold">
                                            กลุ่มงาน : {{ $deptName }}
                                        </h5>
                                    </td>
                                </tr>
                                <tr style="background-color: #fcfcfc;">
                                    <th class="text-center" style="width: 5%; border-bottom: 2px solid #dee2e6;">ลำดับ</th>
                                    <th class="text-center" style="width: 54%; border-bottom: 2px solid #dee2e6;">ชื่อตัวชี้วัด (น้ำหนักคะแนน)</th>
                                    <th class="text-right" style="width: 8%; border-bottom: 2px solid #dee2e6;">เป้าหมาย</th>
                                    <th class="text-right" style="width: 8%; border-bottom: 2px solid #dee2e6;">ผลงาน</th>
                                    <th class="text-center" style="width: 9%; border-bottom: 2px solid #dee2e6;">ร้อยละ</th>
                                    <th class="text-center" style="width: 6%; border-bottom: 2px solid #dee2e6;">Rank</th>
                                    <th class="text-center" style="width: 6%; border-bottom: 2px solid #dee2e6;">Score</th>
                                </tr>
                                @php $currentDept = $deptName; @endphp
                            @endif

                            <tr>
                                <td class="text-center">{{ $index++ }}</td>
                                <td>
                                    <div>
                                        <strong>R{{ $ranking->ranking_code }}</strong>
                                        {{ $ranking->ranking_name }}
                                        <span class="ml-2">(weight : {{ number_format($ranking->weight, 2) }})</span>
                                    </div>
                                </td>
                                <td class="text-right">{{ number_format($ranking->total_target) }}</td>
                                <td class="text-right">{{ number_format($ranking->total_result) }}</td>
                                <td class="text-center">
                                    {{ number_format($percent, 2) }}%
                                </td>
                                <td class="text-center">
                                    {{ $ranking->rank }}
                                </td>
                                <td class="text-center">
                                    {{ number_format($ranking->score_total, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">ไม่พบข้อมูลตัวชี้วัด</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @php
            $months = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
            $day = date('j');
            $month = $months[date('n')];
            $year = date('Y') + 543;
            $time = date('H.i');
            $currentDate = "$day $month $year เวลา $time น.";
        @endphp
        <div class="row mt-4 pb-5">
            <div class="col-6"></div>
            <div class="col-6">
                <table class="table table-bordered summary-table mb-1" style="border: 3px solid #000 !important; font-size: 1.25rem;">
                    <tr><th colspan="2" class="text-center bg-light" style="font-size: 1.35rem; border-bottom: 3px solid #000 !important;">สรุปผลการดำเนินงาน</th></tr>
                    <tr><td>จำนวน HDC KPI</td><td class="text-right" style="width: 45%"><b>{{ $totalRankings }}</b> ข้อ</td></tr>
                    <tr><td>ผ่านเกณฑ์</td><td class="text-right text-success"><b>{{ $passedRankings }}</b> ข้อ</td></tr>
                    <tr><td>ไม่ผ่านเกณฑ์</td><td class="text-right text-danger"><b>{{ $failedRankings }}</b> ข้อ</td></tr>
                    <tr><td>คะแนนรวม</td><td class="text-right"><b>{{ number_format($totalScore, 2) }} / {{ number_format($totalWeight, 2) }}</b> คะแนน</td></tr>
                    <tr><td>ร้อยละความสำเร็จ</td><td class="text-right"><b>{{ number_format($percentScore, 2) }} %</b></td></tr>
                </table>
                <div class="text-right text-muted" style="font-size: 1rem;">
                    วันที่ออกรายงาน {{ $currentDate }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
