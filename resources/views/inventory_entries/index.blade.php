@extends('layouts.admin')

@section('title', 'Quản lý Phiếu nhập kho')
@section('header', 'Danh sách Phiếu nhập kho')

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
                <h3 class="card-title">Danh sách Phiếu nhập</h3>
                <div class="card-tools d-flex gap-2">
                    <a href="{{ route('inventory-entries.export-excel') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                    </a>
                    <a href="{{ route('inventory-entries.export-pdf') }}" class="btn btn-sm btn-danger">
                        <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                    </a>
                    <a href="{{ route('inventory-entries.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Lập phiếu nhập
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">ID</th>
                            <th>Ngày nhập</th>
                            <th>Kho hàng</th>
                            <th>Nhà cung cấp</th>
                            <th>Người lập</th>
                            <th>Trạng thái</th>
                            <th style="width: 150px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                        <tr class="align-middle">
                            <td>#{{ $entry->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($entry->date)->format('d/m/Y') }}</td>
                            <td>{{ $entry->warehouse->name ?? 'N/A' }}</td>
                            <td>{{ $entry->supplier->name ?? 'N/A' }}</td>
                            <td>{{ $entry->user->name ?? 'N/A' }}</td>
                            <td>
                                @if($entry->status == 'completed')
                                    <span class="badge text-bg-success">Hoàn thành</span>
                                @elseif($entry->status == 'pending')
                                    <span class="badge text-bg-warning">Chờ xử lý</span>
                                @else
                                    <span class="badge text-bg-secondary">{{ $entry->status }}</span>
                                @endif
                            </td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('inventory-entries.show', $entry) }}" class="btn btn-sm btn-info text-white">Xem</a>
                                @if($entry->status === 'pending')
                                    <form action="{{ route('inventory-entries.approve', $entry) }}" method="POST" class="d-inline" onsubmit="return confirm('Duyệt phiếu này và cộng số lượng vào tồn kho?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                                    </form>
                                    <form action="{{ route('inventory-entries.cancel', $entry) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy phiếu này?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Hủy</button>
                                    </form>
                                @elseif($entry->status === 'completed')
                                    <form action="{{ route('inventory-entries.cancel', $entry) }}" method="POST" class="d-inline" onsubmit="return confirm('CẢNH BÁO: Hủy phiếu đã duyệt sẽ trừ lại hệ thống số lượng tồn kho tương ứng. Chắc chắn?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">Hủy</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Chưa có dữ liệu phiếu nhập</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                {{ $entries->links() }}
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
