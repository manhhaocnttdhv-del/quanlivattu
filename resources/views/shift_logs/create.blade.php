@extends('layouts.admin')
@section('title', 'Thêm chấm công')
@section('header', 'Thêm chấm công đơn lẻ')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-7">
        <div class="card card-outline card-primary">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="bi bi-calendar-check"></i> Chấm công</h3>
                @if(!auth()->user()->isNhanVienKho())
                <a href="{{ route('shift-logs.bulk') }}" class="btn btn-sm btn-success">
                    <i class="bi bi-people-fill"></i> Chấm công hàng loạt
                </a>
                @endif
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                @endif
                <form action="{{ route('shift-logs.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nhân viên <span class="text-danger">*</span></label>
                            <select name="staff_id" class="form-select" required>
                                <option value="">-- Chọn nhân viên --</option>
                                @foreach($staffs as $staff)
                                    <option value="{{ $staff->id }}" {{ (old('staff_id') == $staff->id || $staffs->count() === 1) ? 'selected' : '' }}>
                                        {{ $staff->full_name }} — {{ $staff->warehouse->name ?? '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ca làm <span class="text-danger">*</span></label>
                            <select name="shift_id" class="form-select" required>
                                <option value="">-- Chọn ca --</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}" {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                        {{ $shift->name }} ({{ $shift->duration }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày làm việc <span class="text-danger">*</span></label>
                            <input type="date" name="work_date" class="form-control" value="{{ old('work_date', date('Y-m-d')) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Trạng thái <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="present" {{ old('status','present') === 'present' ? 'selected' : '' }}>✅ Có mặt</option>
                                <option value="late" {{ old('status') === 'late' ? 'selected' : '' }}>⏰ Đi trễ</option>
                                <option value="half_day" {{ old('status') === 'half_day' ? 'selected' : '' }}>🌓 Nửa ngày</option>
                                <option value="absent" {{ old('status') === 'absent' ? 'selected' : '' }}>❌ Vắng mặt</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Giờ vào</label>
                            <input type="time" name="check_in" class="form-control" value="{{ old('check_in') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Giờ ra</label>
                            <input type="time" name="check_out" class="form-control" value="{{ old('check_out') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ghi chú</label>
                            <input type="text" name="note" class="form-control" value="{{ old('note') }}" placeholder="Lý do vắng, trễ...">
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Lưu chấm công</button>
                        <a href="{{ route('shift-logs.index') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
