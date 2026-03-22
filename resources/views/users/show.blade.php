@extends('layouts.template')

@section('title', 'ข้อมูลผู้ใช้งาน')

@section('content')
    <div class="container-fluid p-3">

        <div class="row justify-content-center">

            <div class="col-md-10">

                <div class="card">
                    <div class="card-header bg-gradient-info">
                        <h3 class="card-title"><i class="fas fa-user-tag mr-2"></i><b>รายละเอียดผู้ใช้งาน</b></h3>
                    </div>

                    <div class="card-body">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <th style="width: 30%;">ชื่อ-นามสกุล</th>
                                    <td><i class="far fa-id-card mr-2"></i>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>E-mail</th>
                                    <td><i class="fas fa-envelope mr-2"></i><b class="text-primary">{{ $user->email }}</b></td>
                                </tr>
                                <tr>
                                    <th>สิทธิ์การใช้งาน</th>
                                    <td>
                                        @if($user->role === 'admin')
                                            <i class="fas fa-user-shield mr-2"></i><span class="badge bg-maroon" style="min-width: 100px; text-align: center;"><b>ADMIN</b></span>
                                        @else
                                            <i class="fas fa-user-shield mr-2"></i><span class="badge bg-info" style="min-width: 100px; text-align: center;"><b>USER</b></span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>สถานะ</th>
                                    <td>
                                        @if($user->status === 'active')
                                            <i class="fas fa-user-check mr-2"></i><span class="badge bg-success" style="min-width: 100px; text-align: center;">ใช้งานปกติ</span>
                                        @else
                                            <i class="fas fa-user-times mr-2"></i><span class="badge bg-danger" style="min-width: 100px; text-align: center;">ระงับการใช้งาน</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>วันที่ลงทะเบียน</th>
                                    <td>
                                        @if($user->created_at)
                                            @php
                                                $months = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
                                                $d = $user->created_at->format('j');
                                                $m = $months[$user->created_at->format('n')];
                                                $y = $user->created_at->format('Y') + 543;
                                            @endphp
                                            <i class="fas fa-calendar-alt mr-2"></i>{{ $d }} {{ $m }} {{ $y }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="card-footer">
                        <a href="{{ route('users.index') }}" class="btn btn-warning"><i class="fas fa-undo-alt"></i>
                            กลับ</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
