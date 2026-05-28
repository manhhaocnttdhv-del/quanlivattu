@extends('layouts.admin')

@section('title', 'Thêm Phương tiện / Đối tác Vận chuyển')
@section('header', 'Thêm Phương tiện / Đối tác Vận chuyển')

@section('content')
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card card-primary card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title fw-bold"><i class="bi bi-plus-circle me-1"></i> Tạo mới phương tiện hoặc đối tác</h3>
            </div>
            <form action="{{ route('delivery-partners.store') }}" method="POST" id="partnerForm">
                @csrf
                <div class="card-body">
                    <!-- Name -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Tên phương tiện / Tên đối tác <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="VD: Xe tải Kia 1.4 tấn, Giao Hàng Tiết Kiệm..." required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Type -->
                    <div class="mb-3">
                        <label for="type" class="form-label fw-semibold">Loại hình vận chuyển <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="internal" {{ old('type') == 'internal' ? 'selected' : '' }}>Phương tiện nội bộ (Xe công ty)</option>
                            <option value="external" {{ old('type', 'external') == 'external' ? 'selected' : '' }}>Đối tác vận chuyển bên ngoài (3PL)</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Internal vehicle fields group -->
                    <div id="internalFields" class="p-3 mb-3 bg-light rounded border border-primary-subtle" style="display: none;">
                        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-truck"></i> Thông tin Xe & Tài xế Nội bộ</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="license_plate" class="form-label fw-semibold">Biển số xe <span class="text-danger">*</span></label>
                                <input type="text" name="license_plate" id="license_plate" class="form-control font-monospace @error('license_plate') is-invalid @enderror" value="{{ old('license_plate') }}" placeholder="VD: 29C-123.45">
                                @error('license_plate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="driver_name" class="form-label fw-semibold">Họ tên tài xế</label>
                                <input type="text" name="driver_name" id="driver_name" class="form-control @error('driver_name') is-invalid @enderror" value="{{ old('driver_name') }}" placeholder="VD: Nguyễn Văn A">
                                @error('driver_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-12">
                                <label for="driver_phone" class="form-label fw-semibold">Số điện thoại tài xế</label>
                                <input type="text" name="driver_phone" id="driver_phone" class="form-control @error('driver_phone') is-invalid @enderror" value="{{ old('driver_phone') }}" placeholder="VD: 0987654321">
                                @error('driver_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- External partner fields group -->
                    <div id="externalFields" class="p-3 mb-3 bg-light rounded border border-info-subtle">
                        <h6 class="fw-bold text-info mb-3"><i class="bi bi-building"></i> Thông tin liên hệ Đối tác Vận chuyển</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="contact_name" class="form-label fw-semibold">Người liên hệ</label>
                                <input type="text" name="contact_name" id="contact_name" class="form-control @error('contact_name') is-invalid @enderror" value="{{ old('contact_name') }}" placeholder="VD: Trần Thị B">
                                @error('contact_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="contact_phone" class="form-label fw-semibold">Số điện thoại liên hệ</label>
                                <input type="text" name="contact_phone" id="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" value="{{ old('contact_phone') }}" placeholder="VD: 0912345678">
                                @error('contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label fw-semibold">Trạng thái hoạt động <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Hoạt động</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Note -->
                    <div class="mb-3">
                        <label for="note" class="form-label fw-semibold">Ghi chú</label>
                        <textarea name="note" id="note" rows="3" class="form-control @error('note') is-invalid @enderror" placeholder="Ghi chú thêm thông tin (tải trọng, khu vực chạy...).">{{ old('note') }}</textarea>
                        @error('note')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card-footer bg-light d-flex justify-content-between">
                    <a href="{{ route('delivery-partners.index') }}" class="btn btn-secondary"><i class="bi bi-x-circle me-1"></i> Hủy bỏ</a>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Lưu lại</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const internalFields = document.getElementById('internalFields');
        const externalFields = document.getElementById('externalFields');
        const licensePlateInput = document.getElementById('license_plate');

        function toggleFields() {
            if (typeSelect.value === 'internal') {
                internalFields.style.display = 'block';
                externalFields.style.display = 'none';
                licensePlateInput.setAttribute('required', 'required');
            } else {
                internalFields.style.display = 'none';
                externalFields.style.display = 'block';
                licensePlateInput.removeAttribute('required');
            }
        }

        typeSelect.addEventListener('change', toggleFields);
        toggleFields(); // Initial run on load
    });
</script>
@endsection
