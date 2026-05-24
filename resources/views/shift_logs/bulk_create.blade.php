@extends('layouts.admin')
@section('title', 'Chấm công hàng loạt')
@section('header', 'Chấm công hàng loạt')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-11">
        <div class="card card-outline card-success">
            <div class="card-header"><h3 class="card-title"><i class="bi bi-people-fill"></i> Chấm công cả nhóm trong 1 ngày</h3></div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <form action="{{ route('shift-logs.bulk-store') }}" method="POST" id="bulk-form">
                    @csrf
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ngày làm việc <span class="text-danger">*</span></label>
                            <input type="date" name="work_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Ca làm <span class="text-danger">*</span></label>
                            <select name="shift_id" class="form-select" required>
                                <option value="">-- Chọn ca --</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}">{{ $shift->name }} ({{ $shift->duration }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-secondary w-100" onclick="setAllStatus('present')">
                                <i class="bi bi-check-all"></i> Điểm danh tất cả có mặt
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Họ tên</th>
                                    <th class="text-center">Trạng thái</th>
                                    <th class="text-center">Giờ vào</th>
                                    <th class="text-center">Giờ ra</th>
                                    <th>Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($staffs as $i => $staff)
                                <tr>
                                    <td>{{ $i + 1 }}</td>
                                    <td class="fw-bold">{{ $staff->full_name }}<br><small class="text-muted">{{ $staff->position }}</small></td>
                                    <td>
                                        <input type="hidden" name="logs[{{ $i }}][staff_id]" value="{{ $staff->id }}">
                                        <select name="logs[{{ $i }}][status]" class="form-select form-select-sm status-select">
                                            <option value="present">✅ Có mặt</option>
                                            <option value="late">⏰ Đi trễ</option>
                                            <option value="half_day">🌓 Nửa ngày</option>
                                            <option value="absent">❌ Vắng mặt</option>
                                        </select>
                                    </td>
                                    <td><input type="time" name="logs[{{ $i }}][check_in]" class="form-control form-control-sm"></td>
                                    <td><input type="time" name="logs[{{ $i }}][check_out]" class="form-control form-control-sm"></td>
                                    <td><input type="text" name="logs[{{ $i }}][note]" class="form-control form-control-sm" placeholder="Ghi chú..."></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Lưu chấm công</button>
                        <a href="{{ route('shift-logs.index') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function setAllStatus(status) {
    document.querySelectorAll('.status-select').forEach(sel => sel.value = status);
}
</script>
@endsection
