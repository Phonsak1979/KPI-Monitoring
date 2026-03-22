@extends('layouts.template')

@section('title', 'อำเภอ')

@section('content')
    <div class="container-fluid p-3">

        <div class="row justify-content-center">

            <div class="col-md-10">

                <div class="card">
                    <div class="card-header bg-gradient-warning">
                        <h3 class="card-title"><i class="fas fa-landmark mr-2"></i><b>แก้ไขอำเภอ</b></h3>
                    </div>
                    <form action="{{ route('districts.update', $district->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="district_code">รหัสอำเภอ <span class="text-danger">*</span></label>
                                <input type="text" name="district_code" class="form-control" id="district_code"
                                    placeholder="รหัสอำเภอ" value="{{ $district->district_code }}">
                                @error('district_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="district_name">ชื่ออำเภอ <span class="text-danger">*</span></label>
                                <input type="text" name="district_name" class="form-control" id="district_name"
                                    placeholder="ชื่ออำเภอ" value="{{ $district->district_name }}">
                                @error('district_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> บันทึก</button>
                            <a href="{{ session('district_url', route('districts.index')) }}" class="btn btn-warning"><i class="fas fa-undo-alt"></i> กลับ</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
