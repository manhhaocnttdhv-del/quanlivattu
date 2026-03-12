@extends('layouts.admin')

@section('title', 'Thêm mới Khách hàng')
@section('header', 'Thêm mới Khách hàng')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-primary card-outline mb-4">
            <div class="card-header">
                <div class="card-title">Nhập thông tin</div>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên khách hàng <span class="text-danger">*</span></label>
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
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Lưu lại</button>
                    <a href="{{ route('customers.index') }}" class="btn btn-default float-end">Hủy bỏ</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
