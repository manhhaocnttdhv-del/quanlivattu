@extends('layouts.admin')

@section('title', 'Cập nhật Kho hàng')
@section('header', 'Cập nhật Kho hàng')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-warning card-outline mb-4">
            <div class="card-header">
                <div class="card-title">Chỉnh sửa thông tin: {{ $warehouse->name }}</div>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ route('warehouses.update', $warehouse) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên kho <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $warehouse->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="2" required>{{ old('address', $warehouse->address) }}</textarea>
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status', $warehouse->status) == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="inactive" {{ old('status', $warehouse->status) == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="manager_id" class="form-label">Quản lý kho</label>
                            <select class="form-select @error('manager_id') is-invalid @enderror" id="manager_id" name="manager_id">
                                <option value="">-- Chưa gắn --</option>
                                @foreach($managers as $manager)
                                <option value="{{ $manager->id }}" {{ old('manager_id', $warehouse->manager_id) == $manager->id ? 'selected' : '' }}>{{ $manager->name }} ({{ $manager->email }})</option>
                                @endforeach
                            </select>
                            <div class="form-text">Bạn phải thiết lập User là Admin kho trước.</div>
                            @error('manager_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Cập nhật</button>
                    <a href="{{ route('warehouses.index') }}" class="btn btn-default float-end">Hủy bỏ</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
