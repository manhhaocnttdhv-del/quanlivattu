@extends('layouts.admin')

@section('title', 'Thêm mới Công trình')
@section('header', 'Thêm mới Công trình')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-primary card-outline mb-4">
            <div class="card-header">
                <div class="card-title">Nhập thông tin</div>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ route('projects.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên công trình <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}">
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="address" class="form-label">Địa chỉ</label>
                        <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3">{{ old('address') }}</textarea>
                        @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    @if(auth()->check() && auth()->user()->role === 'Admin tổng')
                    <div class="mb-3">
                        <label for="warehouse_id" class="form-label">Gán Công trình cho Kho (Tùy chọn)</label>
                        <select class="form-select @error('warehouse_id') is-invalid @enderror" id="warehouse_id" name="warehouse_id">
                            <option value="">-- Dùng chung toàn hệ thống --</option>
                            @foreach($warehouses as $wh)
                            <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                        <small class="form-text text-muted">Nếu để trống, công trình này sẽ được dùng chung cho tất cả các kho.</small>
                        @error('warehouse_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Lưu lại</button>
                    <a href="{{ route('projects.index') }}" class="btn btn-default float-end">Hủy bỏ</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
