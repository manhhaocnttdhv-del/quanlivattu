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
                <div class="card-tools d-flex gap-2">
                    @if(auth()->check() && auth()->user()->hasRole(['Admin tổng', 'Admin kho']))
                    <a href="{{ route('inventory-exits.export-excel') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                    </a>
                    <a href="{{ route('inventory-exits.export-pdf') }}" class="btn btn-sm btn-danger">
                        <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                    </a>
                    @endif
                    <a href="{{ route('inventory-exits.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Lập phiếu xuất
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <!-- Filter Section -->
            <div class="card-body border-bottom pb-3">
                <form method="GET" action="{{ route('inventory-exits.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Từ ngày</label>
                        <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Đến ngày</label>
                        <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Trạng thái</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Hoàn thành</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    @if(auth()->user()->role === 'Admin tổng')
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Kho</label>
                        <select name="warehouse_id" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Công trình</label>
                        <select name="project_id" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            @foreach($projects as $proj)
                                <option value="{{ $proj->id }}" {{ request('project_id') == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i> Lọc</button>
                        <a href="{{ route('inventory-exits.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-x-lg"></i></a>
                    </div>
                </form>
            </div>
            <!-- Table -->
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
                            <td>{{ $exit->project->name ?? 'N/A' }}</td>
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
                            <td class="d-flex gap-1">
                                <a href="{{ route('inventory-exits.show', $exit) }}" class="btn btn-sm btn-info text-white">Xem</a>
                                @if(auth()->check() && auth()->user()->hasRole(['Admin tổng', 'Admin kho']))
                                    @if($exit->status === 'pending')
                                        <form action="{{ route('inventory-exits.cancel', $exit) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy phiếu này?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">Hủy</button>
                                        </form>
                                    @elseif($exit->status === 'completed')
                                        <form action="{{ route('inventory-exits.cancel', $exit) }}" method="POST" class="d-inline" onsubmit="return confirm('CẢNH BÁO: Hủy phiếu đã duyệt sẽ TRẢ LẠI số lượng tồn kho đã xuất vào hệ thống. Chắc chắn?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">Hủy</button>
                                        </form>
                                    @endif
                                @endif
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
