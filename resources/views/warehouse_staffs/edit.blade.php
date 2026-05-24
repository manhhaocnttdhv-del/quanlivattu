@extends('layouts.admin')
@section('title', 'Sửa Nhân viên: ' . $warehouseStaff->full_name)
@section('header', 'Sửa thông tin: ' . $warehouseStaff->full_name)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card card-outline card-warning">
            <div class="card-header"><h3 class="card-title"><i class="bi bi-pencil"></i> Cập nhật thông tin nhân viên</h3></div>
            <div class="card-body">
                <form action="{{ route('warehouse-staffs.update', $warehouseStaff) }}" method="POST">
                    @csrf @method('PUT')
                    @if($errors->any())
                        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kho <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select" required>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ old('warehouse_id', $warehouseStaff->warehouse_id) == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tài khoản hệ thống</label>
                            <select name="user_id" class="form-select">
                                <option value="">-- Không liên kết --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ old('user_id', $warehouseStaff->user_id) == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $warehouseStaff->full_name) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $warehouseStaff->phone) }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">CCCD / CMND</label>
                            <input type="text" name="id_card" class="form-control" value="{{ old('id_card', $warehouseStaff->id_card) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Giới tính</label>
                            <select name="gender" class="form-select">
                                <option value="male" {{ old('gender', $warehouseStaff->gender) === 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ old('gender', $warehouseStaff->gender) === 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="other" {{ old('gender', $warehouseStaff->gender) === 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Ngày sinh</label>
                            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth', $warehouseStaff->date_of_birth?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Địa chỉ</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address', $warehouseStaff->address) }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Chức vụ</label>
                            <input type="text" name="position" class="form-control" value="{{ old('position', $warehouseStaff->position) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Ngày bắt đầu</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $warehouseStaff->start_date?->format('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status', $warehouseStaff->status) === 'active' ? 'selected' : '' }}>Đang làm</option>
                                <option value="inactive" {{ old('status', $warehouseStaff->status) === 'inactive' ? 'selected' : '' }}>Đã nghỉ</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Lương cơ bản (₫/tháng) <span class="text-danger">*</span></label>
                            <input type="number" name="base_salary" class="form-control" value="{{ old('base_salary', $warehouseStaff->base_salary) }}" min="0" step="1000" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Ghi chú</label>
                            <textarea name="note" class="form-control" rows="2">{{ old('note', $warehouseStaff->note) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Lưu thay đổi</button>
                        <a href="{{ route('warehouse-staffs.show', $warehouseStaff) }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
