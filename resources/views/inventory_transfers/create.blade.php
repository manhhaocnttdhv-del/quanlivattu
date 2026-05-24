@extends('layouts.admin')

@section('title', 'Lập Phiếu chuyển kho')
@section('header', 'Lập Phiếu chuyển kho')

@section('content')
<div class="row">
    <div class="col-md-10 mx-auto">
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

        <div class="card card-info card-outline mb-4">
            <div class="card-header">
                <div class="card-title">Thông tin điều chuyển</div>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ route('inventory-transfers.store') }}" method="POST" id="transferForm">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="date" class="form-label">Ngày chuyển <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="from_warehouse_id" class="form-label text-danger">Từ Kho (Xuất) <span class="text-danger">*</span></label>
                            <select class="form-select @error('from_warehouse_id') is-invalid @enderror" id="from_warehouse_id" name="from_warehouse_id" required>
                                <option value="">-- Chọn Kho xuất --</option>
                                @foreach($fromWarehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('from_warehouse_id', auth()->user()->warehouse_id) == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                            @error('from_warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="to_warehouse_id" class="form-label text-success">Đến Kho (Nhập) <span class="text-danger">*</span></label>
                            <select class="form-select @error('to_warehouse_id') is-invalid @enderror" id="to_warehouse_id" name="to_warehouse_id" required>
                                <option value="">-- Chọn Kho nhập --</option>
                                @foreach($toWarehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('to_warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                            @error('to_warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">Ghi chú / Lý do chuyển</label>
                        <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="2">{{ old('note') }}</textarea>
                        @error('note')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Danh sách Vật tư điều chuyển</h5>
                        <button type="button" class="btn btn-sm btn-info text-white" id="addMaterialBtn">
                            <i class="bi bi-cart-plus"></i> Thêm vật tư
                        </button>
                    </div>

                    @if($errors->has('materials'))
                        <div class="alert alert-danger p-2 mb-3">Vui lòng thêm ít nhất một vật tư hợp lệ.</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered" id="materialsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Tên Vật tư</th>
                                    <th style="width: 15%;">ĐVT</th>
                                    <th style="width: 15%;">Số lượng chuyển</th>
                                    <th style="width: 20%;">Vị trí tại kho xuất</th>
                                    <th style="width: 10%; text-align: center;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="materialList">
                                <!-- Rows will be added here via JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer text-end">
                    <a href="{{ route('inventory-transfers.index') }}" class="btn btn-info me-2 text-white">Hủy bỏ</a>
                    <button type="submit" class="btn btn-info text-white" id="submitBtn">Hoàn tất Chuyển kho</button>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
</div>

<!-- Template for JS -->
<template id="materialRowTemplate">
    <tr>
        <td>
            <select class="form-select material-select" name="materials[__INDEX__][id]" required>
                <option value="">-- Chọn Vật tư --</option>
                @foreach($materials as $material)
                @php
                    $stocks = $material->warehouseStocks->keyBy('warehouse_id')->map(function($s) {
                        return ['stock' => $s->stock, 'location' => $s->location];
                    });
                @endphp
                <option value="{{ $material->id }}" 
                        data-name="{{ $material->name }}"
                        data-unit="{{ $material->unit->name ?? '' }}"
                        data-stocks="{{ json_encode($stocks) }}">
                    {{ $material->name }}
                </option>
                @endforeach
            </select>
        </td>
        <td class="unit-cell text-center align-middle bg-light">-</td>
        <td>
            <input type="number" class="form-control text-end qty-input" name="materials[__INDEX__][quantity]" step="any" min="0.01" value="1" required>
        </td>
        <td>
            <input type="text" class="form-control" name="materials[__INDEX__][location]" readonly placeholder="Vị trí lấy hàng">
        </td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn"><i class="bi bi-trash"></i></button>
        </td>
    </tr>
</template>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let materialIndex = 0;
        const addBtn = document.getElementById('addMaterialBtn');
        const listBody = document.getElementById('materialList');
        const template = document.getElementById('materialRowTemplate').innerHTML;
        const form = document.getElementById('transferForm');
        const fromWhSelect = document.getElementById('from_warehouse_id');
        const toWhSelect = document.getElementById('to_warehouse_id');

        // Add first row automatically
        addRow();

        addBtn.addEventListener('click', function() {
            addRow();
        });

        listBody.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row-btn')) {
                const row = e.target.closest('tr');
                if (listBody.querySelectorAll('tr').length > 1) {
                    row.remove();
                } else {
                    alert('Phiếu chuyển kho phải có ít nhất 1 vật tư.');
                }
            }
        });

        listBody.addEventListener('change', function(e) {
            if (e.target.classList.contains('material-select')) {
                const row = e.target.closest('tr');
                const selectedOption = e.target.options[e.target.selectedIndex];
                const unitCell = row.querySelector('.unit-cell');
                const locationInput = row.querySelector('input[name*="[location]"]');
                
                const unitName = selectedOption ? selectedOption.getAttribute('data-unit') : '';
                unitCell.textContent = unitName ? unitName : '-';

                updateRowLocation(row);
            }
        });

        function filterMaterials(selectElement) {
            const selectedWarehouse = fromWhSelect.value;
            const options = selectElement.querySelectorAll('option');
            
            options.forEach(option => {
                if (option.value === '') return; // Skip placeholder
                
                const originalName = option.getAttribute('data-name');
                let hasStock = false;
                let stockAmount = 0;
                
                if (selectedWarehouse) {
                    const stocks = JSON.parse(option.getAttribute('data-stocks') || '{}');
                    if (stocks[selectedWarehouse] && parseFloat(stocks[selectedWarehouse].stock) > 0) {
                        hasStock = true;
                        stockAmount = parseFloat(stocks[selectedWarehouse].stock);
                    }
                }
                
                if (hasStock) {
                    option.style.display = '';
                    option.disabled = false;
                    option.textContent = `${originalName} (Tồn: ${stockAmount})`;
                } else {
                    option.style.display = 'none';
                    option.disabled = true;
                    option.textContent = originalName;
                    if (selectElement.value === option.value) {
                        selectElement.value = '';
                        selectElement.dispatchEvent(new Event('change', { bubbles: true }));
                    }
                }
            });
        }

        function updateRowLocation(row) {
            const materialSelect = row.querySelector('.material-select');
            if (!materialSelect) return;
            const selectedOption = materialSelect.options[materialSelect.selectedIndex];
            const locationInput = row.querySelector('input[name*="[location]"]');
            const fromWhId = fromWhSelect.value;

            if (selectedOption && selectedOption.value && fromWhId) {
                const stocks = JSON.parse(selectedOption.getAttribute('data-stocks') || '{}');
                if (stocks[fromWhId]) {
                    locationInput.value = stocks[fromWhId].location || 'Không định vị';
                } else {
                    locationInput.value = 'Hết hàng/Không có';
                }
            } else {
                if (locationInput) locationInput.value = '';
            }
        }
        
        fromWhSelect.addEventListener('change', function() {
            listBody.querySelectorAll('.material-select').forEach(select => {
                filterMaterials(select);
            });
            listBody.querySelectorAll('tr').forEach(row => {
                updateRowLocation(row);
            });
        });

        form.addEventListener('submit', function(e) {
            if (listBody.querySelectorAll('tr').length === 0) {
                e.preventDefault();
                alert('Vui lòng thêm ít nhất một vật tư.');
                return;
            }

            const fromWh = fromWhSelect.value;
            const toWh = toWhSelect.value;
            
            if(fromWh === toWh && fromWh !== '') {
                e.preventDefault();
                alert('Kho xuất và Kho nhập không được trùng nhau.');
            }
        });

        function addRow() {
            let newRowHtml = template.replace(/__INDEX__/g, materialIndex);
            listBody.insertAdjacentHTML('beforeend', newRowHtml);
            const newRow = listBody.lastElementChild;
            const newSelect = newRow.querySelector('.material-select');
            if (newSelect) {
                filterMaterials(newSelect);
            }
            materialIndex++;
        }

        if (fromWhSelect.value) {
            listBody.querySelectorAll('.material-select').forEach(select => {
                filterMaterials(select);
            });
        }
    });
</script>
@endsection
