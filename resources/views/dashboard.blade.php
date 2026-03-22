@extends('layouts.template')

@section('title', 'หน้าแรก')

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

                        <!-- small box Row -->
                        <div class="row">
                            {{-- small box 1 --}}
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="small-box bg-gradient-success">
                                    <div class="inner">
                                        <h3>{{ number_format($totalHospitals) }} <small>หน่วยบริการ</small></h3>
                                        <p>(HCODE)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-h-square"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">รายละเอียด <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            {{-- small box 2 --}}
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="small-box bg-gradient-info">
                                    <div class="inner">
                                        <h3>{{ number_format($totalRankings) }} <small>ตัวชี้วัด</small></h3>
                                        <p>(HDC-KPI)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="far fa-list-alt"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">รายละเอียด <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            {{-- small box 3 --}}
                            <div class="col-lg-4 col-md-4 col-sm-12">
                                <div class="small-box bg-indigo">
                                    <div class="inner">
                                        <h3>{{ number_format($totalWeight, 2) }} <small>คะแนน</small></h3>
                                        <p>(Weight)</p>
                                    </div>
                                    <div class="icon">
                                        <i class="fas fa-tasks"></i>
                                    </div>
                                    <a href="#" class="small-box-footer">รายละเอียด <i
                                            class="fas fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                        </div>

                        <!-- Bar Chart -->
                        <div class="row">
                            <div class="col-lg-8">
                                <div class="card">
                                    <div class="card-header bg-gradient-primary">
                                        <h3 class="card-title"><i class="fas fa-chart-bar mr-2"></i><b>ร้อยละผลงาน รายหน่วยบริการ</b> </h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart">
                                            <canvas id="barChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Donut Chart -->
                            <div class="col-lg-4">
                                <div class="card">
                                    <div class="card-header bg-gradient-primary">
                                        <h3 class="card-title"><i class="fas fa-chart-pie mr-2"></i><b>ผลงานรวมทุกหน่วย</b></h3>
                                        <div class="card-tools">
                                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart">
                                            <canvas id="donutChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Table KPI -->
                        <div class="row">
                            <div class="col-lg-12">

                                <div class="card">
                                    <div class="card-header bg-gradient-success">
                                        <h3 class="card-title"><i class="fas fa-list mr-2"></i><b>ร้อยละผลงาน รายหน่วยบริการ</b></h3>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-hover table-bordered">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 10%">ลำดับ</th>
                                                    <th class="text-center" style="width: 35%">หน่วยบริการ</th>
                                                    <th class="text-right" style="width: 15%">ผ่านเกณฑ์ (ข้อ)</th>
                                                    <th class="text-right" style="width: 15%">ไม่ผ่านเกณฑ์ (ข้อ)</th>
                                                    <th class="text-right" style="width: 10%">คะแนนรวม</th>
                                                    <th class="text-center" style="width: 10%">ร้อยละ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse ($paginatedHospitals as $index => $hospital)
                                                    <tr>
                                                        <td class="text-center">{{ $paginatedHospitals->firstItem() + $index }}</td>
                                                        <td>
                                                            <span class="badge badge-info" style="min-width: 60px; display: inline-block;">{{ $hospital['hospcode'] }}</span>
                                                            {{ $hospital['hospital_name'] }}
                                                        </td>
                                                        <td class="text-right text-bold text-success">{{ number_format($hospital['passed_kpi']) }}</td>
                                                        <td class="text-right text-bold text-danger">{{ number_format($hospital['failed_kpi']) }}</td>
                                                        <td class="text-right text-bold">{{ number_format($hospital['total_score'], 2) }}</td>
                                                        <td class="text-center">
                                                            @if($hospital['percent_score'] >= 80)
                                                                <span class="badge bg-success" style="min-width: 60px; display: inline-block;">{{ number_format($hospital['percent_score'], 2) }}%</span>
                                                            @elseif($hospital['percent_score'] >= 50)
                                                                <span class="badge bg-warning" style="min-width: 60px; display: inline-block;">{{ number_format($hospital['percent_score'], 2) }}%</span>
                                                            @else
                                                                <span class="badge bg-danger" style="min-width: 60px; display: inline-block;">{{ number_format($hospital['percent_score'], 2) }}%</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6" class="text-center text-muted py-4">ไม่พบข้อมูล</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                            <tfoot class="bg-light font-weight-bold">
                                                <tr style="font-size: 1.25rem;">
                                                    <td colspan="2" class="text-center" style="border-bottom-left-radius: 10px;">รวมทุกหน่วยบริการ</td>
                                                    <td class="text-right text-success">{{ number_format($passedRankings) }}</td>
                                                    <td class="text-right text-danger">{{ number_format($failedRankings) }}</td>
                                                    <td class="text-right text-dark">{{ number_format($totalScore, 2) }}</td>
                                                    <td class="text-center" style="border-bottom-right-radius: 10px;">
                                                        @if($percentScore >= 80)
                                                            <span class="badge bg-success" style="min-width: 60px; display: inline-block;">{{ number_format($percentScore, 2) }}%</span>
                                                        @elseif($percentScore >= 50)
                                                            <span class="badge bg-warning" style="min-width: 60px; display: inline-block;">{{ number_format($percentScore, 2) }}%</span>
                                                        @else
                                                            <span class="badge bg-danger" style="min-width: 60px; display: inline-block;">{{ number_format($percentScore, 2) }}%</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div class="ml-2">
                                        รายการที่ {{ $paginatedHospitals->firstItem() ?? 0 }} - {{ $paginatedHospitals->lastItem() ?? 0 }} จาก
                                        {{ $paginatedHospitals->total() }} รายการ
                                    </div>
                                    <div class="mr-2">
                                        {{ $paginatedHospitals->appends(request()->query())->links('pagination::bootstrap-4') }}
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

            </div>
            </section>
        </div>
    </div>
    </div>
@endsection

@section('JS')
<script>
    $(function () {
        var chartLabels = @json($chartLabels);
        var chartData = @json($chartData);
        var chartColors = @json($chartColors);
        var chartBorderColors = chartColors.map(function(c) {
            return c.replace(/0\.\d+\)/, '1)'); // Replace alpha to 1 for solid borders
        });

        var barChartCanvas = $('#barChart').get(0).getContext('2d');

        var barChartData = {
            labels: chartLabels,
            datasets: [
                {
                    label: 'ร้อยละความสำเร็จ',
                    backgroundColor: chartColors,
                    borderColor: chartBorderColors,
                    borderWidth: 2,
                    borderRadius: 10,
                    borderSkipped: false,
                    barPercentage: 0.65,
                    categoryPercentage: 0.9,
                    data: chartData
                }
            ]
        };

        var barChartOptions = {
            responsive: true,
            maintainAspectRatio: false,
            datasetFill: false,
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                        min: 0,
                        max: 100,
                        stepSize: 20,
                        fontFamily: "'Sarabun', sans-serif",
                        fontStyle: 'bold',
                        fontSize: 14
                    },
                    scaleLabel: {
                        display: false,
                        labelString: 'ร้อยละ (%)'
                    },
                    gridLines: {
                        color: "rgba(0,0,0,0.05)"
                    }
                }],
                xAxes: [{
                    ticks: {
                        autoSkip: false,
                        fontFamily: "'Sarabun', sans-serif",
                        fontStyle: 'bold',
                        fontSize: 14
                    },
                    gridLines: {
                        display: false
                    }
                }]
            },
            legend: {
                display: false
            },
            tooltips: {
                titleFontFamily: "'Sarabun', sans-serif",
                titleFontSize: 15,
                bodyFontFamily: "'Sarabun', sans-serif",
                bodyFontSize: 14,
                callbacks: {
                    label: function(tooltipItem, data) {
                        return tooltipItem.yLabel + '%';
                    }
                }
            }
        };

        new Chart(barChartCanvas, {
            type: 'bar',
            data: barChartData,
            options: barChartOptions
        });

        // ==========================================
        // Donut Chart
        // ==========================================
        var donutScore = @json(round($percentScore, 2));
        var donutRemaining = +(100 - donutScore).toFixed(2);
        
        var donutColor = donutScore >= 80 ? 'rgba(40, 167, 69, 0.8)' : (donutScore >= 50 ? 'rgba(255, 193, 7, 0.8)' : 'rgba(220, 53, 69, 0.8)');
        var donutBorderColor = donutScore >= 80 ? 'rgba(40, 167, 69, 1)' : (donutScore >= 50 ? 'rgba(255, 193, 7, 1)' : 'rgba(220, 53, 69, 1)');

        var donutChartCanvas = $('#donutChart').get(0).getContext('2d');
        var donutData = {
          labels: [
              'คะแนนรวม (ร้อยละ)',
              
          ],
          datasets: [
            {
              data: [donutScore, donutRemaining],
              backgroundColor : [donutColor, 'rgba(0,0,0,0.05)'],
              borderColor: [donutBorderColor, 'rgba(0,0,0,0.1)'],
              borderWidth: 2
            }
          ]
        }
        var donutOptions = {
          maintainAspectRatio : false,
          responsive : true,
          cutoutPercentage: 65,
          legend: {
            display: true,
            position: 'bottom',
            labels: {
                fontFamily: "'Sarabun', sans-serif",
                fontSize: 14,
                fontStyle: 'bold'
            }
          },
          tooltips: {
            titleFontFamily: "'Sarabun', sans-serif",
            bodyFontFamily: "'Sarabun', sans-serif",
            bodyFontSize: 14,
            callbacks: {
                label: function(tooltipItem, data) {
                    var label = data.labels[tooltipItem.index] || '';
                    if (label) {
                        label += ': ';
                    }
                    label += data.datasets[tooltipItem.datasetIndex].data[tooltipItem.index] + '%';
                    return label;
                }
            }
          }
        }
        
        new Chart(donutChartCanvas, {
          type: 'doughnut',
          data: donutData,
          options: donutOptions
        });
    });
</script>
@endsection
