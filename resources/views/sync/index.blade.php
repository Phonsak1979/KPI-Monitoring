@extends('layouts.template')

@section('title', 'Sync ข้อมูล')

@section('content')
    <div class="container-fluid p-3">

        <div class="row justify-content-center">

            <div class="col-md-10">

                <div class="card">
                    <div class="card-header bg-gradient-success">
                        <h3 class="card-title mb-0"><i class="fas fa-server mr-2"></i><b>Sync MOPH Open-Data</b></h3>
                    </div>
                    <div class="card-body">

                        <!-- ปุ่ม Sync ข้อมูลทั้งหมด -->
                        <div class="mb-3">
                            <button type="button" class="btn btn-outline-success btn-sync-all text-bold">
                                <i class="fas fa-sync-alt mr-1"></i> Sync Data ทุกข้อ
                            </button>
                        </div>

                        <!-- ตารางแสดงรายการตัวชี้วัด -->
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%">ลำดับ</th>
                                    <th class="text-center" style="width: 60%">ชื่อตัวชี้วัด</th>
                                    <th class="text-center" style="width: 20%">อัปเดตล่าสุด</th>
                                    <th class="text-center" style="width: 15%">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rankings as $index => $kpi)
                                    <tr>
                                        <td class="text-center align-middle">{{ $rankings->firstItem() + $loop->index }}
                                        </td>
                                        <td class="align-middle">
                                            <span class="badge bg-teal"
                                                style="min-width: 50px; display: inline-block;">
                                                R{{ $kpi->ranking_code }}
                                            </span>
                                            {{ $kpi->ranking_name }}
                                        </td>

                                        <td class="text-center align-middle">
                                            @php
                                                // ตรวจสอบว่าวันที่อัปเดตล่าสุดคือ "วันนี้" หรือไม่
                                                $isUpdatedToday = false;
                                                if ($kpi->updated_at_formatted) {
                                                    // ตัดเอาเฉพาะวันที่ (DD/MM/YYYY) มาเทียบกับวันนี้
                                                    $updatedDateOnly = substr($kpi->updated_at_formatted, 0, 10);
                                                    $todayDateOnly = \Carbon\Carbon::now()->format('d/m/Y');
                                                    $isUpdatedToday = $updatedDateOnly === $todayDateOnly;
                                                }
                                            @endphp

                                            @if ($kpi->updated_at_formatted)
                                                @if ($isUpdatedToday)
                                                    <span class="badge badge-success mr-1">
                                                        <i class="fas fa-calendar-check mr-1"></i>
                                                        {{ $kpi->updated_at_formatted }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning mr-1"
                                                        title="ข้อมูลยังไม่อัปเดตเป็นของวันนี้">
                                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                                        {{ $kpi->updated_at_formatted }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-warning font-weight-bold">
                                                    <i class="fas fa-calendar-times mr-1"></i> ยังไม่มีข้อมูล
                                                </span>
                                            @endif
                                        </td>

                                        <td class="text-center align-middle">

                                            @php
                                                // ตัดตัว R ออก และตัดช่องว่าง เผื่อข้อมูลใน DB พิมพ์มาไม่เหมือนกัน
                                                $cleanCode = str_replace(['R', 'r'], '', trim($kpi->ranking_code));

                                                // [แก้ไขใหม่] สร้าง Array จับคู่ตัวชี้วัดที่ซ้ำกัน [ 'ตัวที่ต้องซ่อน' => 'ตัวหลักที่ต้องกด' ]
                                                $duplicateMap = [
                                                    '26.2' => '26.1',
                                                    '26.3' => '26.1',
                                                    '26.4' => '26.1',
                                                    '25.2' => '3.4', // เพิ่ม 25.2 ให้อิงกับ 3.4
                                                    // ถ้าอนาคตเจอตัวไหนซ้ำอีก ก็พิมพ์เพิ่มบรรทัดตรงนี้ได้เลยครับ เช่น '25.3' => '3.5'
                                                ];

                                                // เช็คว่ารหัสปัจจุบันอยู่ในรายการที่ต้องซ่อนหรือไม่
                                                $isDuplicate = array_key_exists($cleanCode, $duplicateMap);
                                            @endphp

                                            @if ($isDuplicate)
                                                @php
                                                    // ดึงรหัสตัวหลักมาแสดงบนปุ่ม
                                                    $parentCode = $duplicateMap[$cleanCode];
                                                @endphp
                                                {{-- ปิดปุ่มสำหรับตัวชี้วัดที่ซ้ำ และแสดงไอคอนลิงก์ --}}
                                                <button type="button" class="btn btn-outline-secondary btn-sm text-bold" disabled
                                                    title="ข้อมูลนี้อัปเดตพร้อมกับ R{{ $parentCode }} แล้ว เพื่อป้องกันการดึงซ้ำซ้อน">
                                                    <i class="fas fa-link"></i> R{{ $parentCode }}
                                                </button>
                                            @else
                                                <form action="{{ route('sync.update', $kpi->id) }}" method="POST"
                                                    class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-primary btn-sm btn-sync-single text-bold"
                                                        data-code="{{ $kpi->ranking_code }}">
                                                        <i class="fas fa-sync-alt"></i> Sync
                                                    </button>
                                                </form>
                                            @endif

                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="ml-2 text-muted">
                        แสดงรายการที่ {{ $rankings->firstItem() ?? 0 }} ถึง {{ $rankings->lastItem() ?? 0 }} จากทั้งหมด
                        {{ $rankings->total() }} รายการ
                    </div>
                    <div class="mr-2">
                        {{ $rankings->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('JS')
    <script>
        // ==========================================
        // ลอจิกสำหรับการกดปุ่ม "ซิงค์ทั้งหมด" (Sync-All)
        // ==========================================
        $('.btn-sync-all').click(function(event) {
            event.preventDefault();
            Swal.fire({
                title: 'ยืนยันการ Sync ทุกตัวชี้วัด?',
                text: "ระบบจะทำการ Sync ข้อมูลทุกตัวชี้วัด อาจใช้เวลาหลายนาที กรุณาอย่าปิดหน้าต่างนี้จนกว่าจะเสร็จสิ้น",
                footer: '<span style="color: red;">*** มีระบบหน่วงเวลา 5 วินาที เพื่อป้องกัน Server Block ***</span>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="fas fa-sync-alt"></i> เริ่ม Sync Data',
                cancelButtonText: '<i class="fas fa-times"></i> ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {

                    Swal.fire({
                        title: 'กำลังเตรียมข้อมูล...',
                        html: 'ระบบกำลังอ่านรายการ KPI กรุณารอสักครู่',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // ยิง Ajax เพื่อไปขอรายชื่อ KPI ทั้งหมดมาวนลูปทำ Progress Bar
                    $.ajax({
                        url: '{{ route('sync.list') }}',
                        type: 'GET',
                        success: function(response) {
                            if (response.success && response.data.length > 0) {
                                let kpis = response.data;
                                let total = kpis.length;
                                let current = 0;
                                let successCount = 0;
                                let failCount = 0;
                                let failedItems = [];

                                // จำชื่อตารางที่ซิงค์ไปแล้ว ป้องกันการดึงซ้ำ (ลดภาระ MOPH Open-Data)
                                let syncedTables = [];

                                Swal.fire({
                                    title: 'กำลัง Sync ข้อมูล...',
                                    html: `
                                        <div class="mb-2 font-weight-bold text-primary" id="sync-status-text" style="font-size: 1.1em;">กำลังโหลดข้อมูล (0/${total})</div>
                                        <div class="progress shadow-sm" style="height: 25px; border-radius: 15px;">
                                            <div id="sync-progress-bar" class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 0%; font-weight: bold; font-size: 1rem;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
                                        </div>
                                        <div class="mt-3 text-muted" id="sync-current-kpi" style="min-height: 20px;">...</div>
                                    `,
                                    allowOutsideClick: false,
                                    showConfirmButton: false
                                });

                                // ฟังก์ชันวนลูปซิงค์ทีละตัวชี้วัด (Recursive)
                                function syncNext() {
                                    if (current >= total) {
                                        let finalTitle = failCount === 0 ?
                                            "Sync ข้อมูลสำเร็จทั้งหมด" : "Sync ข้อมูลเสร็จสิ้น";
                                        let finalIcon = failCount === 0 ? "success" : "warning";

                                        let resultHtml =
                                            `Sync ข้อมูลสำเร็จ : <b>${successCount}</b> รายการ<br>ล้มเหลว : <b>${failCount}</b> รายการ`;
                                        if (failCount > 0) {
                                            resultHtml +=
                                                `<div class="mt-3 text-danger text-left p-2 bg-light rounded" style="font-size: 0.9em; border:1px solid #ffc107; max-height: 150px; overflow-y: auto;"><b>รายการที่ล้มเหลว:</b><br>${failedItems.join('<br>')}</div>`;
                                        }

                                        Swal.fire({
                                            title: finalTitle,
                                            html: resultHtml,
                                            icon: finalIcon,
                                            confirmButtonColor: '#28a745'
                                        }).then(() => {
                                            window.location.reload();
                                        });
                                        return;
                                    }

                                    let kpi = kpis[current];
                                    let percent = Math.round(((current) / total) * 100);

                                    // จัดการชื่อตาราง (ตัดช่องว่างทิ้ง ป้องกันบัคจากฐานข้อมูล)
                                    let safeTableName = kpi.table_name ? kpi.table_name.trim() :
                                        '';

                                    $('#sync-status-text').text(
                                        `กำลังประมวลผล (${current + 1}/${total})`);
                                    $('#sync-progress-bar').css('width', percent + '%').text(
                                        percent + '%').attr('aria-valuenow', percent);
                                    $('#sync-current-kpi').html(
                                        `กำลัง Sync : <b>รหัส R${kpi.ranking_code}</b> (ตาราง : ${safeTableName || 'ไม่มีข้อมูล'})`
                                    );

                                    // 1. ถ้ายังไม่ได้ระบุชื่อตารางในระบบ ให้ขึ้นล้มเหลวและข้ามไปเลย
                                    if (!safeTableName) {
                                        failCount++;
                                        failedItems.push('R' + kpi.ranking_code +
                                            ' (ลืมใส่ชื่อตารางในระบบ)');
                                        current++;
                                        setTimeout(syncNext, 500);
                                        return;
                                    }

                                    // 2. ถ้าเป็นตารางซ้ำที่เคยดึงไปแล้ว ให้ถือว่า "สำเร็จ" และข้ามไปเลย (ช่วยเซิร์ฟเวอร์ MOPH Open-Data)
                                    if (syncedTables.includes(safeTableName)) {
                                        successCount++;
                                        current++;
                                        setTimeout(syncNext, 500);
                                        return;
                                    }

                                    // 3. ยิง API ของจริง
                                    $.ajax({
                                        url: '/sync/' + kpi.id,
                                        type: 'POST',
                                        timeout: 600000, // รอนานสุด 10 นาที
                                        data: {
                                            _token: '{{ csrf_token() }}'
                                        },
                                        success: function(res) {
                                            if (res.success) {
                                                successCount++;
                                                syncedTables.push(safeTableName);
                                            } else {
                                                failCount++;
                                                failedItems.push('R' + kpi
                                                    .ranking_code +
                                                    ' (MOPH Open-Data ไม่ตอบสนอง)');
                                            }
                                            current++;
                                            // หน่วงเวลา 5 วินาที (5000 ms) ก่อนดึงตัวต่อไป
                                            setTimeout(syncNext, 5000);
                                        },
                                        error: function(xhr, status, error) {
                                            // ถ้า Status 200 แต่เข้าเงื่อนไข Error แปลว่า Browser รอนานจนตัดสายเอง (ถือว่าข้อมูลเข้า DB สำเร็จแล้ว)
                                            if (xhr.status === 200) {
                                                successCount++;
                                                syncedTables.push(safeTableName);
                                            } else {
                                                failCount++;
                                                let reason = status === 'timeout' ?
                                                    ' (MOPH Open-Data API Timeout)' :
                                                    ' (MOPH Open-Data Connection Error)';
                                                failedItems.push('R' + kpi
                                                    .ranking_code + reason);
                                            }
                                            current++;
                                            // หน่วงเวลา 5 วินาที
                                            setTimeout(syncNext, 5000);
                                        }
                                    });
                                }

                                syncNext(); // เริ่มลูป

                            } else {
                                Swal.fire('ข้อผิดพลาด',
                                    'ไม่พบข้อมูล KPI ที่ต้อง Sync ใน Database', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('ข้อผิดพลาด',
                                'ไม่สามารถเชื่อมต่อ Server เพื่อดึงรายการ Sync ได้',
                                'error');
                        }
                    });
                }
            });
        });

        // ==========================================
        // ลอจิกสำหรับการกดปุ่ม Sync ทีละ 1 ตัว (Single)
        // ==========================================
        $('.btn-sync-single').click(function(event) {
            var form = $(this).closest("form");
            var code = $(this).data("code");
            event.preventDefault();

            Swal.fire({
                title: 'ยืนยันการ Sync ข้อมูล?',
                html: "ต้องการ <b>Sync</b> ตัวชี้วัด : <b class='text-primary' style='font-size:1.2em;'> R" +
                    code + " </b>ใช่หรือไม่?",
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#d33',
                confirmButtonText: '<i class="fas fa-sync-alt"></i> ยืนยัน',
                cancelButtonText: '<i class="fas fa-times"></i> ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'กำลังเชื่อมต่อ MOPH Open-Data',
                        html: '<div class="mt-2">กำลังดึงข้อมูล : <b class="text-primary">R' +
                            code +
                            '</b> อาจใช้เวลาสักครู่...</div>',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    form.submit();
                }
            });
        });
    </script>
@endsection
