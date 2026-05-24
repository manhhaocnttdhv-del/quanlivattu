@extends('layouts.admin')
@section('title', 'Cài đặt chung')
@section('header', 'Cài đặt chung hệ thống')

@section('content')
<div class="row">
    <div class="col-md-8">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="card card-outline card-primary">
            <div class="card-header"><h3 class="card-title"><i class="bi bi-gear-fill"></i> Cài đặt hệ thống</h3></div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <h6 class="fw-bold text-primary border-bottom pb-2"><i class="bi bi-box-seam"></i> Cài đặt Tồn kho</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mức tồn tối thiểu mặc định <span class="text-danger">*</span></label>
                            <input type="number" name="default_min_stock_level" class="form-control @error('default_min_stock_level') is-invalid @enderror"
                                   value="{{ old('default_min_stock_level', $settings->get('default_min_stock_level')?->value ?? 10) }}" min="0" step="any" required>
                            <div class="form-text text-muted">Cảnh báo sẽ kích hoạt khi tồn kho dưới mức này (áp dụng cho các vật tư chưa cài mức riêng)</div>
                            @error('default_min_stock_level')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="fw-bold text-warning border-bottom pb-2"><i class="bi bi-calendar-check"></i> Cài đặt Lương</h6>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Số ngày công chuẩn trong tháng <span class="text-danger">*</span></label>
                            <input type="number" name="standard_work_days" class="form-control @error('standard_work_days') is-invalid @enderror"
                                   value="{{ old('standard_work_days', $settings->get('standard_work_days')?->value ?? 26) }}" min="1" max="31" required>
                            <div class="form-text text-muted">Dùng để tính lương thực tế = Lương cơ bản × Ngày làm thực tế / Ngày công chuẩn</div>
                            @error('standard_work_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Lưu cài đặt</button>
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title"><i class="bi bi-info-circle"></i> Hướng dẫn</h3></div>
            <div class="card-body small">
                <p><strong>Mức tồn tối thiểu:</strong> Hệ thống sẽ hiển thị cảnh báo trong mục <em>Cảnh báo tồn kho</em> và trên Dashboard khi số lượng tồn thực tế thấp hơn mức này.</p>
                <p>Bạn có thể cài đặt mức riêng cho từng vật tư trong phần <em>Danh sách Vật tư</em>.</p>
                <hr>
                <p><strong>Ngày công chuẩn:</strong> Số ngày làm việc tiêu chuẩn trong tháng. Thông thường là 26 ngày (trừ chủ nhật) hoặc theo quy định công ty.</p>
            </div>
        </div>
    </div>
</div>
@endsection
