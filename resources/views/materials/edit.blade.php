@extends('layouts.admin')

@section('title', 'Cập nhật Vật tư')
@section('header', 'Cập nhật Vật tư')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-warning card-outline mb-4">
            <div class="card-header">
                <div class="card-title">Chỉnh sửa thông tin: {{ $material->name }}</div>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ route('materials.update', $material) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên vật tư <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $material->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label for="unit_id" class="form-label">Đơn vị tính <span class="text-danger">*</span></label>
                        <select class="form-select @error('unit_id') is-invalid @enderror" id="unit_id" name="unit_id" required>
                            <option value="">-- Chọn đơn vị tính --</option>
                            @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id', $material->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                        @error('unit_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Nhóm vật tư</label>
                        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                            <option value="">-- Không phân loại --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $material->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @foreach($cat->children as $child)
                                    <option value="{{ $child->id }}" {{ old('category_id', $material->category_id) == $child->id ? 'selected' : '' }}>&nbsp;&nbsp;└ {{ $child->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                        @error('category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="cost_price" class="form-label">Giá nhập / Giá vốn (VNĐ)</label>
                            <input type="number" class="form-control @error('cost_price') is-invalid @enderror" id="cost_price" name="cost_price" value="{{ old('cost_price', $material->cost_price) }}" min="0" step="1">
                            <small class="text-muted">Đơn giá mặc định khi nhập kho</small>
                            @error('cost_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="selling_price" class="form-label">Giá bán / Giá xuất (VNĐ)</label>
                            <input type="number" class="form-control @error('selling_price') is-invalid @enderror" id="selling_price" name="selling_price" value="{{ old('selling_price', $material->selling_price) }}" min="0" step="1">
                            <small class="text-muted">Đơn giá mặc định khi xuất kho</small>
                            @error('selling_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $material->description) }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="min_stock" class="form-label">Tồn kho tối thiểu</label>
                            <input type="number" class="form-control @error('min_stock') is-invalid @enderror" id="min_stock" name="min_stock" value="{{ old('min_stock', $material->min_stock) }}" min="0">
                            @error('min_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="max_stock" class="form-label">Tồn kho tối đa</label>
                            <input type="number" class="form-control @error('max_stock') is-invalid @enderror" id="max_stock" name="max_stock" value="{{ old('max_stock', $material->max_stock) }}" min="0">
                            @error('max_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Cập nhật</button>
                    <a href="{{ route('materials.index') }}" class="btn btn-default float-end">Hủy bỏ</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
