@extends('layouts.template')

@section('title', 'ตัวชี้วัด')

@section('content')
    <div class="container-fluid p-3">
        <div class="row justify-content-center">

            <div class="col-md-10">

                <div class="card card-success">
                    <div class="card-header d-flex">
                        <h3 class="card-title"><i class="fas fa-cog"></i> ตัวชี้วัด</h3>

                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-3">
                            <a href="{{ route('rankings.create') }}" class="btn btn-outline-success text-bold"><i
                                    class="fas fa-plus"></i> เพิ่มตัวชี้วัด</a>
                            <form action="{{ route('rankings.index') }}" method="GET">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control border-success"
                                        placeholder="ค้นหาตัวชี้วัด" value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-success" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Table --}}
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 5%">ลำดับ</th>
                                    <th class="text-center" style="width: 55%">ชื่อตัวชี้วัด</th>
                                    <th style="width: 20%">ชื่อตาราง</th>
                                    <th class="text-center" style="width: 20%">การจัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rankings as $ranking)
                                    <tr>
                                        <td class="text-center">{{ $rankings->firstItem() + $loop->index }}</td>
                                        <td><span class="badge badge-info"
                                                style="min-width: 50px; display: inline-block;">R{{ $ranking->ranking_code }}</span>
                                            {{ $ranking->ranking_name }}
                                        </td>
                                        <td>
                                            @if (!empty($ranking->hdc_link))
                                                <a href="{{ $ranking->hdc_link }}" target="_blank"
                                                    class="badge badge-primary" title="HDC Link">
                                                    <i class="fas fa-link mr-1"></i>{{ $ranking->table_name }}</a>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('rankings.show', $ranking->id) }}"
                                                class="btn btn-info btn-sm"><i class="fas fa-eye"></i></a>
                                            <a href="{{ route('rankings.edit', $ranking->id) }}"
                                                class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                            <form action="{{ route('rankings.destroy', $ranking->id) }}" method="POST"
                                                style="display:inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-danger btn-sm delete-confirm"
                                                    data-toggle="tooltip" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="ml-2">
                        รายการที่ {{ $rankings->firstItem() ?? 0 }} - {{ $rankings->lastItem() ?? 0 }} จาก
                        {{ $rankings->total() }} รายการ
                    </div>
                    <div class="mr-2">
                        {{ $rankings->appends(request()->query())->links('pagination::bootstrap-4') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
