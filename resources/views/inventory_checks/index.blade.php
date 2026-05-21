@extends('layouts.admin')

@section('title', 'Kiểm kê kho')
@section('header', 'Danh sách Phiếu Kiểm kê')

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
                <h3 class="card-title">Danh sách Phiếu Kiểm kê</h3>
                <div class="card-tools">
                    <a href="{{ route('inventory-checks.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-list-check"></i> Tạo phiếu kiểm kê
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">ID</th>
                            <th>Ngày kiểm kê</th>
                            <th>Kho hàng</th>
                            <th>Người lập</th>
                            <th>Trạng thái</th>
                            <th style="width: 150px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($checks as $check)
                        <tr class="align-middle">
                            <td>#{{ $check->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($check->date)->format('d/m/Y') }}</td>
                            <td>{{ $check->warehouse->name ?? 'N/A' }}</td>
                            <td>{{ $check->user->name ?? 'N/A' }}</td>
                            <td>
                                @if($check->status == 'completed')
                                    <span class="badge text-bg-success">Đã xử lý</span>
                                @elseif($check->status == 'pending')
                                    <span class="badge text-bg-warning">Chờ xử lý</span>
                                @else
                                    <span class="badge text-bg-secondary">{{ $check->status }}</span>
                                @endif
                            </td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('inventory-checks.show', $check) }}" class="btn btn-sm btn-info text-white">Xem</a>
                                @if(auth()->check() && auth()->user()->hasRole(['Admin tổng', 'Admin kho']))
                                    @if($check->status === 'pending')
                                        <form action="{{ route('inventory-checks.approve', $check) }}" method="POST" class="d-inline" onsubmit="return confirm('Duyệt kết quả và TỰ ĐỘNG ĐIỀU CHỈNH KHO (sẽ sinh ra Phiếu nhập/xuất bù trừ)?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                                        </form>
                                        <form action="{{ route('inventory-checks.cancel', $check) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy phiếu này?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">Hủy</button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Chưa có dữ liệu phiếu kiểm kê</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                {{ $checks->links() }}
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
