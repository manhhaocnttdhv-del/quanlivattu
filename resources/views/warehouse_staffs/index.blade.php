@extends('layouts.admin')
@section('title', 'Nhân viên Kho')
@section('header', 'Danh sách Nhân viên Kho')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        {{-- Bộ lọc --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" class="row g-2 align-items-end">
                    @if(auth()->user()->isAdminTong())
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Kho</label>
                        <select name="warehouse_id" class="form-select form-select-sm">
                            <option value="">-- Tất cả kho --</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Trạng thái</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">Tất cả</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Đang làm</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Đã nghỉ</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Tên, SĐT, CCCD..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search"></i> Lọc</button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('warehouse-staffs.index') }}" class="btn btn-sm btn-secondary w-100">Xóa lọc</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Danh sách ({{ $staffs->total() }} nhân viên)</h3>
                <a href="{{ route('warehouse-staffs.create') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-person-plus-fill"></i> Thêm nhân viên
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Họ tên</th>
                                <th>Kho</th>
                                <th>Chức vụ</th>
                                <th>SĐT</th>
                                <th class="text-end">Lương cơ bản</th>
                                <th class="text-center">Trạng thái</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($staffs as $staff)
                            <tr>
                                <td>{{ $staffs->firstItem() + $loop->index }}</td>
                                <td>
                                    <a href="{{ route('warehouse-staffs.show', $staff) }}" class="fw-bold text-decoration-none">
                                        {{ $staff->full_name }}
                                    </a>
                                    @if($staff->id_card)
                                        <br><small class="text-muted">CCCD: {{ $staff->id_card }}</small>
                                    @endif
                                </td>
                                <td><span class="badge text-bg-info">{{ $staff->warehouse->name ?? 'N/A' }}</span></td>
                                <td>{{ $staff->position ?? '—' }}</td>
                                <td>{{ $staff->phone ?? '—' }}</td>
                                <td class="text-end fw-bold">{{ number_format($staff->base_salary) }} ₫</td>
                                <td class="text-center">
                                    @if($staff->status === 'active')
                                        <span class="badge text-bg-success">Đang làm</span>
                                    @else
                                        <span class="badge text-bg-secondary">Đã nghỉ</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('warehouse-staffs.show', $staff) }}" class="btn btn-xs btn-info" title="Chi tiết"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('warehouse-staffs.edit', $staff) }}" class="btn btn-xs btn-warning" title="Sửa"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('warehouse-staffs.destroy', $staff) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Xóa nhân viên {{ $staff->full_name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-danger" title="Xóa"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="8" class="text-center py-4 text-muted">Chưa có nhân viên nào</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">{{ $staffs->links() }}</div>
        </div>
    </div>
</div>
@endsection
