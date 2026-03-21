@extends('layouts.template')

@section('title', 'กลุ่มงาน/ฝ่าย')

@section('content')
    <div class="container-fluid p-3">

        <div class="row justify-content-center">

            <div class="col-md-10">

                <div class="card">
                    <div class="card-header bg-gradient-success ">
                        <h3 class="card-title"><i class="fas fa-cog"></i> กลุ่มงาน/ฝ่าย</h3>

                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            @if(Auth::user()->role !== 'user')
                            <a href="{{ route('departments.create') }}" class="btn btn-outline-success text-bold"><i
                                    class="fas fa-plus"></i> เพิ่มกลุ่มงาน/ฝ่าย</a>
                            @else
                            <div></div>
                            @endif
                            <form action="{{ route('departments.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control border-success"
                                        placeholder="ค้นหากลุ่มงาน/ฝ่าย" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-success" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center">ลำดับ</th>
                                    <th class="text-center">ID</th>
                                    <th>กลุ่มงาน/ฝ่าย</th>
                                    @if(Auth::user()->role !== 'user')
                                    <th class="text-center">การจัดการ</th>
                                    @else
                                    <th class="text-center">ดูข้อมูล</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($departments as $department)
                                    <tr>
                                        <td class="text-center">{{ $departments->firstItem() + $loop->index }}</td>

                                        <td class="text-center"><span class="badge bg-teal"
                                                style="min-width: 50px; display: inline-block;">{{ $department->id }}</span>
                                        </td>
                                        <td>{{ $department->department_name }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('departments.show', $department->id) }}"
                                                class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                            @if(Auth::user()->role !== 'user')
                                            <a href="{{ route('departments.edit', $department->id) }}"
                                                class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('departments.destroy', $department->id) }}"
                                                method="POST" style="display:inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm delete-confirm"
                                                    data-toggle="tooltip" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="float-left ml-2">
                    รายการที่ {{ $departments->firstItem() }} - {{ $departments->lastItem() }} จาก
                    {{ $departments->total() }} รายการ
                </div>
                <div class="float-right mr-2">
                    {{ $departments->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
