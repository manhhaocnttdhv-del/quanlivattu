@extends('layouts.admin')

@section('title', 'Lập Phiếu xuất kho')
@section('header', 'Lập Phiếu xuất kho')

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

        <div class="card card-warning card-outline mb-4">
            <div class="card-header">
                <div class="card-title">Thông tin phiếu xuất</div>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ route('inventory-exits.store') }}" method="POST" id="exitForm">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="date" class="form-label">Ngày xuất <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('date') is-invalid @enderror" id="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required>
                            @error('date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="warehouse_id" class="form-label">Xuất từ Kho <span class="text-danger">*</span></label>
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
                            <label for="project_id" class="form-label">Công trình <span class="text-danger">*</span></label>
                            <select class="form-select @error('project_id') is-invalid @enderror" id="project_id" name="project_id" required>
                                <option value="">-- Chọn Công trình --</option>
                                @foreach($projects as $project)
                                <option value="{{ $project->id }}" data-warehouse-id="{{ $project->warehouse_id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                            @error('project_id')
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
                        <h5 class="mb-0">Danh sách Vật tư xuất</h5>
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
                                    <th style="width: 15%;">Số lượng xuất</th>
                                    <th style="width: 15%;">Đơn giá</th>
                                    <th style="width: 15%;">Thành tiền</th>
                                    <th style="width: 15%;">Vị trí lấy hàng</th>
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
                    <a href="{{ route('inventory-exits.index') }}" class="btn btn-default me-2">Hủy bỏ</a>
                    <button type="submit" class="btn btn-warning" id="submitBtn">Hoàn tất Xuất kho</button>
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
                        return [
                            'stock' => $s->stock,
                            'location' => $s->location,
                            'cost_price' => $s->cost_price,
                            'selling_price' => $s->selling_price
                        ];
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
            <input type="text" class="form-control text-end qty-input" name="materials[__INDEX__][quantity]" value="1" required>
        </td>
        <td>
            <input type="text" class="form-control text-end price-input" name="materials[__INDEX__][unit_price]" value="0">
        </td>
        <td class="subtotal-cell text-end align-middle fw-bold bg-light">0 ₫</td>
        <td>
            <input type="text" class="form-control" name="materials[__INDEX__][location]" placeholder="Vị trí lấy hàng">
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
        const form = document.getElementById('exitForm');
        const warehouseSelect = document.getElementById('warehouse_id');
        const projectSelect = document.getElementById('project_id');

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
                const priceVal = priceInput ? priceInput.value : '0';
                const price = parseFloat(priceVal.replace(/\D/g, "")) || 0;
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
            if (e.target.classList.contains('price-input')) {
                let cursorPosition = e.target.selectionStart;
                let originalLength = e.target.value.length;
                let cleanValue = e.target.value.replace(/\D/g, "");
                if (cleanValue) {
                    e.target.value = new Intl.NumberFormat("vi-VN").format(cleanValue);
                } else {
                    e.target.value = "";
                }
                let newLength = e.target.value.length;
                let diff = newLength - originalLength;
                e.target.setSelectionRange(cursorPosition + diff, cursorPosition + diff);
            }
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
                    alert('Phiếu xuất phải có ít nhất 1 vật tư.');
                }
            }
        });

        listBody.addEventListener('change', function(e) {
            if (e.target.classList.contains('material-select')) {
                const row = e.target.closest('tr');
                const selectedOption = e.target.options[e.target.selectedIndex];
                const unitCell = row.querySelector('.unit-cell');
                
                const unitName = selectedOption ? selectedOption.getAttribute('data-unit') : '';
                unitCell.textContent = unitName ? unitName : '-';

                updateRowData(row);
                calculateTotals();
            }
        });

        function filterMaterials(selectElement) {
            const selectedWarehouse = warehouseSelect.value;
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

        function updateRowData(row) {
            const materialSelect = row.querySelector('.material-select');
            if (!materialSelect) return;
            const selectedOption = materialSelect.options[materialSelect.selectedIndex];
            const locationInput = row.querySelector('input[name*="[location]"]');
            const priceInput = row.querySelector('.price-input');
            const warehouseId = warehouseSelect.value;

            if (selectedOption && selectedOption.value && warehouseId) {
                const stocks = JSON.parse(selectedOption.getAttribute('data-stocks') || '{}');
                if (stocks[warehouseId]) {
                    if (locationInput) locationInput.value = stocks[warehouseId].location || '';
                    if (priceInput) priceInput.value = new Intl.NumberFormat('vi-VN').format(Math.round(stocks[warehouseId].selling_price || 0));
                } else {
                    if (locationInput) locationInput.value = '';
                    if (priceInput) priceInput.value = 0;
                }
            } else {
                if (locationInput) locationInput.value = '';
                if (priceInput) priceInput.value = 0;
            }
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
                filterMaterials(newSelect);
            }
            materialIndex++;
            calculateTotals();
        }

        function filterProjects() {
            const selectedWarehouse = warehouseSelect.value;
            Array.from(projectSelect.options).forEach(option => {
                if (option.value === '') return;
                
                const projectWarehouseId = option.getAttribute('data-warehouse-id');
                if (!projectWarehouseId || projectWarehouseId === selectedWarehouse) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                    if (projectSelect.value === option.value) {
                        projectSelect.value = '';
                    }
                }
            });
        }

        function updateAllRowsData() {
            listBody.querySelectorAll('tr').forEach(row => {
                updateRowData(row);
            });
            calculateTotals();
        }

        warehouseSelect.addEventListener('change', function() {
            filterProjects();
            listBody.querySelectorAll('.material-select').forEach(select => {
                filterMaterials(select);
            });
            updateAllRowsData();
        });

        if (warehouseSelect.value) {
            filterProjects();
            listBody.querySelectorAll('.material-select').forEach(select => {
                filterMaterials(select);
            });
        }
    });
</script>
@endsection
