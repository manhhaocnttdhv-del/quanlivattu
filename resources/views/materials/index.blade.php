@extends('layouts.admin')

@section('title', 'Quản lý Vật tư')
@section('header', 'Danh sách Vật tư')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Danh sách</h3>
                <div class="card-tools d-flex gap-2 align-items-center">
                    @if(auth()->check() && (auth()->user()->role === 'Admin tổng' || auth()->user()->role === 'Admin kho'))
                    <a href="{{ route('materials.template') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-file-earmark-arrow-down"></i> Tải file mẫu
                    </a>
                    <form action="{{ route('materials.import') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-1 mb-0" onsubmit="if(!this.file.value) { alert('Vui lòng chọn file Excel!'); return false; }">
                        @csrf
                        <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx, .xls, .csv" required style="max-width: 200px;">
                        <button type="submit" class="btn btn-sm btn-info text-white"><i class="bi bi-upload"></i> Import</button>
                    </form>
                    <a href="{{ route('materials.export') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-download"></i> Export
                    </a>
                    <a href="{{ route('materials.create') }}?kho={{ $selectedWarehouseId }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Thêm mới
                    </a>
                    @endif
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card-body border-bottom pb-3">
                <form method="GET" action="{{ route('materials.index') }}" class="row g-2 align-items-end">
                    @if(auth()->user()->role === 'Admin tổng')
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Kho hàng</label>
                        <select name="kho" class="form-select form-select-sm" onchange="this.form.submit()">
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ $selectedWarehouseId == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-2">
                        <label class="form-label small mb-1">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Tên vật tư..." value="{{ request('search') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label small mb-1">Đơn vị tính</label>
                        <select name="unit_id" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Nhóm vật tư</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @foreach($cat->children as $child)
                                    <option value="{{ $child->id }}" {{ request('category_id') == $child->id ? 'selected' : '' }}>&nbsp;&nbsp;└ {{ $child->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Trạng thái tồn</label>
                        <select name="stock_status" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            <option value="below_min" {{ request('stock_status') == 'below_min' ? 'selected' : '' }}>Dưới tối thiểu</option>
                            <option value="above_max" {{ request('stock_status') == 'above_max' ? 'selected' : '' }}>Trên tối đa</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-search"></i> Lọc
                        </button>
                        <a href="{{ route('materials.index') }}?kho={{ $selectedWarehouseId }}" class="btn btn-sm btn-outline-secondary text-nowrap">
                            <i class="bi bi-x-lg"></i> Xóa lọc
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Tên vật tư</th>
                            <th>Nhóm</th>
                            <th>Đơn vị tính</th>
                            @if(auth()->user()->role !== 'Admin tổng')
                            <th class="text-end" style="min-width: 100px;">Tồn kho</th>
                            <th class="text-end">Giá nhập</th>
                            <th class="text-end">Giá bán</th>
                            <th class="text-end">Lợi nhuận</th>
                            @endif
                            <th>Mô tả</th>
                            @if(auth()->check() && (auth()->user()->role === 'Admin tổng' || auth()->user()->role === 'Admin kho'))
                            <th style="width: 220px">Thao tác</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materials as $material)
                        <tr class="align-middle">
                            <td>{{ $loop->iteration + ($materials->currentPage() - 1) * $materials->perPage() }}</td>
                            <td>{{ $material->name }}</td>
                            <td>
                                @if($material->category)
                                    <span class="badge text-bg-secondary">{{ $material->category->full_name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $material->unit->name ?? 'N/A' }}</td>
                            
                            @php
                                $stockRecord = $material->warehouseStocks->first();
                                $stock = $stockRecord ? $stockRecord->stock : 0;
                                $location = $stockRecord ? $stockRecord->location : '';
                                $costPrice = $stockRecord ? $stockRecord->cost_price : 0;
                                $sellingPrice = $stockRecord ? $stockRecord->selling_price : 0;
                                $profit = $sellingPrice - $costPrice;
                                $profitMargin = $costPrice > 0 ? round(($profit / $costPrice) * 100, 1) : 0;
                            @endphp

                            @if(auth()->user()->role !== 'Admin tổng')
                            <td class="text-end fw-bold">
                                <span class="badge {{ $stock > 0 ? 'text-bg-success' : 'text-bg-danger' }}">
                                    {{ number_format($stock, 2) }}
                                </span>
                                @if($location)
                                    <br><small class="text-muted fw-normal">Vị trí: {{ $location }}</small>
                                @endif
                            </td>
                            
                            <td class="text-end">{{ number_format($costPrice) }} ₫</td>
                            <td class="text-end">{{ number_format($sellingPrice) }} ₫</td>
                            <td class="text-end">
                                @if($profit > 0)
                                    <span class="text-success fw-bold">+{{ number_format($profit) }} ₫</span>
                                    <br><small class="badge text-bg-success">{{ $profitMargin }}%</small>
                                @elseif($profit < 0)
                                    <span class="text-danger fw-bold">{{ number_format($profit) }} ₫</span>
                                    <br><small class="badge text-bg-danger">{{ $profitMargin }}%</small>
                                @else
                                    <span class="text-muted">0 ₫</span>
                                @endif
                            </td>
                            @endif

                            <td>{{ $material->description }}</td>
                            @if(auth()->check() && (auth()->user()->role === 'Admin tổng' || auth()->user()->role === 'Admin kho'))
                            <td>
                                @if(auth()->user()->role === 'Admin kho')
                                <button type="button" class="btn btn-sm btn-info text-white" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#updateStockModal" 
                                        data-material-id="{{ $material->id }}" 
                                        data-material-name="{{ $material->name }}" 
                                        data-current-stock="{{ $stock }}" 
                                        data-current-cost-price="{{ $costPrice }}" 
                                        data-current-selling-price="{{ $sellingPrice }}" 
                                        data-current-location="{{ $location }}">
                                    Nhập tồn
                                </button>
                                @endif
                                <a href="{{ route('materials.edit', $material) }}" class="btn btn-sm btn-warning">Sửa</a>
                                <form action="{{ route('materials.destroy', $material) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center">Chưa có dữ liệu</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $materials->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal cập nhật tồn kho -->
@if(auth()->check() && (auth()->user()->role === 'Admin tổng' || auth()->user()->role === 'Admin kho'))
<div class="modal fade" id="updateStockModal" tabindex="-1" aria-labelledby="updateStockModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('materials.update-stock') }}" method="POST">
            @csrf
            <input type="hidden" name="material_id" id="modal_material_id">
            <input type="hidden" name="warehouse_id" value="{{ $selectedWarehouseId }}">
            
            <div class="modal-content text-start">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateStockModalLabel">Cập nhật số lượng tồn kho</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Vật tư:</label>
                        <span id="modal_material_name" class="text-primary fw-bold fs-5"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Kho thực hiện:</label>
                        <span class="text-success fw-bold fs-6">
                            {{ $warehouses->firstWhere('id', $selectedWarehouseId)->name ?? 'Kho mặc định' }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <label for="modal_stock" class="form-label fw-bold">Số lượng tồn kho mới <span class="text-danger">*</span></label>
                        <input type="number" step="any" min="0" class="form-control" name="stock" id="modal_stock" required placeholder="Nhập số lượng tồn kho hiện tại...">
                        <small class="text-muted d-block mt-1">Vui lòng nhập số lượng thực tế hiện tại trong kho. Hệ thống sẽ tính toán chênh lệch để tạo phiếu điều chỉnh tự động.</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="modal_cost_price" class="form-label fw-bold">Giá nhập kho này (VNĐ)</label>
                            <input type="number" class="form-control" name="cost_price" id="modal_cost_price" min="0" step="1" placeholder="Ví dụ: 15000">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="modal_selling_price" class="form-label fw-bold">Giá bán kho này (VNĐ)</label>
                            <input type="number" class="form-control" name="selling_price" id="modal_selling_price" min="0" step="1" placeholder="Ví dụ: 20000">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="modal_location" class="form-label fw-bold">Vị trí trong kho (Kệ / Ô / Dãy)</label>
                        <input type="text" class="form-control" name="location" id="modal_location" placeholder="Ví dụ: Kệ A1, Ô B2...">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-primary">Xác nhận cập nhật</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const updateStockModal = document.getElementById('updateStockModal');
        if (updateStockModal) {
            updateStockModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const materialId = button.getAttribute('data-material-id');
                const materialName = button.getAttribute('data-material-name');
                const currentStock = button.getAttribute('data-current-stock');
                const currentCost = button.getAttribute('data-current-cost-price');
                const currentSelling = button.getAttribute('data-current-selling-price');
                const currentLocation = button.getAttribute('data-current-location');
                
                document.getElementById('modal_material_id').value = materialId;
                document.getElementById('modal_material_name').textContent = materialName;
                document.getElementById('modal_stock').value = currentStock;
                document.getElementById('modal_cost_price').value = currentCost || 0;
                document.getElementById('modal_selling_price').value = currentSelling || 0;
                document.getElementById('modal_location').value = currentLocation;
            });
        }
    });
</script>
@endpush

@endsection
