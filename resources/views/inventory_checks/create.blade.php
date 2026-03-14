@extends('layouts.admin')

@section('title', 'Tạo Phiếu Kiểm kê')
@section('header', 'Tạo Phiếu Kiểm kê Kho')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Error Messages -->
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card card-primary card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title">Thông tin Phiếu Kiểm kê</h3>
            </div>
            
            <form id="selectWarehouseForm" action="{{ route('inventory-checks.create') }}" method="GET" class="px-4 pt-3 pb-0">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="warehouse_id" class="form-label">Chọn Kho cần kiểm kê <span class="text-danger">*</span></label>
                        <select class="form-select" id="warehouse_id" name="warehouse_id" onchange="document.getElementById('selectWarehouseForm').submit();" required>
                            <option value="">-- Chọn Kho --</option>
                            @foreach($warehouses as $warehouse)
                                <option value="{{ $warehouse->id }}" {{ $selectedWarehouseId == $warehouse->id ? 'selected' : '' }}>
                                    {{ $warehouse->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>

            @if($selectedWarehouseId)
            <form action="{{ route('inventory-checks.store') }}" method="POST">
                @csrf
                <input type="hidden" name="warehouse_id" value="{{ $selectedWarehouseId }}">
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="date" class="form-label">Ngày kiểm kê <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="note" class="form-label">Ghi chú</label>
                            <input type="text" class="form-control" id="note" name="note" value="{{ old('note') }}" placeholder="Lý do kiểm kê...">
                        </div>
                    </div>

                    <h5 class="mt-4 mb-3 border-bottom pb-2">Danh sách Vật tư trong kho</h5>
                    
                    <table class="table table-bordered table-striped" id="materialsTable">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Mã - Tên Vật tư</th>
                                <th>ĐVT</th>
                                <th class="text-center" style="width: 15%">Tồn Máy tính</th>
                                <th class="text-center" style="width: 20%">Tồn Thực tế (Nhập) <span class="text-danger">*</span></th>
                                <th class="text-center" style="width: 15%">Độ lệch</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($materials as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    <input type="hidden" name="items[{{ $index }}][material_id]" value="{{ $item->material_id }}">
                                    {{ $item->material->id }} - {{ $item->material->name }}
                                </td>
                                <td>{{ $item->material->unit->name ?? '' }}</td>
                                <td class="text-center align-middle">
                                    <input type="hidden" name="items[{{ $index }}][system_stock]" value="{{ $item->stock }}">
                                    <span class="badge text-bg-secondary fs-6">{{ $item->stock }}</span>
                                </td>
                                <td>
                                    <input type="number" step="0.01" class="form-control text-center actual-stock-input" 
                                           name="items[{{ $index }}][actual_stock]" 
                                           value="{{ old('items.'.$index.'.actual_stock') }}" 
                                           data-system="{{ $item->stock }}" required>
                                </td>
                                <td class="text-center align-middle variance-cell fw-bold">
                                    -
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-danger py-3">Kho này chưa có vật tư nào được ghi nhận. Không thể kiểm kê.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- /.card-body -->

                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('inventory-checks.index') }}" class="btn btn-secondary">Hủy</a>
                    @if(count($materials) > 0)
                    <button type="submit" class="btn btn-primary">Xác nhận tạo Phiếu</button>
                    @endif
                </div>
            </form>
            @endif
        </div>
        <!-- /.card -->
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.actual-stock-input');
        
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                const actual = parseFloat(this.value) || 0;
                const system = parseFloat(this.dataset.system) || 0;
                const variance = actual - system;
                
                const varianceCell = this.closest('tr').querySelector('.variance-cell');
                
                if (variance > 0) {
                    varianceCell.innerHTML = `<span class="text-success">+${variance.toFixed(2)} (Thừa)</span>`;
                } else if (variance < 0) {
                    varianceCell.innerHTML = `<span class="text-danger">${variance.toFixed(2)} (Thiếu)</span>`;
                } else {
                    varianceCell.innerHTML = `<span class="text-secondary">0 (Khớp)</span>`;
                }
            });
        });
    });
</script>
@endsection
