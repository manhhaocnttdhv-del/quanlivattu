@extends('layouts.admin')

@section('title', 'Quản lý Đơn vị tính')
@section('header', 'Danh sách Đơn vị tính')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Danh sách</h3>
                <div class="card-tools">
                    @if(auth()->check() && (auth()->user()->role === 'Admin tổng' || auth()->user()->role === 'Admin kho'))
                    <a href="{{ route('units.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Thêm mới
                    </a>
                    @endif
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Tên đơn vị tính</th>
                            @if(auth()->check() && (auth()->user()->role === 'Admin tổng' || auth()->user()->role === 'Admin kho'))
                            <th style="width: 150px">Thao tác</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $unit)
                        <tr class="align-middle">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $unit->name }}</td>
                            @if(auth()->check() && (auth()->user()->role === 'Admin tổng' || auth()->user()->role === 'Admin kho'))
                            <td>
                                <a href="{{ route('units.edit', $unit) }}" class="btn btn-sm btn-warning">Sửa</a>
                                <form action="{{ route('units.destroy', $unit) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center">Chưa có dữ liệu</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                {{ $units->links() }}
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
