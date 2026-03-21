@extends('layouts.template')

@section('title', 'อำเภอ')

@section('content')
    <div class="container-fluid p-3">

        <div class="row justify-content-center">

            <div class="col-md-10">

                <div class="card">
                    <div class="card-header bg-gradient-success">
                        <h3 class="card-title"><i class="fas fa-cog"></i> เพิ่มอำเภอ</h3>
                    </div>
                    <form action="{{ route('districts.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="district_code">รหัสอำเภอ</label>
                                <input type="text" name="district_code" class="form-control" id="district_code"
                                    placeholder="รหัสอำเภอ 4 หลัก">
                                @error('district_code')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="district_name">ชื่ออำเภอ</label>
                                <input type="text" name="district_name" class="form-control" id="district_name"
                                    placeholder="ชื่ออำเภอ เช่น ดอนมดแดง">
                                @error('district_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> บันทึก</button>
                            <a href="{{ route('districts.index') }}" class="btn btn-warning"><i
                                    class="fas fa-undo-alt"></i> กลับ</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
