@extends('layouts.template')

@section('title', 'หน่วยบริการ')

@section('content')
    <div class="container-fluid p-3">

        <div class="row justify-content-center">

            <div class="col-md-10">

                <div class="card card-success">
                    <div class="card-header d-flex">
                        <h3 class="card-title"><i class="fas fa-cog"></i> เพิ่มหน่วยบริการ</h3>
                    </div>

                    <form action="{{ route('hospitals.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="hospital_code">รหัสหน่วยบริการ</label>
                                <input type="text" name="hospital_code" class="form-control" id="hospital_code"
                                    value="{{ old('hospital_code') }}" placeholder="รหัสสถานบริการ 5 หลัก">
                                @error('hospital_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="hospital_name">ชื่อหน่วยบริการ</label>
                                <input type="text" name="hospital_name" class="form-control" id="hospital_name"
                                    value="{{ old('hospital_name') }}" placeholder="เช่น รพ.ดอนมดแดง /รพ.สต.เหล่าแดง">
                                @error('hospital_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="district_id">อำเภอ</label>
                                <select name="district_id" id="district_id" class="custom-select">
                                    <option value="">--เลือกอำเภอ--</option>
                                    @foreach ($districts as $district)
                                        <option value="{{ $district->id }}">{{ $district->district_name }}</option>
                                    @endforeach
                                </select>
                                @error('district_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> บันทึก</button>
                            <a href="{{ route('hospitals.index') }}" class="btn btn-warning"><i class="fas fa-undo-alt"></i>
                                กลับ</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
