@extends('layouts.template')

@section('title', 'อำเภอ')

@section('content')
    <div class="container-fluid p-3">

        <div class="row justify-content-center">

            <div class="col-md-10">

                <div class="card">
                    <div class="card-header bg-gradient-success ">
                        <h3 class="card-title"><i class="fas fa-landmark mr-2"></i><b>อำเภอ</b></h3>

                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            @if(Auth::user()->role !== 'user')
                            <a href="{{ route('districts.create') }}" class="btn btn-outline-success text-bold"><i
                                    class="fas fa-plus"></i> เพิ่มอำเภอ</a>
                            @else
                            <div></div>
                            @endif
                            <form action="{{ route('districts.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control border-success"
                                        placeholder="ค้นหาอำเภอ" value="{{ request('search') }}">
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
                                    <th class="text-center">รหัสอำเภอ</th>
                                    <th>ชื่ออำเภอ</th>
                                    @if(Auth::user()->role !== 'user')
                                    <th class="text-center">การจัดการ</th>
                                    @else
                                    <th class="text-center">ดูข้อมูล</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($districts as $district)
                                    <tr>
                                        <td class="text-center">{{ $districts->firstItem() + $loop->index }}</td>
                                        <td class="text-center"><span class="badge bg-teal"
                                                style="min-width: 50px; display: inline-block;">{{ $district->district_code }}</span>
                                        </td>
                                        <td>{{ $district->district_name }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('districts.show', $district->id) }}"
                                                class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                            @if(Auth::user()->role !== 'user')
                                            <a href="{{ route('districts.edit', $district->id) }}"
                                                class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('districts.destroy', $district->id) }}" method="POST"
                                                style="display:inline-block">
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
                    รายการที่ {{ $districts->firstItem() }} - {{ $districts->lastItem() }} จาก
                    {{ $districts->total() }} รายการ
                </div>
                <div class="float-right mr-2">
                    {{ $districts->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
