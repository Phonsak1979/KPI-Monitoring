@extends('layouts.template')

@section('title', 'ผู้ใช้งาน')

@section('CSS')
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
    <style>
        .toggle-handle {
            background-color: white;
        }

        .toggle.ios,
        .toggle-on.ios,
        .toggle-off.ios {
            border-radius: 20px;
        }

        .toggle.ios .toggle-handle {
            border-radius: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid p-3">

        <div class="row justify-content-center">

            <div class="col-md-10">

                <div class="card">
                    <div class="card-header bg-gradient-success ">
                        <h3 class="card-title"><i class="fas fa-user-cog mr-2"></i><b>จัดการผู้ใช้งาน</b></h3>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            @if (Auth::user()->role !== 'user')
                                <a href="{{ route('users.create') }}" class="btn btn-outline-success text-bold"><i
                                        class="fas fa-plus"></i> เพิ่มผู้ใช้งาน</a>
                            @else
                                <div></div>
                            @endif
                            <form action="{{ route('users.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control border-success"
                                        placeholder="ค้นหาชื่อ-สกุล / E-mail" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-success" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 9%">ลำดับ</th>
                                        <th class="text-center" style="width: 23%">ชื่อ-นามสกุล</th>
                                        <th class="text-center" style="width: 23%">E-Mail</th>
                                        <th class="text-center" style="width: 15%">สิทธิ์การใช้งาน</th>
                                        <th class="text-center" style="width: 15%">สถานะ</th>
                                        @if (Auth::user()->role !== 'user')
                                            <th class="text-center" style="width: 15%">การจัดการ</th>
                                        @else
                                            <th class="text-center" style="width: 15%">ดูข้อมูล</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr>
                                            <td class="text-center">{{ $users->firstItem() + $loop->index }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td class="text-primary text-bold">{{ $user->email }}</td>
                                            <td class="text-center">
                                                @if ($user->role === 'admin')
                                                    <span class="badge bg-maroon" style="min-width: 50px;">ADMIN</span>
                                                @else
                                                    <span class="badge bg-info" style="min-width: 50px;">USER</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if (Auth::user()->role !== 'user' && $user->id !== 1)
                                                    <input data-id="{{ $user->id }}" class="toggle-class"
                                                        type="checkbox" data-onstyle="success" data-offstyle="warning"
                                                        data-style="ios" data-toggle="toggle" data-on="ใช้งาน"
                                                        data-off="ระงับ" data-width="100" data-height="20"
                                                        {{ $user->status == 'active' ? 'checked' : '' }}>
                                                @else
                                                    @if ($user->status === 'active')
                                                        <span class="badge bg-success"
                                                            style="min-width: 100px;">ใช้งานปกติ</span>
                                                    @else
                                                        <span class="badge bg-danger"
                                                            style="min-width: 100px;">ระงับการใช้</span>
                                                    @endif
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('users.show', $user->id) }}"
                                                    class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                                @if (Auth::user()->role !== 'user')
                                                    @if ($user->id === 1 && Auth::id() !== 1)
                                                        <button type="button" class="btn btn-warning btn-sm" disabled
                                                            data-toggle="tooltip" title="แก้ไขได้เฉพาะเจ้าของบัญชีเท่านั้น">
                                                            <i class="fas fa-ban"></i>
                                                        </button>
                                                    @else
                                                        <a href="{{ route('users.edit', $user->id) }}"
                                                            class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                                    @endif
                                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                        style="display:inline-block">
                                                        @csrf
                                                        @method('DELETE')
                                                        @if ($user->id === 1)
                                                            <button type="button" class="btn btn-danger btn-sm" disabled
                                                                data-toggle="tooltip" title="ผู้ดูแลระบบหลัก">
                                                                <i class="fas fa-ban"></i>
                                                            </button>
                                                        @else
                                                            <button type="button"
                                                                class="btn btn-danger btn-sm delete-confirm"
                                                                data-toggle="tooltip" title="ลบข้อมูล">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        @endif
                                                    </form>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="float-left ml-2">
                    รายการที่ {{ $users->firstItem() ?? 0 }} - {{ $users->lastItem() ?? 0 }} จาก
                    {{ $users->total() }} รายการ
                </div>
                <div class="float-right mr-2">
                    {{ $users->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('JS')
    <script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
    <script>
        $(function() {
            $('.toggle-class').change(function() {
                var status = $(this).prop('checked') == true ? 'active' : 'inactive';
                var user_id = $(this).data('id');
                var toggleEvent = $(this);

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: '{{ route('users.changeStatus') }}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'status': status,
                        'user_id': user_id
                    },
                    success: function(data) {
                        Toast.fire({
                            icon: 'success',
                            title: data.message
                        });
                    },
                    error: function(xhr) {
                        Toast.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาดในการอัปเดตสถานะ'
                        });
                        // Revert the toggle on failure without triggering change event
                        setTimeout(function() {
                            toggleEvent.prop('checked', !toggleEvent.prop('checked'))
                                .bootstrapToggle('destroy').bootstrapToggle();
                        }, 500);
                    }
                });
            });
        });
    </script>
@endsection
