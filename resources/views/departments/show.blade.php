@extends('layouts.template')

@section('title', 'กลุ่มงาน/ฝ่าย')

@section('content')
    <div class="container-fluid p-3">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card card-info">
                    <div class="card-header d-flex">
                        <h3 class="card-title"><i class="fas fa-cog"></i> แสดงกลุ่มงาน/ฝ่าย</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="department_id">ID</label>
                            <input type="text" class="form-control" id="department_id" value="{{ $department->id }}"
                                readonly>
                        </div>
                        <div class="form-group">
                            <label for="department_name">ชื่อกลุ่มงาน/ฝ่าย</label>
                            <input type="text" class="form-control" id="department_name"
                                value="{{ $department->department_name }}" readonly>
                        </div>
                    </div>
                    <div class="card-footer">
                        <a href="{{ session('department_url', route('departments.index')) }}" class="btn btn-warning"><i
                                class="fas fa-undo-alt"></i> กลับ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
