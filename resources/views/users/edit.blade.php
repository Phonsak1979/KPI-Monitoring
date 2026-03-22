@extends('layouts.template')

@section('title', 'การจัดการผู้ใช้งาน')

@section('content')
    <div class="container-fluid p-3">

        <div class="row justify-content-center">

            <div class="col-md-10">

                <div class="card card-warning">
                    <div class="card-header d-flex">
                        <h3 class="card-title"><i class="fas fa-user-edit mr-2"></i><b>แก้ไขข้อมูลผู้ใช้งาน</b></h3>
                    </div>

                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group">
                                <label for="name">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" id="name"
                                    value="{{ old('name', $user->name) }}" placeholder="ชื่อผู้ใช้งาน">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="email">อีเมล <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" id="email"
                                        value="{{ old('email', $user->email) }}" placeholder="example@gmail.com">
                                    @error('email')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                            </div>

                            <div class="form-group">
                                <label for="password">รหัสผ่านใหม่</label>
                                    <input type="password" name="password" class="form-control" id="password"
                                        placeholder="ปล่อยว่างไว้หากไม่ต้องการเปลี่ยนรหัสผ่าน">
                                    <small class="text-muted">หากไม่เปลี่ยนรหัส ให้ปล่อยช่องนี้ว่างไว้</small>
                                    @error('password')
                                        <br><span class="text-danger">{{ $message }}</span>
                                    @enderror
                            </div>

                            <div class="form-group">
                                <label for="role">สิทธิ์การใช้งาน <span class="text-danger">*</span></label>
                                    <select name="role" id="role" class="custom-select">
                                        <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User (ผู้ใช้งานทั่วไป)</option>
                                        <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin (ผู้ดูแลระบบ)</option>
                                    </select>
                                    @error('role')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                            </div>
                            
                            <div class="form-group">
                                <label for="status">สถานะ <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="custom-select">
                                        <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>ปกติ (Active)</option>
                                        <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>ระงับการใช้งาน (Inactive)</option>
                                    </select>
                                    @error('status')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> บันทึก</button>
                            <a href="{{ route('users.index') }}" class="btn btn-warning"><i class="fas fa-undo-alt"></i> กลับ</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
