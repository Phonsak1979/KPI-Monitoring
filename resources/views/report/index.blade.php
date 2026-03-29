@extends('layouts.template')

@section('title', 'รายงานหน่วยบริการ')

@section('content')

    <!-- เพิ่ม icon ใน small-box หน้าจอ mobile -->
    <style>
        @media (max-width: 767.98px) {
            .small-box .icon {
                display: block !important;
            }
            .small-box .icon > i {
                font-size: 70px !important;
                top: 15px !important;
                right: 15px !important;
                opacity: 1.0;
            }
        }
    </style>

    <div class="container-fluid p-3">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <section class="content">
                    <div class="container-fluid">

                        <!-- ตัวกรองหน่วยบริการ -->
                        <div class="card">
                            <div class="card-header bg-gradient-primary">
                                <h3 class="card-title text-white">
                                    <i class="fas fa-filter mr-2"></i><b>กรองข้อมูลรายหน่วยบริการ</b>
                                </h3>
                            </div>
                            <div class="card-body">
                                <form method="GET" action="{{ route('report.index') }}" id="filterForm">
                                    <div class="form-group row mb-0 align-items-center">
                                        <label for="hospcode" class="col-sm-2 col-form-label text-right">เลือกหน่วยบริการ : </label>
                                        <div class="col-sm-6">
                                            <select name="hospcode" id="hospcode" class="custom-select"
                                                onchange="document.getElementById('filterForm').submit()">
                                                <option value="">-- รวมทุกหน่วยบริการ --</option>
                                                @foreach ($hospitalsList as $hospital)
                                                    <option value="{{ $hospital->hospital_code }}"
                                                        {{ $selectedHospcode == $hospital->hospital_code ? 'selected' : '' }}>
                                                        <i class="fas fa-home mr-2"></i>{{ $hospital->hospital_code }} : {{ $hospital->hospital_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-sm-4 mt-2 mt-sm-0">
                                            @if ($selectedHospcode)
                                                <a href="{{ route('report.index') }}" class="btn btn-outline-danger"><i
                                                        class="fas fa-times"></i> ล้างตัวกรอง</a>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- small box -->
                        <div class="row">
                            <div class="col-lg-2 col-md-4">
                                <div class="small-box bg-gradient-info">
                                    <div class="inner">
                                        <h3>{{ number_format($totalRankings) }}</h3>
                                        <p>HDC-KPI</p>
                                    </div>
                                    <div class="icon">
                                        <i class="far fa-list-alt"></i>
                                    </div>
                                    <a href="{{ request()->fullUrlWithQuery(['filterStatus' => 'all', 'page' => 1]) }}" class="small-box-footer">รายละเอียด <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4">
                                <div class="small-box bg-gradient-success">
                                    <div class="inner">
                                        <h3>{{ number_format($passedRankings) }}</h3>
                                        <p>ผ่านเกณฑ์</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <a href="{{ request()->fullUrlWithQuery(['filterStatus' => 'passed', 'page' => 1]) }}" class="small-box-footer">รายละเอียด <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-12">
                                <div class="small-box bg-gradient-danger">
                                    <div class="inner">
                                        <h3>{{ number_format($failedRankings) }}</h3>
                                        <p>ไม่ผ่านเกณฑ์</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-window-close"></i>
                                    </div>
                                    <a href="{{ request()->fullUrlWithQuery(['filterStatus' => 'failed', 'page' => 1]) }}" class="small-box-footer">รายละเอียด <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-12">
                                <div class="small-box bg-gradient-indigo">
                                    <div class="inner">
                                        <h3>{{ number_format($totalWeight, 2) }}</h3>
                                        <p>น้ำหนักคะแนน</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-server"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">รายละเอียด <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-12">
                                <div class="small-box bg-gradient-warning">
                                    <div class="inner">
                                        <h3>{{ number_format($totalScore, 2) }}</h3>
                                        <p>คะแนนรวม</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">รายละเอียด <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-2 col-md-4 col-sm-12">
                                <div class="small-box bg-gradient-teal">
                                    <div class="inner">
                                        <h3>{{ number_format($percentScore, 2) }} %</h3>
                                        <p>ร้อยละ</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-chart-pie"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">รายละเอียด <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>

                        <!-- ตารางข้อมูล KPI -->
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header bg-gradient-success">
                                        <h3 class="card-title">
                                            <i class="fas fa-tasks mr-2"></i><b>รายงานผลการดำเนินงาน</b>
                                            @if ($selectedHospcode)
                                                <span class="badge badge-warning ml-2">หน่วยบริการ : 
                                                    {{ $selectedHospcode }}</span>
                                            @else
                                                <span class="badge badge-warning ml-2">รวมทุกหน่วยบริการ</span>
                                            @endif
                                            
                                            @if (($filterStatus ?? 'all') === 'passed')
                                                <span class="badge badge-success ml-2">เฉพาะผ่านเกณฑ์</span>
                                            @elseif (($filterStatus ?? 'all') === 'failed')
                                                <span class="badge badge-danger ml-2">เฉพาะไม่ผ่านเกณฑ์</span>
                                            @endif
                                        </h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-hover mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width: 7%">ลำดับ</th>
                                                        <th class="text-center" style="width: 58%">ชื่อตัวชี้วัด</th>
                                                        <th class="text-right" style="width: 8%">เป้าหมาย</th>
                                                        <th class="text-right" style="width: 8%">ผลงาน</th>
                                                        <th class="text-center" style="width: 8%">ร้อยละ</th>
                                                        <th class="text-center" style="width: 5%">Rank</th>
                                                        <th class="text-center" style="width: 4%"><i
                                                                class="fas fa-search-plus"></i></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @php $currentDept = null; @endphp
                                                    @forelse ($rankings as $index => $ranking)
                                                        @php
                                                            $deptName = $ranking->department->department_name ?? 'ไม่มีกลุ่มงาน';
                                                            $percent = $ranking->percent;
                                                            $badgeClass = $percent >= $ranking->target_value ? 'bg-success' : 'bg-danger';
                                                        @endphp

                                                        @if ($currentDept !== $deptName)
                                                            <tr class="table-info" style="border-top: 3px solid #20c997; border-bottom: 1px solid #dee2e6;">
                                                                <td colspan="7">
                                                                    <h6 class="mb-0 font-weight-bold">
                                                                        <i class="fas fa-hospital-user mr-2 ml-2"></i>กลุ่มงาน : {{ $deptName }}
                                                                    </h6>
                                                                </td>
                                                            </tr>
                                                            @php $currentDept = $deptName; @endphp
                                                        @endif

                                                        <tr>
                                                            <td class="text-center">{{ $rankings->firstItem() + $index }}</td>
                                                            <td>
                                                                <div>
                                                                    <span class="badge bg-info" style="min-width: 50px; display: inline-block;">R{{ $ranking->ranking_code }}</span>
                                                                    {{ $ranking->ranking_name }}
                                                                    <span class="badge bg-indigo" style="min-width: 30px; display: inline-block;">{{ $ranking->weight }}</span>
                                                                    <span class="badge bg-warning" style="min-width: 30px; display: inline-block;">{{ $ranking->score_total }}</span>
                                                                    <a href="{{ $ranking->hdc_link }}" target="_blank" class="badge badge-primary" style="min-width: 30px; display: inline-block;" title="HDC Link">
                                                                        <i class="fas fa-link"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                            <td class="text-right text-bold">{{ number_format($ranking->total_target) }}</td>
                                                            <td class="text-right text-bold">{{ number_format($ranking->total_result) }}</td>
                                                            <td class="text-center">
                                                                <span class="badge {{ $badgeClass }}" style="min-width: 50px; display: inline-block;">{{ number_format($percent, 2) }}%</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-primary" style="min-width: 50px; display: inline-block;">{{ $ranking->rank }}</span>
                                                            </td>
                                                            <td class="text-center">
                                                                <a href="#" data-toggle="modal" data-target="#modal-kpi-{{ $ranking->id }}">
                                                                    <i class="fas fa-search-plus text-primary"></i>
                                                                </a>
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
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="ml-2">
                                        รายการที่ {{ $rankings->firstItem() ?? 0 }} - {{ $rankings->lastItem() ?? 0 }} จาก
                                        {{ $rankings->total() }} รายการ
                                    </div>
                                    <div class="mr-2">
                                        {{ $rankings->appends(request()->query())->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>

                <!-- Modals Popup KPI -->
                @foreach ($rankings as $ranking)
                    @if (isset($ranking->details) && count($ranking->details) > 0)
                        <div class="modal fade" id="modal-kpi-{{ $ranking->id }}" tabindex="-1" role="dialog"
                            aria-hidden="true">
                            <div class="modal-dialog modal-xl" role="document">
                                <div class="modal-content shadow-lg" style="border-radius: 10px;">
                                    <div class="modal-header bg-success border-0"
                                        style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                                        <h5 class="modal-title">
                                            {{ $ranking->ranking_code }} {{ $ranking->ranking_name }} (ร้อยละ
                                            {{ $ranking->target_value }})
                                        </h5>
                                        <button type="button" class="close text-white" data-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-striped table-bordered mb-0">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width: 5%">ลำดับ</th>
                                                        <th class="text-center" style="width: 40%">หน่วยบริการ</th>
                                                        <th class="text-right" style="width: 10%">เป้าหมาย</th>
                                                        <th class="text-right" style="width: 10%">ผลงาน</th>
                                                        <th class="text-center" style="width: 30%">ร้อยละ (%)</th>
                                                        <th class="text-center" style="width: 5%">Rank</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($ranking->details as $detail)
                                                        <tr>
                                                            <td class="text-center">{{ $loop->iteration }}</td>
                                                            <td><span class="badge badge-info"
                                                                    style="min-width: 50px; display: inline-block;">{{ $detail->hospcode }}</span>
                                                                {{ $detail->hospital_name }}
                                                            </td>
                                                            <td class="text-right">{{ number_format($detail->target) }}
                                                            </td>
                                                            <td class="text-right">{{ number_format($detail->result) }}
                                                            </td>
                                                            <td class="text-center">
                                                                @php
                                                                    $detailPercent = $detail->percent;
                                                                    $detailBadgeClass =
                                                                        $detailPercent >= $ranking->target_value
                                                                            ? 'bg-success'
                                                                            : 'bg-danger';
                                                                    $detailTextClass =
                                                                        $detailPercent >= $ranking->target_value
                                                                            ? 'text-success'
                                                                            : 'text-danger';
                                                                @endphp
                                                                <div class="progress progress-sm mb-1 bg-light"
                                                                    style="height: 8px; border-radius: 10px;">
                                                                    <div class="progress-bar {{ $detailBadgeClass }}"
                                                                        role="progressbar"
                                                                        style="width: {{ min(100, $detailPercent) }}%; border-radius: 10px;">
                                                                    </div>
                                                                </div>
                                                                <small
                                                                    class="font-weight-bold {{ $detailTextClass }}">{{ number_format($detailPercent, 2) }}%</small>
                                                            </td>
                                                            <td class="text-center">
                                                                <span class="badge bg-primary"
                                                                    style="min-width: 50px; display: inline-block;">{{ $detail->rank }}</span>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                                <tfoot class="bg-light font-weight-bold">
                                                    <tr style="font-size: 1.15rem;">
                                                        <td colspan="2" class="text-center "
                                                            style="border-bottom-left-radius: 10px;">รวมทุกหน่วยบริการ</td>
                                                        <td class="text-right text-dark">
                                                            {{ number_format($ranking->total_target) }}</td>
                                                        <td class="text-right text-dark">
                                                            {{ number_format($ranking->total_result) }}</td>
                                                        <td class="text-center">
                                                            @if ($ranking->percent >= $ranking->target_value)
                                                                <span
                                                                    class="text-success">{{ number_format($ranking->percent, 2) }}%</span>
                                                            @else
                                                                <span
                                                                    class="text-danger">{{ number_format($ranking->percent, 2) }}%</span>
                                                            @endif
                                                        </td>
                                                        <td class="text-center" style="border-bottom-right-radius: 10px;">
                                                            <span class="badge bg-primary"
                                                                style="min-width: 50px; display: inline-block;">{{ $ranking->rank }}</span>
                                                        </td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection
