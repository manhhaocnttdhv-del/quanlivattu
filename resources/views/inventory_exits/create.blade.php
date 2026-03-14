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
                                <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                            @error('warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="customer_id" class="form-label">Khách hàng <span class="text-danger">*</span></label>
                            <select class="form-select @error('customer_id') is-invalid @enderror" id="customer_id" name="customer_id" required>
                                <option value="">-- Chọn Khách hàng --</option>
                                @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" data-warehouse-id="{{ $customer->warehouse_id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                            @error('customer_id')
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
                        <table class="table table-bordered" id="materialsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Tên Vật tư</th>
                                    <th style="width: 15%;">ĐVT</th>
                                    <th style="width: 20%;">Số lượng xuất</th>
                                    <th style="width: 20%;">Vị trí lấy hàng</th>
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
                        return ['stock' => $s->stock, 'location' => $s->location];
                    });
                @endphp
                <option value="{{ $material->id }}" 
                        data-unit="{{ $material->unit->name ?? '' }}"
                        data-stocks="{{ json_encode($stocks) }}">
                    {{ $material->name }}
                </option>
                @endforeach
            </select>
        </td>
        <td class="unit-cell text-center align-middle bg-light">-</td>
        <td>
            <input type="number" class="form-control text-end qty-input" name="materials[__INDEX__][quantity]" step="0.01" min="0.01" value="1" required>
        </td>
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
                    alert('Phiếu xuất phải có ít nhất 1 vật tư.');
                }
            }
        });

        listBody.addEventListener('change', function(e) {
            if (e.target.classList.contains('material-select')) {
                const row = e.target.closest('tr');
                const selectedOption = e.target.options[e.target.selectedIndex];
                const unitCell = row.querySelector('.unit-cell');
                const locationInput = row.querySelector('input[name*="[location]"]');
                
                const unitName = selectedOption.getAttribute('data-unit');
                unitCell.textContent = unitName ? unitName : '-';

                updateRowLocation(row);
            }
        });

        function updateRowLocation(row) {
            const materialSelect = row.querySelector('.material-select');
            const selectedOption = materialSelect.options[materialSelect.selectedIndex];
            const locationInput = row.querySelector('input[name*="[location]"]');
            const warehouseId = document.getElementById('warehouse_id').value;

            if (selectedOption && selectedOption.value && warehouseId) {
                const stocks = JSON.parse(selectedOption.getAttribute('data-stocks') || '{}');
                if (stocks[warehouseId]) {
                    locationInput.value = stocks[warehouseId].location || '';
                } else {
                    locationInput.value = '';
                }
            }
        }

        const warehouseSelect = document.getElementById('warehouse_id');
        const customerSelect = document.getElementById('customer_id');

        form.addEventListener('submit', function(e) {
            if (listBody.querySelectorAll('tr').length === 0) {
                e.preventDefault();
                alert('Vui lòng thêm ít nhất một vật tư.');
            }
        });

        function addRow() {
            let newRowHtml = template.replace(/__INDEX__/g, materialIndex);
            listBody.insertAdjacentHTML('beforeend', newRowHtml);
            materialIndex++;
        }

        function filterCustomers() {
            const selectedWarehouse = warehouseSelect.value;
            Array.from(customerSelect.options).forEach(option => {
                if (!option.value) return; 
                
                const customerWarehouseId = option.getAttribute('data-warehouse-id');
                if (!customerWarehouseId || customerWarehouseId === selectedWarehouse) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                    if (option.selected) {
                        option.selected = false;
                        customerSelect.value = '';
                    }
                }
            });
        }

        warehouseSelect.addEventListener('change', function() {
            filterCustomers();
            listBody.querySelectorAll('tr').forEach(row => {
                updateRowLocation(row);
            });
        });

        if (warehouseSelect.value) {
            filterCustomers();
        }
    });
</script>
@endsection
