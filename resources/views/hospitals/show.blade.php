@extends('layouts.template')

@section('title', 'หน่วยบริการ')

@section('content')
    <div class="container-fluid p-3">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header bg-gradient-info">
                        <h3 class="card-title"><i class="fas fa-hospital-alt mr-2"></i><b>แสดงหน่วยบริการ</b></h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="hospital_id">ID</label>
                            <input type="text" class="form-control" id="hospital_id" value="{{ $hospital->id }}"
                                readonly>
                        </div>
                        <div class="form-group">
                            <label for="hospital_code">รหัสหน่วยบริการ</label>
                            <input type="text" class="form-control" id="hospital_code"
                                value="{{ $hospital->hospital_code }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="hospital_name">ชื่อหน่วยบริการ</label>
                            <input type="text" class="form-control" id="hospital_name"
                                value="{{ $hospital->hospital_name }}" readonly>
                        </div>
                        <div class="form-group">
                            <label for="district_name">อำเภอ</label>
                            <input type="text" class="form-control" id="district_name"
                                value="{{ $hospital->district->district_name }}" readonly>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ session('hospital_url', route('hospitals.index')) }}" class="btn btn-warning"><i
                                class="fas fa-undo-alt"></i> กลับ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
