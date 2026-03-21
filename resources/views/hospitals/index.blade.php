@extends('layouts.template')

@section('title', 'หน่วยบริการ')

@section('content')
    <div class="container-fluid p-3">

        <div class="row justify-content-center">

            <div class="col-md-10">

                <div class="card">
                    <div class="card-header bg-gradient-success ">
                        <h3 class="card-title"><i class="fas fa-cog"></i> หน่วยบริการ</h3>

                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            @if(Auth::user()->role !== 'user')
                            <a href="{{ route('hospitals.create') }}" class="btn btn-outline-success text-bold"><i
                                    class="fas fa-plus"></i> เพิ่มหน่วยบริการ</a>
                            @else
                            <div></div>
                            @endif
                            <form action="{{ route('hospitals.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control border-success"
                                        placeholder="ค้นหาหน่วยบริการ" value="{{ request('search') }}">
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
                                    <th class="text-center">รหัสหน่วยบริการ</th>
                                    <th>ชื่อหน่วยบริการ</th>
                                    <th>อำเภอ</th>
                                    @if(Auth::user()->role !== 'user')
                                    <th class="text-center">การจัดการ</th>
                                    @else
                                    <th class="text-center">ดูข้อมูล</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($hospitals as $hospital)
                                    <tr>
                                        <td class="text-center">{{ $hospitals->firstItem() + $loop->index }}</td>
                                        <td class="text-center"><span class="badge bg-teal"
                                                style="min-width: 50px; display: inline-block;">{{ $hospital->hospital_code }}</span>
                                        </td>
                                        <td>{{ $hospital->hospital_name }}</td>
                                        <td>{{ $hospital->district->district_name }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('hospitals.show', $hospital->id) }}"
                                                class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                            @if(Auth::user()->role !== 'user')
                                            <a href="{{ route('hospitals.edit', $hospital->id) }}"
                                                class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('hospitals.destroy', $hospital->id) }}" method="POST"
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
                    รายการที่ {{ $hospitals->firstItem() }} - {{ $hospitals->lastItem() }} จาก
                    {{ $hospitals->total() }} รายการ
                </div>
                <div class="float-right mr-2">
                    {{ $hospitals->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection
