@extends('layouts.template')

@section('title', 'ตัวชี้วัด')

@section('content')
    <div class="container-fluid p-3">

        <div class="row justify-content-center">

            <div class="col-md-10">

                <div class="card card-warning">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-cog"></i> แก้ไขตัวชี้วัด</h3>
                    </div>
                    <form action="{{ route('rankings.update', $ranking->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="ranking_code">รหัสตัวชี้วัด</label>
                                <input type="text" name="ranking_code" class="form-control" id="ranking_code"
                                    placeholder="Ranking Code" value="{{ $ranking->ranking_code }}">
                                @error('ranking_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="ranking_name">ชื่อตัวชี้วัด</label>
                                <input type="text" name="ranking_name" class="form-control" id="ranking_name"
                                    placeholder="Ranking Name" value="{{ $ranking->ranking_name }}">
                                @error('ranking_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="department_id">กลุ่มงาน/ฝ่าย</label>
                                <select name="department_id" class="custom-select" id="department_id">
                                    <option value="">--เลือกกลุ่มงาน/ฝ่าย--</option>
                                    @foreach ($departments as $department)
                                        <option value="{{ $department->id }}"
                                            {{ $department->id == $ranking->department_id ? 'selected' : '' }}>
                                            {{ $department->department_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="table_name">ชื่อตาราง</label>
                                <input type="text" name="table_name" class="form-control" id="table_name"
                                    placeholder="Table Name" value="{{ $ranking->table_name }}">
                                @error('table_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="hdc_link">HDC Link</label>
                                <input type="text" name="hdc_link" class="form-control" id="hdc_link"
                                    placeholder="ใส่ URL : Link ไปที่หน้ารายงาน KPI ของ HDC"
                                    value="{{ $ranking->hdc_link }}">
                                @error('hdc_link')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label for="target_value">ค่าเป้าหมาย (ร้อยละ)</label>
                                    <input type="number" step="0.10" name="target_value" class="form-control"
                                        id="target_value" value="{{ $ranking->target_value }}">
                                    @error('target_value')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="form-group col-md-6">
                                    <label for="weight">น้ำหนักคะแนน (Weight)</label>
                                    <input type="number" step="0.10" name="weight" class="form-control" id="weight"
                                        value="{{ $ranking->weight }}">
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
                                                <input type="number" step="0.10" name="score_5" class="form-control"
                                                    value="{{ $ranking->score_5 }}">
                                            </div>
                                        </div>
                                        <!-- Score 4 -->
                                        <div class="mb-2">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text justify-content-center" style="background-color: #9ACD32; color: #000; min-width: 50px;">4</span>
                                                    <span class="input-group-text px-2">&ge;</span>
                                                </div>
                                                <input type="number" step="0.10" name="score_4" class="form-control"
                                                    value="{{ $ranking->score_4 }}">
                                            </div>
                                        </div>
                                        <!-- Score 3 -->
                                        <div class="mb-2">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text justify-content-center" style="background-color: #FFEB3B; color: #000; min-width: 50px;">3</span>
                                                    <span class="input-group-text px-2">&ge;</span>
                                                </div>
                                                <input type="number" step="0.10" name="score_3" class="form-control"
                                                    value="{{ $ranking->score_3 }}">
                                            </div>
                                        </div>
                                        <!-- Score 2.5 -->
                                        <div class="mb-2">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text justify-content-center" style="background-color: #FFC107; color: #000; min-width: 50px;">2.5</span>
                                                    <span class="input-group-text px-2">&ge;</span>
                                                </div>
                                                <input type="number" step="0.10" name="score_2_5" class="form-control"
                                                    value="{{ $ranking->score_2_5 ?? '' }}">
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
                                                <input type="number" step="0.10" name="score_2" class="form-control"
                                                    value="{{ $ranking->score_2 }}">
                                            </div>
                                        </div>
                                        <!-- Score 1 -->
                                        <div class="mb-2">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text justify-content-center" style="background-color: #FF4500; color: white; min-width: 50px;">1</span>
                                                    <select name="score_1_operator" class="custom-select" style="width: auto; flex: none; border-radius: 0;">
                                                        <option value="<" {{ ($ranking->score_1_operator ?? '<') == '<' ? 'selected' : '' }}>&lt;</option>
                                                        <option value=">=" {{ ($ranking->score_1_operator ?? '>=') == '>=' ? 'selected' : '' }}>&ge;</option>
                                                    </select>
                                                </div>
                                                <input type="number" step="0.10" name="score_1" class="form-control"
                                                    value="{{ $ranking->score_1 }}">
                                            </div>
                                        </div>
                                        <!-- Score 0 -->
                                        <div class="mb-2">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text justify-content-center" style="background-color: #dc3545; color: white; min-width: 50px;">0</span>
                                                    <span class="input-group-text px-2">&lt;</span>
                                                </div>
                                                <input type="number" step="0.10" name="score_0" class="form-control"
                                                    value="{{ $ranking->score_0 }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> บันทึก</button>
                            <a href="{{ session('ranking_url', route('rankings.index')) }}" class="btn btn-warning"><i
                                    class="fas fa-undo-alt"></i> กลับ</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
