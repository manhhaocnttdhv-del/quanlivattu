@extends('layouts.admin')

@section('title', 'Quản lý Phiếu xuất kho')
@section('header', 'Danh sách Phiếu xuất kho')

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
                <h3 class="card-title">Danh sách Phiếu xuất</h3>
                <div class="card-tools">
                    <a href="{{ route('inventory-exits.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Lập phiếu xuất
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">ID</th>
                            <th>Ngày xuất</th>
                            <th>Kho xuất</th>
                            <th>Khách hàng</th>
                            <th>Người lập</th>
                            <th>Trạng thái</th>
                            <th style="width: 150px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exits as $exit)
                        <tr class="align-middle">
                            <td>#{{ $exit->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($exit->date)->format('d/m/Y') }}</td>
                            <td>{{ $exit->warehouse->name ?? 'N/A' }}</td>
                            <td>{{ $exit->customer->name ?? 'N/A' }}</td>
                            <td>{{ $exit->user->name ?? 'N/A' }}</td>
                            <td>
                                @if($exit->status == 'completed')
                                    <span class="badge text-bg-success">Hoàn thành</span>
                                @elseif($exit->status == 'pending')
                                    <span class="badge text-bg-warning">Chờ xử lý</span>
                                @else
                                    <span class="badge text-bg-secondary">{{ $exit->status }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('inventory-exits.show', $exit) }}" class="btn btn-sm btn-info text-white">Xem</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Chưa có dữ liệu phiếu xuất</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                {{ $exits->links() }}
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
