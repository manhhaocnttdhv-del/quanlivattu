@extends('layouts.admin')

@section('title', 'Thêm mới Người dùng')
@section('header', 'Thêm mới Người dùng')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-primary card-outline mb-4">
            <div class="card-header">
                <div class="card-title">Nhập thông tin</div>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                            @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required minlength="8">
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="role" class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select class="form-select @error('role') is-invalid @enderror" id="role" name="role" required>
                                <option value="Admin tổng" {{ old('role') == 'Admin tổng' ? 'selected' : '' }}>Admin tổng</option>
                                <option value="Admin kho" {{ old('role') == 'Admin kho' ? 'selected' : '' }}>Admin kho</option>
                                <option value="Nhân viên kho" {{ old('role') == 'Nhân viên kho' ? 'selected' : '' }}>Nhân viên kho</option>
                            </select>
                            @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="warehouse_id" class="form-label">Thuộc kho hàng</label>
                            <select class="form-select @error('warehouse_id') is-invalid @enderror" id="warehouse_id" name="warehouse_id">
                                <option value="">-- Không, hoặc Admin tổng --</option>
                                @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">Bắt buộc nếu là Admin kho/Nhân viên.</div>
                            @error('warehouse_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Trạng thái <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Khóa tài khoản</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Lưu lại</button>
                    <a href="{{ route('users.index') }}" class="btn btn-default float-end">Hủy bỏ</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
