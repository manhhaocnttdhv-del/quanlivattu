@extends('layouts.admin')
@section('title', 'Cảnh báo Tồn kho')
@section('header', 'Cảnh báo Tồn kho')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

        {{-- Bộ lọc kho --}}
        @if(auth()->user()->isAdminTong())
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" class="d-flex gap-2 align-items-end">
                    <div>
                        <label class="form-label small mb-1">Lọc theo kho</label>
                        <select name="warehouse_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">-- Tất cả kho --</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <a href="{{ route('inventory-alerts.index') }}" class="btn btn-sm btn-secondary">Xóa lọc</a>
                </form>
            </div>
        </div>
        @endif

        {{-- Bảng tồn kho dưới mức an toàn --}}
        <div class="card card-outline card-danger mb-4">
            <div class="card-header bg-danger bg-opacity-10">
                <h3 class="card-title text-danger">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Vật tư dưới mức tồn an toàn
                    <span class="badge bg-danger ms-2">{{ $lowStockItems->total() }}</span>
                </h3>
                <div class="card-tools">
                    <small class="text-muted">Mức tối thiểu mặc định: <strong>{{ $defaultMin }}</strong>
                        @can('Phân quyền người dùng')
                            (<a href="{{ route('settings.index') }}">Thay đổi</a>)
                        @endcan
                    </small>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-danger">
                        <tr>
                            <th>#</th>
                            <th>Kho</th>
                            <th class="text-start">Vật tư</th>
                            <th class="text-center">ĐVT</th>
                            <th class="text-center text-danger fw-bold">Tồn hiện tại</th>
                            <th class="text-center">Mức tối thiểu</th>
                            <th class="text-center">Thiếu</th>
                            <th class="text-center">Vị trí</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockItems as $item)
                        @php $minStock = $item->material->min_stock ?? $defaultMin; @endphp
                        <tr>
                            <td>{{ $lowStockItems->firstItem() + $loop->index }}</td>
                            <td><span class="badge text-bg-secondary">{{ $item->warehouse->name ?? 'N/A' }}</span></td>
                            <td class="fw-bold">{{ $item->material->name ?? 'N/A' }}</td>
                            <td class="text-center">{{ $item->material->unit->name ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge bg-danger fs-6">{{ (float)$item->stock }}</span>
                            </td>
                            <td class="text-center text-warning fw-bold">{{ $minStock }}</td>
                            <td class="text-center text-danger fw-bold">{{ (float)max(0, $minStock - $item->stock) }}</td>
                            <td class="text-center"><span class="badge text-bg-light text-dark">{{ $item->location ?? 'N/A' }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="bi bi-check-circle-fill text-success fs-3"></i>
                                <div class="text-muted mt-1">Tuyệt vời! Không có vật tư nào dưới mức an toàn.</div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($lowStockItems->hasPages())
            <div class="card-footer">{{ $lowStockItems->links() }}</div>
            @endif
        </div>

        {{-- Cảnh báo đang chờ xử lý --}}
        @if($pendingAlerts->count() > 0)
        <div class="card card-outline card-warning">
            <div class="card-header">
                <h3 class="card-title text-warning"><i class="bi bi-bell-fill"></i> Cảnh báo chờ xử lý <span class="badge bg-warning text-dark">{{ $pendingAlerts->count() }}</span></h3>
            </div>
            <div class="card-body p-0">
                <table class="table align-middle mb-0">
                    <thead class="table-warning">
                        <tr>
                            <th>Vật tư</th><th>Kho</th><th class="text-center">Tồn ghi nhận</th><th class="text-center">Tối thiểu</th><th>Ngày cảnh báo</th><th class="text-center">Xử lý</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingAlerts as $alert)
                        <tr>
                            <td class="fw-bold">{{ $alert->material->name ?? 'N/A' }}</td>
                            <td>{{ $alert->warehouse->name ?? 'N/A' }}</td>
                            <td class="text-center text-danger fw-bold">{{ $alert->current_stock }}</td>
                            <td class="text-center">{{ $alert->min_stock_level }}</td>
                            <td>{{ $alert->updated_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <form action="{{ route('inventory-alerts.resolve', $alert) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success" onclick="return confirm('Đánh dấu đã xử lý?')">
                                        <i class="bi bi-check"></i> Xử lý
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
