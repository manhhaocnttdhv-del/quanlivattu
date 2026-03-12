@extends('layouts.admin')

@section('title', 'Cập nhật Nhà cung cấp')
@section('header', 'Cập nhật Nhà cung cấp')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-warning card-outline mb-4">
            <div class="card-header">
                <div class="card-title">Chỉnh sửa thông tin: {{ $supplier->name }}</div>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên nhà cung cấp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $supplier->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $supplier->phone) }}">
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address', $supplier->address) }}</textarea>
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-warning">Cập nhật</button>
                    <a href="{{ route('suppliers.index') }}" class="btn btn-default float-end">Hủy bỏ</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
