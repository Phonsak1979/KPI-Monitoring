<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #000000; padding: 5px; font-family: Tahoma, sans-serif; font-size: 10pt; }
        th { background-color: #20c997; font-weight: bold; text-align: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <table>
        <tr>
            <th colspan="7" style="font-size: 14pt; padding: 10px;">รายงานผลการดำเนินงาน KPI {{ $selectedHospcode ? '(หน่วยบริการ: ' . $selectedHospcode . ')' : '(รวมทุกหน่วยบริการ)' }}</th>
        </tr>
        <tr>
            <th style="width: 50px;">ลำดับ</th>
            <th style="width: 400px;">ชื่อตัวชี้วัด</th>
            <th>เป้าหมาย</th>
            <th>ผลงาน</th>
            <th>ร้อยละ</th>
            <th>Rank</th>
            <th>Score</th>
        </tr>
        @php $currentDept = null; $index = 1; @endphp
        @forelse ($rankings as $ranking)
            @php
                $deptName = $ranking->department->department_name ?? 'ไม่มีกลุ่มงาน';
                $percent = $ranking->percent;
            @endphp

            @if ($currentDept !== $deptName)
                <tr>
                    <td colspan="7" style="background-color: #e9ecef; font-weight: bold;">กลุ่มงาน : {{ $deptName }}</td>
                </tr>
                @php $currentDept = $deptName; @endphp
            @endif

            <tr>
                <td class="text-center">{{ $index++ }}</td>
                <td>R{{ $ranking->ranking_code }} - {{ $ranking->ranking_name }} (weight : {{ number_format($ranking->weight, 2) }})</td>
                <td class="text-right">{{ number_format($ranking->total_target) }}</td>
                <td class="text-right">{{ number_format($ranking->total_result) }}</td>
                <td class="text-center">{{ number_format($percent, 2) }}%</td>
                <td class="text-center">{{ $ranking->rank }}</td>
                <td class="text-center">{{ number_format($ranking->score_total, 2) }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="7" class="text-center">ไม่พบข้อมูลตัวชี้วัด</td>
            </tr>
        @endforelse
    </table>
</body>
</html>
