@extends('layouts.admin')
@section('title', 'Chi tiết Nhân viên')
@section('header', 'Chi tiết Nhân viên: ' . $warehouseStaff->full_name)

@section('content')
<div class="row">
    {{-- Cột trái: thông tin --}}
    <div class="col-md-4">
        <div class="card card-outline card-primary">
            <div class="card-body text-center">
                <div class="mb-3">
                    <i class="bi bi-person-circle" style="font-size:5rem;color:#6c757d;"></i>
                </div>
                <h4 class="mb-0">{{ $warehouseStaff->full_name }}</h4>
                <p class="text-muted mb-1">{{ $warehouseStaff->position ?? 'Không có chức vụ' }}</p>
                <span class="badge text-bg-{{ $warehouseStaff->status === 'active' ? 'success' : 'secondary' }} mb-3">
                    {{ $warehouseStaff->status === 'active' ? 'Đang làm việc' : 'Đã nghỉ' }}
                </span>
                <hr>
                <table class="table table-sm text-start">
                    <tr><td class="text-muted">Kho</td><td class="fw-bold">{{ $warehouseStaff->warehouse->name ?? 'N/A' }}</td></tr>
                    <tr><td class="text-muted">Giới tính</td><td>{{ $warehouseStaff->gender_label }}</td></tr>
                    <tr><td class="text-muted">Ngày sinh</td><td>{{ $warehouseStaff->date_of_birth?->format('d/m/Y') ?? '—' }}</td></tr>
                    <tr><td class="text-muted">CCCD</td><td>{{ $warehouseStaff->id_card ?? '—' }}</td></tr>
                    <tr><td class="text-muted">SĐT</td><td>{{ $warehouseStaff->phone ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Địa chỉ</td><td>{{ $warehouseStaff->address ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Ngày vào</td><td>{{ $warehouseStaff->start_date?->format('d/m/Y') ?? '—' }}</td></tr>
                    <tr><td class="text-muted">Lương CB</td><td class="fw-bold text-success">{{ number_format($warehouseStaff->base_salary) }} ₫</td></tr>
                </table>
                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('warehouse-staffs.edit', $warehouseStaff) }}" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i> Chỉnh sửa</a>
                    <a href="{{ route('warehouse-staffs.index') }}" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Quay lại</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Cột phải: lịch sử lương --}}
    <div class="col-md-8">
        <div class="card card-outline card-warning mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="bi bi-cash-coin"></i> Lịch sử lương gần đây</h3>
                <a href="{{ route('salaries.index') }}" class="btn btn-sm btn-outline-warning">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kỳ lương</th>
                            <th class="text-center">Ngày công</th>
                            <th class="text-end">Lương cuối</th>
                            <th class="text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentSalaries as $sal)
                        <tr>
                            <td><a href="{{ route('salaries.show', $sal) }}">{{ $sal->period }}</a></td>
                            <td class="text-center">{{ $sal->total_work_days }}/{{ $sal->standard_work_days }}</td>
                            <td class="text-end fw-bold">{{ number_format($sal->final_salary) }} ₫</td>
                            <td class="text-center">
                                <span class="badge text-bg-{{ $sal->status_color }}">{{ $sal->status_label }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center text-muted py-3">Chưa có bảng lương</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Chấm công tháng hiện tại --}}
        <div class="card card-outline card-info">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="bi bi-calendar3"></i> Chấm công tháng {{ now()->format('m/Y') }}</h3>
                <a href="{{ route('shift-logs.index') }}" class="btn btn-sm btn-outline-info">Xem chấm công</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead class="table-light">
                        <tr><th>Ngày</th><th>Ca</th><th>Vào</th><th>Ra</th><th>Trạng thái</th></tr>
                    </thead>
                    <tbody>
                        @php
                            $monthLogs = $warehouseStaff->shiftLogs->filter(fn($l) => $l->work_date->month === now()->month && $l->work_date->year === now()->year)->sortByDesc('work_date')->take(10);
                        @endphp
                        @forelse($monthLogs as $log)
                        <tr>
                            <td>{{ $log->work_date->format('d/m') }}</td>
                            <td>{{ $log->shift->name ?? '—' }}</td>
                            <td>{{ $log->check_in ?? '—' }}</td>
                            <td>{{ $log->check_out ?? '—' }}</td>
                            <td>
                                @php $colors = ['present'=>'success','absent'=>'danger','late'=>'warning','half_day'=>'info']; @endphp
                                <span class="badge text-bg-{{ $colors[$log->status] ?? 'secondary' }}">{{ $log->status_label }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">Chưa có dữ liệu chấm công tháng này</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
