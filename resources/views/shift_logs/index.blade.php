@extends('layouts.admin')
@section('title', 'Chấm công')
@section('header', 'Bảng Chấm công tháng ' . $month . '/' . $year)

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        {{-- Bộ lọc tháng --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <form method="GET" class="row g-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Tháng</label>
                        <select name="month" class="form-select form-select-sm">
                            @for($m = 1; $m <= 12; $m++)
                                <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Năm</label>
                        <select name="year" class="form-select form-select-sm">
                            @for($y = now()->year; $y >= now()->year - 3; $y--)
                                <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    @if(auth()->user()->isAdminTong())
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Kho</label>
                        <select name="warehouse_id" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-search"></i> Xem</button>
                    </div>
                    @if(!auth()->user()->isNhanVienKho())
                    <div class="col-md-2 ms-auto text-end">
                        <a href="{{ route('shift-logs.bulk') }}" class="btn btn-sm btn-success">
                            <i class="bi bi-people-fill"></i> Chấm công hàng loạt
                        </a>
                    </div>
                    @endif
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h3 class="card-title">Danh sách chấm công ({{ $logs->total() }} bản ghi)</h3></div>
            <div class="card-body p-0">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Ngày</th>
                            <th>Nhân viên</th>
                            <th>Kho</th>
                            <th>Ca làm</th>
                            <th class="text-center">Giờ vào</th>
                            <th class="text-center">Giờ ra</th>
                            <th class="text-center">Trạng thái</th>
                            <th>Ghi chú</th>
                            @if(!auth()->user()->isNhanVienKho())
                            <th class="text-center">Xóa</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @php $statusColors = ['present'=>'success','absent'=>'danger','late'=>'warning','half_day'=>'info']; @endphp
                        @forelse($logs as $log)
                        <tr>
                            <td class="fw-bold">{{ $log->work_date->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('warehouse-staffs.show', $log->staff_id) }}">{{ $log->staff->full_name ?? 'N/A' }}</a>
                            </td>
                            <td><span class="badge text-bg-secondary">{{ $log->staff->warehouse->name ?? '—' }}</span></td>
                            <td>{{ $log->shift->name ?? '—' }}<br><small class="text-muted">{{ $log->shift->duration ?? '' }}</small></td>
                            <td class="text-center">{{ $log->check_in ?? '—' }}</td>
                            <td class="text-center">{{ $log->check_out ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge text-bg-{{ $statusColors[$log->status] ?? 'secondary' }}">{{ $log->status_label }}</span>
                            </td>
                            <td class="small text-muted">{{ $log->note ?? '' }}</td>
                            @if(!auth()->user()->isNhanVienKho())
                            <td class="text-center">
                                <form action="{{ route('shift-logs.destroy', $log) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Xóa bản ghi chấm công này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr><td colspan="9" class="text-center py-4 text-muted">Chưa có dữ liệu chấm công tháng này</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                {{ $logs->links() }}
                <a href="{{ route('shift-logs.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i> Thêm chấm công</a>
            </div>
        </div>
    </div>
</div>
@endsection
