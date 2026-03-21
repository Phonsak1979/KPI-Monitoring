@extends('layouts.template')

@section('title', 'อำเภอ')

@section('content')
    <div class="container-fluid p-3">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-gradient-info">
                        <h3 class="card-title"><i class="fas fa-cog"></i> แสดงอำเภอ</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="district_id">ID</label>
                            <input type="text" class="form-control" id="district_id" value="{{ $district->id }}"
                                readonly>
                        </div>
                        <div class="form-group">
                            <label for="district_code">รหัสอำเภอ</label>
                            <input type="text" class="form-control" id="district_code"
                                value="{{ $district->district_code }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="district_name">ชื่ออำเภอ</label>
                            <input type="text" class="form-control" id="district_name"
                                value="{{ $district->district_name }}" readonly>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ session('district_url', route('districts.index')) }}" class="btn btn-warning"><i
                                class="fas fa-undo-alt"></i> กลับ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
