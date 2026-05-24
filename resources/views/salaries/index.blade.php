@extends('layouts.admin')
@section('title', 'Bảng lương tháng ' . $month . '/' . $year)
@section('header', 'Bảng lương tháng ' . $month . '/' . $year)

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
        @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

        {{-- Bộ lọc + Tính lương --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="row g-2 align-items-end">
                    <div class="col-auto">
                        <form method="GET" class="d-flex gap-2 align-items-end">
                            <div>
                                <label class="form-label small mb-1">Tháng</label>
                                <select name="month" class="form-select form-select-sm">
                                    @for($m = 1; $m <= 12; $m++)
                                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>Tháng {{ $m }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="form-label small mb-1">Năm</label>
                                <select name="year" class="form-select form-select-sm">
                                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-search"></i> Xem</button>
                        </form>
                    </div>
                    <div class="col-auto ms-auto">
                        <form action="{{ route('salaries.generate') }}" method="POST">
                            @csrf
                            <input type="hidden" name="month" value="{{ $month }}">
                            <input type="hidden" name="year" value="{{ $year }}">
                            <button type="submit" class="btn btn-sm btn-success"
                                    onclick="return confirm('Tự động tính lương tháng {{ $month }}/{{ $year }} từ dữ liệu chấm công?')">
                                <i class="bi bi-calculator"></i> Tính lương tháng {{ $month }}/{{ $year }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Danh sách bảng lương ({{ $records->total() }} nhân viên)</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Nhân viên</th>
                            <th>Kho</th>
                            <th class="text-center">Ngày công</th>
                            <th class="text-end">Lương CB</th>
                            <th class="text-end">Lương thực</th>
                            <th class="text-end">Thưởng</th>
                            <th class="text-end">Khấu trừ</th>
                            <th class="text-end text-success fw-bold">Lương cuối</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalFinal = 0; @endphp
                        @forelse($records as $rec)
                        @php $totalFinal += $rec->final_salary; @endphp
                        <tr>
                            <td>{{ $records->firstItem() + $loop->index }}</td>
                            <td class="fw-bold">
                                <a href="{{ route('salaries.show', $rec) }}">{{ $rec->staff->full_name ?? 'N/A' }}</a>
                            </td>
                            <td><span class="badge text-bg-info">{{ $rec->staff->warehouse->name ?? '—' }}</span></td>
                            <td class="text-center">{{ $rec->total_work_days }}/{{ $rec->standard_work_days }}</td>
                            <td class="text-end">{{ number_format($rec->base_salary) }}</td>
                            <td class="text-end">{{ number_format($rec->actual_salary) }}</td>
                            <td class="text-end text-success">+{{ number_format($rec->bonus) }}</td>
                            <td class="text-end text-danger">-{{ number_format($rec->deduction) }}</td>
                            <td class="text-end fw-bold text-success fs-6">{{ number_format($rec->final_salary) }} ₫</td>
                            <td class="text-center">
                                <span class="badge text-bg-{{ $rec->status_color }}">{{ $rec->status_label }}</span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('salaries.show', $rec) }}" class="btn btn-xs btn-info" title="Chi tiết"><i class="bi bi-eye"></i></a>
                                @if($rec->status === 'draft')
                                    <form action="{{ route('salaries.confirm', $rec) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-warning" title="Xác nhận"><i class="bi bi-check-circle"></i></button>
                                    </form>
                                @elseif($rec->status === 'confirmed')
                                    <form action="{{ route('salaries.pay', $rec) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Xác nhận đã trả lương cho {{ $rec->staff->full_name ?? "" }}?')">
                                        @csrf
                                        <button type="submit" class="btn btn-xs btn-success" title="Đã trả lương"><i class="bi bi-cash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="11" class="text-center py-4 text-muted">Chưa có bảng lương. Nhấn "Tính lương" để tạo.</td></tr>
                        @endforelse
                    </tbody>
                    @if($records->count() > 0)
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="8" class="text-end">TỔNG CHI LƯƠNG:</th>
                            <th class="text-end text-danger fs-5">{{ number_format($totalFinal) }} ₫</th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
            <div class="card-footer">{{ $records->links() }}</div>
        </div>
    </div>
</div>
@endsection
