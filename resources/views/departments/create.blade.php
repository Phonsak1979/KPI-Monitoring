@extends('layouts.template')

@section('title', 'กลุ่มงาน/ฝ่าย')

@section('content')
    <div class="container-fluid p-3">

        <div class="row justify-content-center">

            <div class="col-md-10">

                <div class="card">
                    <div class="card-header bg-gradient-success">
                        <h3 class="card-title"><i class="fas fa-hospital-user mr-2"></i><b>เพิ่มกลุ่มงาน/ฝ่าย</b></h3>
                    </div>

                    <form action="{{ route('departments.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            <div class="form-group">
                                <label for="department_name">ชื่อกลุ่มงาน/ฝ่าย <span class="text-danger">*</span></label>
                                <input type="text" name="department_name" class="form-control" id="department_name"
                                    placeholder="ชื่อกลุ่มงาน/ฝ่าย เช่น กลุ่มงานสุขภาพดิจิทัล">
                                @error('department_name')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> บันทึก</button>
                            <a href="{{ route('departments.index') }}" class="btn btn-warning"><i
                                    class="fas fa-undo-alt"></i> กลับ</a>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
@endsection
