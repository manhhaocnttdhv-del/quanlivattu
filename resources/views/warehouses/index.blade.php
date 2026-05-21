@extends('layouts.admin')

@section('title', 'Quản lý Kho hàng')
@section('header', 'Danh sách Kho hàng')

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
                    @can('Thêm / Sửa / Xóa kho')
                    <a href="{{ route('warehouses.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Thêm mới
                    </a>
                    @endcan
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Tên kho</th>
                            <th>Địa chỉ</th>
                            <th>Quản lý kho</th>
                            <th>Trạng thái</th>
                            @can('Thêm / Sửa / Xóa kho')
                            <th style="width: 150px">Thao tác</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouses as $warehouse)
                        <tr class="align-middle">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $warehouse->name }}</td>
                            <td>{{ $warehouse->address }}</td>
                            <td>{{ $warehouse->manager->name ?? 'Chưa rước' }}</td>
                            <td>
                                @if($warehouse->status == 'active')
                                    <span class="badge text-bg-success">Đang hoạt động</span>
                                @else
                                    <span class="badge text-bg-secondary">Ngừng hoạt động</span>
                                @endif
                            </td>
                            @can('Thêm / Sửa / Xóa kho')
                            <td>
                                <a href="{{ route('warehouses.edit', $warehouse) }}" class="btn btn-sm btn-warning">Sửa</a>
                                <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </td>
                            @endcan
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Chưa có dữ liệu</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                {{ $warehouses->links() }}
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
