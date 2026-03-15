@extends('layouts.template')

@section('title', 'หน้าแรก')

@section('content')
    <div class="container-fluid p-3">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <section class="content">
                    <div class="container-fluid">

                        <!-- small box Row 1 -->
                        <div class="row">
                            <div class="col-lg-4 col-12">
                                <div class="small-box bg-info">
                                    <div class="inner">
                                        <h3>{{ number_format($totalRankings) }}</h3>
                                        <p>จำนวน KPI (HDC)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="far fa-list-alt"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">รายละเอียด <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="small-box bg-success">
                                    <div class="inner">
                                        <h3>{{ number_format($passedRankings) }}</h3>
                                        <p>จำนวน KPI ผ่านเกณฑ์</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">รายละเอียด <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="small-box bg-danger">
                                    <div class="inner">
                                        <h3>{{ number_format($failedRankings) }}</h3>
                                        <p>จำนวน KPI ไม่ผ่านเกณฑ์</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-window-close"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">รายละเอียด <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>

                        <!-- Small Box Row 2 -->
                        <div class="row">
                            <div class="col-lg-4 col-6">
                                <div class="small-box bg-indigo">
                                    <div class="inner">
                                        <h3>{{ number_format($totalWeight, 2) }}</h3>
                                        <p>น้ำหนักคะแนนรวม (Weight)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-server"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">รายละเอียด <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-6">
                                <div class="small-box bg-warning">
                                    <div class="inner">
                                        <h3>{{ number_format($totalScore, 2) }}</h3>
                                        <p>คะแนนรวม (Score)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">รายละเอียด <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-lg-4 col-12">
                                <div class="small-box bg-teal">
                                    <div class="inner">
                                        <h3>{{ number_format($percentScore, 2) }} %</h3>
                                        <p>คะแนนรวม (ร้อยละ)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-chart-pie"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">รายละเอียด <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>

                        <!-- Table KPI -->
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="card card-success">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-list mr-1"></i> รายงาน KPI (HDC)</h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 5%">ลำดับ</th>
                                                    <th class="text-center" style="width: 60%">ชื่อตัวชี้วัด</th>
                                                    <th class="text-right" style="width: 8%">เป้าหมาย</th>
                                                    <th class="text-right" style="width: 8%">ผลงาน</th>
                                                    <th class="text-center" style="width: 8%">ร้อยละ</th>
                                                    <th class="text-center" style="width: 8%">Rank</th>
                                                    <th class="text-center" style="width: 3%"><i class="fas fa-search-plus"></i></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($rankings as $index => $ranking)
                                                    @php
                                                        $percent = $ranking->percent;
                                                        if ($percent >= $ranking->target_value) {
                                                            $badgeClass = 'bg-success';
                                                        } else {
                                                            $badgeClass = 'bg-danger';
                                                        }
                                                    @endphp
                                                    <tr>
                                                        <td class="text-center">{{ $rankings->firstItem() + $index }}</td>
                                                        <td>
                                                            <div>
                                                                <span class="badge bg-info" style="min-width: 50px; display: inline-block;">R{{ $ranking->ranking_code }}</span>
                                                                {{ $ranking->ranking_name }} 
                                                                <span class="badge bg-teal" style="min-width: 30px; display: inline-block;">{{ $ranking->weight }}</span>
                                                                <a href="{{ $ranking->hdc_link }}" target="_blank" class="badge badge-primary" style="min-width: 30px; display: inline-block;" title="HDC Link">
                                                                    <i class="fas fa-link"></i>
                                                                </a>
                                                            </div>
                                                        </td>
                                                        <td class="text-right text-bold ">
                                                            {{ number_format($ranking->total_target) }}
                                                        </td>
                                                        <td class="text-right text-bold">
                                                            {{ number_format($ranking->total_result) }}
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge {{ $badgeClass }}"
                                                                style="min-width: 50px; display: inline-block;">{{ number_format($percent, 2) }}%</span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-primary"
                                                                style="min-width: 50px; display: inline-block;">{{ $ranking->rank }}</span>
                                                        </td>
                                                        <td class="text-center"><a href="#" data-toggle="modal"
                                                                data-target="#modal-kpi-{{ $ranking->id }}"><i
                                                                    class="fas fa-search-plus text-primary"></i></a></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
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

            </div>
            </section>

            <!-- Modals Popup KPI -->
            @foreach ($rankings as $ranking)
                @if (isset($ranking->details))
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
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 8%">ลำดับ</th>
                                                    <th class="text-center" style="width: 33%">หน่วยบริการ</th>
                                                    <th class="text-right" style="width: 12%">เป้าหมาย</th>
                                                    <th class="text-right" style="width: 12%">ผลงาน</th>
                                                    <th class="text-center" style="width: 2%"></th>
                                                    <th class="text-center" style="width: 23%">ร้อยละ (%)</th>
                                                    <th class="text-center" style="width: 10%">Rank</th>
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
                                                        <td></td>
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
                                                        style="border-bottom-left-radius: 10px;">รวมทั้งหมด</td>
                                                    <td class="text-right text-dark">
                                                        {{ number_format($ranking->total_target) }}</td>
                                                    <td class="text-right text-dark">
                                                        {{ number_format($ranking->total_result) }}</td>
                                                    <td></td>
                                                    <td class="text-center text-primary">
                                                        {{ number_format($ranking->percent, 2) }}%</td>
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
