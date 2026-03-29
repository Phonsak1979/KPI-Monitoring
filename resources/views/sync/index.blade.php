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

                        <!-- ปุ่ม Sync ข้อมูลทั้งหมด + ตั้งเวลา -->
                        <div class="mb-3 d-flex align-items-center">
                            <button type="button" class="btn btn-outline-success btn-sync-all text-bold mr-2">
                                <i class="fas fa-sync-alt mr-1"></i> Sync Data ทุกข้อ
                            </button>
                            <button type="button" class="btn btn-outline-info text-bold" data-toggle="modal" data-target="#syncScheduleModal">
                                <i class="fas fa-clock mr-1"></i> ตั้งเวลา Sync อัตโนมัติ
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

    <!-- ==========================================
         Modal: ตั้งเวลา Sync อัตโนมัติ
         ========================================== -->
    <div class="modal fade" id="syncScheduleModal" tabindex="-1" role="dialog" aria-labelledby="syncScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-gradient-info">
                    <h5 class="modal-title" id="syncScheduleModalLabel">
                        <i class="fas fa-clock mr-2"></i><b>ตั้งเวลา Sync อัตโนมัติ</b>
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                    <!-- ฟอร์มเพิ่มเวลา -->
                    <div class="card card-outline card-info mb-3">
                        <div class="card-body">
                            <div class="row align-items-end">
                                <div class="col-md-6">
                                    <label for="inputSyncTime" class="font-weight-bold">
                                        <i class="fas fa-plus-circle text-info mr-1"></i> เพิ่มเวลา Sync ใหม่
                                    </label>
                                    <input type="time" class="form-control" id="inputSyncTime" required>
                                </div>
                                <div class="col-md-6 mt-2 mt-md-0">
                                    <button type="button" class="btn btn-info text-bold" id="btnSaveSchedule">
                                        <i class="fas fa-save mr-1"></i> บันทึกเวลา
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ตารางรายการเวลา -->
                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title"><i class="fas fa-list mr-1"></i> รายการเวลาที่ตั้งไว้</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead class="thead-light">
                                    <tr>
                                        <th class="text-center" style="width: 10%">ลำดับ</th>
                                        <th class="text-center" style="width: 25%">เวลา</th>
                                        <th class="text-center" style="width: 20%">สถานะ</th>
                                        <th class="text-center" style="width: 25%">รันล่าสุด</th>
                                        <th class="text-center" style="width: 20%">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody id="scheduleTableBody">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fas fa-spinner fa-spin mr-1"></i> กำลังโหลด...
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>หมายเหตุ:</strong> ระบบจะ Sync ข้อมูลทุกตัวชี้วัดอัตโนมัติตามเวลาที่ตั้งไว้ในแต่ละวัน
                        <br><small>** ต้องตั้ง Cron Job / Task Scheduler บน Server เพื่อให้ระบบทำงานอัตโนมัติ **</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times mr-1"></i> ปิด
                    </button>
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

        // ==========================================
        // ลอจิกสำหรับ Modal ตั้งเวลา Sync อัตโนมัติ
        // ==========================================

        // โหลดรายการ Schedule เมื่อเปิด Modal
        $('#syncScheduleModal').on('shown.bs.modal', function() {
            loadSchedules();
        });

        // ฟังก์ชันโหลดรายการ Schedule
        function loadSchedules() {
            $.ajax({
                url: '{{ route('sync.schedules') }}',
                type: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderScheduleTable(response.data);
                    }
                },
                error: function() {
                    $('#scheduleTableBody').html(
                        '<tr><td colspan="5" class="text-center text-danger py-3">' +
                        '<i class="fas fa-exclamation-triangle mr-1"></i> ไม่สามารถโหลดข้อมูลได้</td></tr>'
                    );
                }
            });
        }

        // ฟังก์ชันแสดงตารางข้อมูล Schedule
        function renderScheduleTable(data) {
            var tbody = $('#scheduleTableBody');
            tbody.empty();

            if (data.length === 0) {
                tbody.html(
                    '<tr><td colspan="5" class="text-center text-muted py-4">' +
                    '<i class="fas fa-calendar-times mr-1"></i> ยังไม่มีเวลาที่ตั้งไว้</td></tr>'
                );
                return;
            }

            $.each(data, function(index, schedule) {
                var statusBadge = schedule.is_active
                    ? '<span class="badge badge-success"><i class="fas fa-check-circle mr-1"></i>เปิดใช้งาน</span>'
                    : '<span class="badge badge-secondary"><i class="fas fa-pause-circle mr-1"></i>ปิดใช้งาน</span>';

                var toggleBtnClass = schedule.is_active ? 'btn-outline-warning' : 'btn-outline-success';
                var toggleBtnIcon = schedule.is_active ? 'fa-pause' : 'fa-play';
                var toggleBtnTitle = schedule.is_active ? 'ปิดใช้งาน' : 'เปิดใช้งาน';

                var lastRun = schedule.last_run_at
                    ? new Date(schedule.last_run_at).toLocaleString('th-TH', { day:'2-digit', month:'2-digit', year:'numeric', hour:'2-digit', minute:'2-digit' })
                    : '<span class="text-muted">ยังไม่เคยรัน</span>';

                // ปุ่มดูผลลัพธ์ (เฉพาะเมื่อมี last_run_result)
                var resultBtn = '';
                if (schedule.last_run_result) {
                    resultBtn = '  <button class="btn btn-outline-info btn-sm mr-1 btn-view-result" data-result=\'' + JSON.stringify(schedule.last_run_result) + '\' data-time="' + schedule.sync_time + '" title="ดูผลลัพธ์">' +
                        '    <i class="fas fa-list-alt"></i>' +
                        '  </button>';
                }

                var row = '<tr>' +
                    '<td class="text-center align-middle">' + (index + 1) + '</td>' +
                    '<td class="text-center align-middle"><span class="font-weight-bold" style="font-size: 1.2em;"><i class="far fa-clock mr-1 text-info"></i>' + schedule.sync_time + ' น.</span></td>' +
                    '<td class="text-center align-middle">' + statusBadge + '</td>' +
                    '<td class="text-center align-middle">' + lastRun + '</td>' +
                    '<td class="text-center align-middle">' +
                    resultBtn +
                    '  <button class="btn ' + toggleBtnClass + ' btn-sm mr-1 btn-toggle-schedule" data-id="' + schedule.id + '" title="' + toggleBtnTitle + '">' +
                    '    <i class="fas ' + toggleBtnIcon + '"></i>' +
                    '  </button>' +
                    '  <button class="btn btn-outline-danger btn-sm btn-delete-schedule" data-id="' + schedule.id + '" data-time="' + schedule.sync_time + '" title="ลบ">' +
                    '    <i class="fas fa-trash-alt"></i>' +
                    '  </button>' +
                    '</td>' +
                    '</tr>';

                tbody.append(row);
            });
        }

        // ปุ่มบันทึกเวลา Sync ใหม่
        $('#btnSaveSchedule').click(function() {
            var syncTime = $('#inputSyncTime').val();

            if (!syncTime) {
                Swal.fire('แจ้งเตือน', 'กรุณาเลือกเวลาที่ต้องการ Sync', 'warning');
                return;
            }

            $.ajax({
                url: '{{ route('sync.schedules.save') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    sync_time: syncTime
                },
                success: function(response) {
                    if (response.success) {
                        Toast.fire({ icon: 'success', title: response.message });
                        $('#inputSyncTime').val('');
                        loadSchedules();
                    }
                },
                error: function(xhr) {
                    var msg = xhr.responseJSON && xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'เกิดข้อผิดพลาดในการบันทึก';
                    Swal.fire('ข้อผิดพลาด', msg, 'error');
                }
            });
        });

        // ปุ่มดูผลลัพธ์ Sync
        $(document).on('click', '.btn-view-result', function() {
            var result = $(this).data('result');
            var time = $(this).data('time');

            if (!result || !result.details) {
                Swal.fire('ไม่มีข้อมูล', 'ยังไม่มีผลลัพธ์การ Sync', 'info');
                return;
            }

            var summaryHtml = '<div class="text-left mb-3">' +
                '<div class="mb-2"><b>เวลาที่รัน:</b> ' + (result.started_at || '-') + '</div>' +
                '<div class="mb-2">' +
                '  <span class="badge badge-success p-2 mr-1"><i class="fas fa-check mr-1"></i>สำเร็จ ' + result.success_count + '</span>' +
                '  <span class="badge badge-danger p-2"><i class="fas fa-times mr-1"></i>ล้มเหลว ' + result.fail_count + '</span>' +
                '  <span class="badge badge-primary p-2 ml-1"><i class="fas fa-list mr-1"></i>ทั้งหมด ' + result.total + '</span>' +
                '</div></div>';

            var tableHtml = '<div style="max-height: 350px; overflow-y: auto;">' +
                '<table class="table table-sm table-bordered text-left" style="font-size: 0.85em;">' +
                '<thead class="thead-light"><tr>' +
                '<th class="text-center" style="width:15%">รหัส</th>' +
                '<th style="width:55%">ชื่อตัวชี้วัด</th>' +
                '<th class="text-center" style="width:15%">สถานะ</th>' +
                '<th class="text-center" style="width:15%">หมายเหตุ</th>' +
                '</tr></thead><tbody>';

            result.details.forEach(function(item) {
                var badge = item.status === 'success'
                    ? '<span class="badge badge-success"><i class="fas fa-check"></i></span>'
                    : '<span class="badge badge-danger"><i class="fas fa-times"></i></span>';
                var reasonColor = item.status === 'success' ? 'text-success' : 'text-danger';
                tableHtml += '<tr>' +
                    '<td class="text-center"><span class="badge bg-teal">R' + item.ranking_code + '</span></td>' +
                    '<td>' + (item.ranking_name || '-') + '</td>' +
                    '<td class="text-center">' + badge + '</td>' +
                    '<td class="text-center ' + reasonColor + '" style="font-size:0.85em">' + item.reason + '</td>' +
                    '</tr>';
            });

            tableHtml += '</tbody></table></div>';

            Swal.fire({
                title: '<i class="fas fa-clipboard-list mr-2"></i>ผลลัพธ์ Sync เวลา ' + time + ' น.',
                html: summaryHtml + tableHtml,
                width: '750px',
                confirmButtonColor: '#17a2b8',
                confirmButtonText: '<i class="fas fa-check"></i> ปิด'
            });
        });

        // ปุ่ม Toggle เปิด/ปิด Schedule
        $(document).on('click', '.btn-toggle-schedule', function() {
            var id = $(this).data('id');

            $.ajax({
                url: '/sync/schedules/' + id + '/toggle',
                type: 'PATCH',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    if (response.success) {
                        Toast.fire({ icon: 'success', title: response.message });
                        loadSchedules();
                    }
                },
                error: function() {
                    Swal.fire('ข้อผิดพลาด', 'ไม่สามารถเปลี่ยนสถานะได้', 'error');
                }
            });
        });

        // ปุ่มลบ Schedule
        $(document).on('click', '.btn-delete-schedule', function() {
            var id = $(this).data('id');
            var time = $(this).data('time');

            Swal.fire({
                title: 'ยืนยันการลบ?',
                html: 'ต้องการลบเวลา Sync <b class="text-danger">' + time + ' น.</b> ใช่หรือไม่?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash-alt"></i> ลบ',
                cancelButtonText: '<i class="fas fa-times"></i> ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/sync/schedules/' + id,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function(response) {
                            if (response.success) {
                                Toast.fire({ icon: 'success', title: response.message });
                                loadSchedules();
                            }
                        },
                        error: function() {
                            Swal.fire('ข้อผิดพลาด', 'ไม่สามารถลบข้อมูลได้', 'error');
                        }
                    });
                }
            });
        });
    </script>
@endsection
