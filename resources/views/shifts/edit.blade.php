@extends('layouts.admin')
@section('title', 'Sửa Ca làm việc')
@section('header', 'Sửa Ca làm việc: ' . $shift->name)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card card-outline card-warning">
            <div class="card-header"><h3 class="card-title"><i class="bi bi-pencil"></i> Cập nhật ca làm</h3></div>
            <div class="card-body">
                <form action="{{ route('shifts.update', $shift) }}" method="POST">
                    @csrf @method('PUT')
                    @if($errors->any())
                        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
                    @endif
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Kho <span class="text-danger">*</span></label>
                        <select name="warehouse_id" class="form-select" required>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ old('warehouse_id', $shift->warehouse_id) == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên ca <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $shift->name) }}" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Giờ bắt đầu <span class="text-danger">*</span></label>
                            <input type="time" name="start_time" class="form-control" value="{{ old('start_time', $shift->start_time) }}" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-semibold">Giờ kết thúc <span class="text-danger">*</span></label>
                            <input type="time" name="end_time" class="form-control" value="{{ old('end_time', $shift->end_time) }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ghi chú</label>
                        <textarea name="note" class="form-control" rows="2">{{ old('note', $shift->note) }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-warning"><i class="bi bi-save"></i> Lưu thay đổi</button>
                        <a href="{{ route('shifts.index') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
