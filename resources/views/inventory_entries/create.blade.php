@extends('layouts.admin')

@section('title', 'Lập Phiếu nhập kho')
@section('header', 'Lập Phiếu nhập kho')

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

        <div class="card card-primary card-outline mb-4">
            <div class="card-header">
                <div class="card-title">Thông tin phiếu nhập</div>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ route('inventory-entries.store') }}" method="POST" id="entryForm">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="date" class="form-label">Ngày nhập <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="warehouse_id" class="form-label">Nhập vào Kho <span class="text-danger">*</span></label>
                            <select class="form-select @error('warehouse_id') is-invalid @enderror" id="warehouse_id" name="warehouse_id" required>
                                <option value="">-- Chọn Kho hàng --</option>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('warehouse_id', auth()->user()->warehouse_id) == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="supplier_id" class="form-label">Nhà cung cấp <span class="text-danger">*</span></label>
                            <select class="form-select @error('supplier_id') is-invalid @enderror" id="supplier_id" name="supplier_id" required>
                                <option value="">-- Chọn Nhà cung cấp --</option>
                                @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" data-warehouse-id="{{ $supplier->warehouse_id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="note" class="form-label">Ghi chú</label>
                        <textarea class="form-control @error('note') is-invalid @enderror" id="note" name="note" rows="2">{{ old('note') }}</textarea>
                        @error('note')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">Danh sách Vật tư nhập</h5>
                        <button type="button" class="btn btn-sm btn-success" id="addMaterialBtn">
                            <i class="bi bi-cart-plus"></i> Thêm vật tư
                        </button>
                    </div>

                    @if($errors->has('materials'))
                        <div class="alert alert-danger p-2 mb-3">Vui lòng thêm ít nhất một vật tư hợp lệ.</div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle" id="materialsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Tên Vật tư</th>
                                    <th style="width: 10%;">ĐVT</th>
                                    <th style="width: 15%;">Số lượng</th>
                                    <th style="width: 15%;">Đơn giá</th>
                                    <th style="width: 15%;">Thành tiền</th>
                                    <th style="width: 15%;">Vị trí kệ</th>
                                    <th style="width: 10%; text-align: center;">Xóa</th>
                                </tr>
                            </thead>
                            <tbody id="materialList">
                                <!-- Rows will be added here via JS -->
                            </tbody>
                            <tfoot>
                                <tr class="table-light fw-bold">
                                    <td colspan="4" class="text-end fw-bold">TỔNG CỘNG:</td>
                                    <td colspan="3" class="text-start text-danger fw-bolder" id="totalAmount">0 ₫</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer text-end">
                    <a href="{{ route('inventory-entries.index') }}" class="btn btn-default me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary" id="submitBtn">Hoàn tất Nhập kho</button>
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
                        return ['stock' => $s->stock, 'location' => $s->location, 'cost_price' => $s->cost_price, 'selling_price' => $s->selling_price];
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
            <input type="number" class="form-control text-end price-input" name="materials[__INDEX__][unit_price]" min="0" value="0">
        </td>
        <td class="subtotal-cell text-end align-middle fw-bold bg-light">0 ₫</td>
        <td>
            <input type="text" class="form-control" name="materials[__INDEX__][location]" placeholder="Vị trí (vd: Kệ A1)">
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
        const form = document.getElementById('entryForm');
        const warehouseSelect = document.getElementById('warehouse_id');
        const supplierSelect = document.getElementById('supplier_id');

        // Add first row automatically
        addRow();

        addBtn.addEventListener('click', function() {
            addRow();
        });

        function calculateTotals() {
            let total = 0;
            listBody.querySelectorAll('tr').forEach(row => {
                const qtyInput = row.querySelector('.qty-input');
                const priceInput = row.querySelector('.price-input');
                const subtotalCell = row.querySelector('.subtotal-cell');

                const qty = parseFloat(qtyInput ? qtyInput.value : 0) || 0;
                const price = parseFloat(priceInput ? priceInput.value : 0) || 0;
                const subtotal = qty * price;
                total += subtotal;

                if (subtotalCell) {
                    subtotalCell.textContent = new Intl.NumberFormat('vi-VN').format(subtotal) + ' ₫';
                }
            });

            const totalAmountEl = document.getElementById('totalAmount');
            if (totalAmountEl) {
                totalAmountEl.textContent = new Intl.NumberFormat('vi-VN').format(total) + ' ₫';
            }
        }

        listBody.addEventListener('input', function(e) {
            if (e.target.classList.contains('qty-input') || e.target.classList.contains('price-input')) {
                calculateTotals();
            }
        });

        listBody.addEventListener('click', function(e) {
            if (e.target.closest('.remove-row-btn')) {
                const row = e.target.closest('tr');
                if (listBody.querySelectorAll('tr').length > 1) {
                    row.remove();
                    calculateTotals();
                } else {
                    alert('Phiếu nhập phải có ít nhất 1 vật tư.');
                }
            }
        });

        listBody.addEventListener('change', function(e) {
            if (e.target.classList.contains('material-select')) {
                const selectedOption = e.target.options[e.target.selectedIndex];
                const row = e.target.closest('tr');
                const unitCell = row.querySelector('.unit-cell');
                const priceInput = row.querySelector('.price-input');
                const locationInput = row.querySelector('input[name*="[location]"]');
                const warehouseId = warehouseSelect.value;
                
                const unitName = selectedOption ? selectedOption.getAttribute('data-unit') : '';
                unitCell.textContent = unitName ? unitName : '-';
                
                if (selectedOption && selectedOption.value && warehouseId) {
                    const stocks = JSON.parse(selectedOption.getAttribute('data-stocks') || '{}');
                    if (stocks[warehouseId]) {
                        if (priceInput) priceInput.value = stocks[warehouseId].cost_price || 0;
                        if (locationInput) locationInput.value = stocks[warehouseId].location || '';
                    } else {
                        if (priceInput) priceInput.value = 0;
                        if (locationInput) locationInput.value = '';
                    }
                }
                calculateTotals();
            }
        });

        function updateRowStockText(selectElement) {
            const selectedWarehouse = warehouseSelect.value;
            const options = selectElement.querySelectorAll('option');
            
            options.forEach(option => {
                if (option.value === '') return;
                
                const originalName = option.getAttribute('data-name');
                let stockAmount = 0;
                
                if (selectedWarehouse) {
                    const stocks = JSON.parse(option.getAttribute('data-stocks') || '{}');
                    if (stocks[selectedWarehouse]) {
                        stockAmount = parseFloat(stocks[selectedWarehouse].stock);
                    }
                }
                
                option.textContent = `${originalName} (Tồn: ${stockAmount})`;
            });
        }

        form.addEventListener('submit', function(e) {
            if (listBody.querySelectorAll('tr').length === 0) {
                e.preventDefault();
                alert('Vui lòng thêm ít nhất một vật tư.');
            }
        });

        function addRow() {
            let newRowHtml = template.replace(/__INDEX__/g, materialIndex);
            listBody.insertAdjacentHTML('beforeend', newRowHtml);
            const newRow = listBody.lastElementChild;
            const newSelect = newRow.querySelector('.material-select');
            if (newSelect) {
                updateRowStockText(newSelect);
            }
            materialIndex++;
            calculateTotals();
        }

        function filterSuppliers() {
            const selectedWarehouse = warehouseSelect.value;
            let currentSupplierValid = false;

            Array.from(supplierSelect.options).forEach(option => {
                if (!option.value) return; // Skip dummy option
                
                const supplierWarehouseId = option.getAttribute('data-warehouse-id');
                // Show if supplier is global (no warehouse) or matches selected warehouse
                if (!supplierWarehouseId || supplierWarehouseId === selectedWarehouse) {
                    option.style.display = '';
                    if (option.selected) currentSupplierValid = true;
                } else {
                    option.style.display = 'none';
                    if (option.selected) {
                        option.selected = false;
                        supplierSelect.value = '';
                    }
                }
            });
        }

        function updateAllRowsData() {
            listBody.querySelectorAll('tr').forEach(row => {
                const materialSelect = row.querySelector('.material-select');
                if (materialSelect && materialSelect.value) {
                    const selectedOption = materialSelect.options[materialSelect.selectedIndex];
                    const priceInput = row.querySelector('.price-input');
                    const locationInput = row.querySelector('input[name*="[location]"]');
                    const warehouseId = warehouseSelect.value;
                    
                    if (selectedOption && warehouseId) {
                        const stocks = JSON.parse(selectedOption.getAttribute('data-stocks') || '{}');
                        if (stocks[warehouseId]) {
                            if (priceInput) priceInput.value = stocks[warehouseId].cost_price || 0;
                            if (locationInput) locationInput.value = stocks[warehouseId].location || '';
                        } else {
                            if (priceInput) priceInput.value = 0;
                            if (locationInput) locationInput.value = '';
                        }
                    }
                }
            });
            calculateTotals();
        }

        warehouseSelect.addEventListener('change', function() {
            filterSuppliers();
            updateAllRowsData();
            listBody.querySelectorAll('.material-select').forEach(select => {
                updateRowStockText(select);
            });
        });
        
        // Run once on load
        if (warehouseSelect.value) {
            filterSuppliers();
            listBody.querySelectorAll('.material-select').forEach(select => {
                updateRowStockText(select);
            });
        }
    });
</script>
@endsection
