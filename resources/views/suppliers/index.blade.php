@extends('layouts.admin')

@section('title', 'Quản lý Nhà cung cấp')
@section('header', 'Danh sách Nhà cung cấp')

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
                    <a href="{{ route('suppliers.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Thêm mới
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Tên nhà cung cấp</th>
                            <th>Số điện thoại</th>
                            <th>Địa chỉ</th>
                            <th style="width: 150px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                        <tr class="align-middle">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $supplier->name }}</td>
                            <td>{{ $supplier->phone ?? 'N/A' }}</td>
                            <td>{{ $supplier->address ?? 'N/A' }}</td>
                            <td>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-sm btn-warning">Sửa</a>
                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">Chưa có dữ liệu</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                {{ $suppliers->links() }}
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
