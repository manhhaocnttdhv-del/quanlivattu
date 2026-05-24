@extends('layouts.admin')
@section('title', 'Thêm Nhân viên Kho')
@section('header', 'Thêm Nhân viên Kho mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-9">
        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title"><i class="bi bi-person-plus"></i> Thông tin nhân viên</h3></div>
            <div class="card-body">
                <form action="{{ route('warehouse-staffs.store') }}" method="POST">
                    @csrf
                    @if($errors->any())
                        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                    @endif

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Kho <span class="text-danger">*</span></label>
                            <select name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror" required>
                                <option value="">-- Chọn kho --</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}" {{ old('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                @endforeach
                            </select>
                            @error('warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Tài khoản hệ thống (nếu có)</label>
                            <select name="user_id" class="form-select">
                                <option value="">-- Không liên kết --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ $u->role }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name') }}" required>
                            @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">CCCD / CMND</label>
                            <input type="text" name="id_card" class="form-control" value="{{ old('id_card') }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Giới tính</label>
                            <select name="gender" class="form-select">
                                <option value="male" {{ old('gender','male') === 'male' ? 'selected' : '' }}>Nam</option>
                                <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Nữ</option>
                                <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Khác</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Ngày sinh</label>
                            <input type="date" name="date_of_birth" class="form-control" value="{{ old('date_of_birth') }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Địa chỉ</label>
                            <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Chức vụ</label>
                            <input type="text" name="position" class="form-control" value="{{ old('position') }}" placeholder="Thủ kho, NV nhập kho...">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Ngày bắt đầu</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date', date('Y-m-d')) }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="active" {{ old('status','active') === 'active' ? 'selected' : '' }}>Đang làm</option>
                                <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Đã nghỉ</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Lương cơ bản (₫/tháng) <span class="text-danger">*</span></label>
                            <input type="number" name="base_salary" class="form-control @error('base_salary') is-invalid @enderror"
                                   value="{{ old('base_salary', 0) }}" min="0" step="1000" required>
                            @error('base_salary')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label fw-semibold">Ghi chú</label>
                            <textarea name="note" class="form-control" rows="2">{{ old('note') }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Lưu nhân viên</button>
                        <a href="{{ route('warehouse-staffs.index') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
