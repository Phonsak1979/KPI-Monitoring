@extends('layouts.template')

@section('title', 'ตัวชี้วัด')

@section('content')
    <div class="container-fluid p-3">

        <div class="row justify-content-center">

            <div class="col-md-10">

                <div class="card">
                    <div class="card-header bg-gradient-info">
                        <h3 class="card-title"><i class="fas fa-th-list mr-2"></i><b>รายละเอียดตัวชี้วัด</b></h3>
                    </div>
                    <div class="card-body">

                        <div class="form-group">
                            <label for="ranking_code">รหัสตัวชี้วัด</label>
                            <input type="text" class="form-control" id="ranking_code"
                                value="{{ $ranking->ranking_code }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="ranking_name">ชื่อตัวชี้วัด</label>
                            <input type="text" class="form-control" id="ranking_name"
                                value="{{ $ranking->ranking_name }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="department_id">หน่วยงาน</label>
                            <input type="text" class="form-control" id="department_id"
                                value="{{ $ranking->department->department_name }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="table_name">ชื่อตาราง</label>
                            <input type="text" class="form-control" id="table_name" value="{{ $ranking->table_name }}"
                                readonly>
                        </div>
                        <div class="form-group">
                            <label for="hdc_link">Link รายงานใน HDC</label>
                            <input type="text" class="form-control" id="hdc_link" value="{{ $ranking->hdc_link }}"
                                readonly>
                        </div>
                        <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="target_value">ค่าเป้าหมาย (ร้อยละ)</label>
                                    <input type="number" step="0.01" name="target_value" class="form-control"
                                        id="target_value" value="{{ $ranking->target_value }}" readonly>
                                    @error('target_value')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="weight">น้ำหนักคะแนน (Weight)</label>
                                    <input type="number" step="0.01" name="weight" class="form-control" id="weight"
                                        value="{{ $ranking->weight }}" readonly>
                                    @error('weight')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                        <div class="form-group">
                            <label>เกณฑ์คะแนน (5-0)</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- Score 5 -->
                                    <div class="mb-2">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text justify-content-center" style="background-color: #28a745; color: white; min-width: 50px;">5</span>
                                                <span class="input-group-text px-2">&ge;</span>
                                            </div>
                                            <input type="number" step="0.10" class="form-control" value="{{ $ranking->score_5 }}" readonly>
                                        </div>
                                    </div>
                                    <!-- Score 4 -->
                                    <div class="mb-2">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text justify-content-center" style="background-color: #9ACD32; color: #000; min-width: 50px;">4</span>
                                                <span class="input-group-text px-2">&ge;</span>
                                            </div>
                                            <input type="number" step="0.10" class="form-control" value="{{ $ranking->score_4 }}" readonly>
                                        </div>
                                    </div>
                                    <!-- Score 3 -->
                                    <div class="mb-2">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text justify-content-center" style="background-color: #FFEB3B; color: #000; min-width: 50px;">3</span>
                                                <span class="input-group-text px-2">&ge;</span>
                                            </div>
                                            <input type="number" step="0.10" class="form-control" value="{{ $ranking->score_3 }}" readonly>
                                        </div>
                                    </div>
                                    <!-- Score 2.5 -->
                                    <div class="mb-2">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text justify-content-center" style="background-color: #FFC107; color: #000; min-width: 50px;">2.5</span>
                                                <span class="input-group-text px-2">&ge;</span>
                                            </div>
                                            <input type="number" step="0.10" class="form-control" value="{{ $ranking->score_2_5 ?? '' }}" readonly>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <!-- Score 2 -->
                                    <div class="mb-2">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text justify-content-center" style="background-color: #FF8C00; color: white; min-width: 50px;">2</span>
                                                <span class="input-group-text px-2">&ge;</span>
                                            </div>
                                            <input type="number" step="0.10" class="form-control" value="{{ $ranking->score_2 }}" readonly>
                                        </div>
                                    </div>
                                    <!-- Score 1 -->
                                    <div class="mb-2">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text justify-content-center" style="background-color: #FF4500; color: white; min-width: 50px;">1</span>
                                                <span class="input-group-text px-2">
                                                    @if(($ranking->score_1_operator ?? '<') == '<') &lt; @else &ge; @endif
                                                </span>
                                            </div>
                                            <input type="number" step="0.10" class="form-control" value="{{ $ranking->score_1 }}" readonly>
                                        </div>
                                    </div>
                                    <!-- Score 0 -->
                                    <div class="mb-2">
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text justify-content-center" style="background-color: #dc3545; color: white; min-width: 50px;">0</span>
                                                <span class="input-group-text px-2">&lt;</span>
                                            </div>
                                            <input type="number" step="0.10" class="form-control" value="{{ $ranking->score_0 }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        <a href="{{ session('ranking_url', route('rankings.index')) }}" class="btn btn-warning"><i
                                class="fas fa-undo-alt"></i> กลับ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
