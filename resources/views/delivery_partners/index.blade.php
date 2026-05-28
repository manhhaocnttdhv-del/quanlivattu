@extends('layouts.admin')

@section('title', 'Quản lý Phương tiện & Đối tác Vận chuyển')
@section('header', 'Phương tiện & Đối tác Vận chuyển')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline mb-4">
            <div class="card-header d-flex align-items-center justify-content-between">
                <h3 class="card-title fw-bold">Danh sách Phương tiện & Đối tác</h3>
                @can('Quản lý đối tác vận chuyển')
                <a href="{{ route('delivery-partners.create') }}" class="btn btn-primary btn-sm ms-auto">
                    <i class="bi bi-plus-circle me-1"></i> Thêm mới
                </a>
                @endcan
            </div>
            <div class="card-body">
                <!-- Filters -->
                <form action="{{ route('delivery-partners.index') }}" method="GET" class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-search"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="Tìm tên, biển số, tài xế, sđt..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="">-- Tất cả loại --</option>
                            <option value="internal" {{ request('type') == 'internal' ? 'selected' : '' }}>Phương tiện nội bộ</option>
                            <option value="external" {{ request('type') == 'external' ? 'selected' : '' }}>Đối tác bên ngoài</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-secondary w-100"><i class="bi bi-funnel me-1"></i> Lọc</button>
                    </div>
                </form>

                <!-- Data Table -->
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Tên / Phương tiện</th>
                                <th>Loại</th>
                                <th>Biển số xe</th>
                                <th>Tài xế / SĐT</th>
                                <th>Người liên hệ</th>
                                <th>Trạng thái</th>
                                <th>Ghi chú</th>
                                @can('Quản lý đối tác vận chuyển')
                                <th class="text-center" style="width: 150px;">Thao tác</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($partners as $index => $partner)
                            <tr>
                                <td>{{ $partners->firstItem() + $index }}</td>
                                <td class="fw-bold">{{ $partner->name }}</td>
                                <td>
                                    @if($partner->type == 'internal')
                                        <span class="badge text-bg-primary"><i class="bi bi-truck me-1"></i> Nội bộ</span>
                                    @else
                                        <span class="badge text-bg-info"><i class="bi bi-building me-1"></i> Đối tác ngoài</span>
                                    @endif
                                </td>
                                <td>
                                    @if($partner->license_plate)
                                        <span class="badge text-bg-dark font-monospace">{{ $partner->license_plate }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($partner->driver_name)
                                        <div><strong>{{ $partner->driver_name }}</strong></div>
                                        <small class="text-muted"><i class="bi bi-telephone"></i> {{ $partner->driver_phone ?? 'N/A' }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($partner->contact_name)
                                        <div><strong>{{ $partner->contact_name }}</strong></div>
                                        <small class="text-muted"><i class="bi bi-telephone"></i> {{ $partner->contact_phone ?? 'N/A' }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($partner->status == 'active')
                                        <span class="badge text-bg-success"><i class="bi bi-check-circle me-1"></i> Hoạt động</span>
                                    @else
                                        <span class="badge text-bg-danger"><i class="bi bi-x-circle me-1"></i> Tạm ngưng</span>
                                    @endif
                                </td>
                                <td><small class="text-muted">{{ Str::limit($partner->note, 40) }}</small></td>
                                @can('Quản lý đối tác vận chuyển')
                                <td class="text-center">
                                    <a href="{{ route('delivery-partners.edit', $partner) }}" class="btn btn-sm btn-outline-warning me-1">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('delivery-partners.destroy', $partner) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phương tiện/đối tác này không?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                                @endcan
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox fs-3 d-block mb-2"></i> Không tìm thấy phương tiện hoặc đối tác vận chuyển nào.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-end mt-3">
                    {{ $partners->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
