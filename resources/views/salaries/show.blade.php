@extends('layouts.admin')
@section('title', 'Chi tiết lương: ' . ($salary->staff->full_name ?? ''))
@section('header', 'Chi tiết Bảng lương — ' . ($salary->staff->full_name ?? '') . ' — ' . $salary->period)

@section('content')
<div class="row">
    <div class="col-md-5">
        {{-- Thông tin lương --}}
        <div class="card card-outline card-{{ $salary->status_color }} mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="bi bi-cash-coin"></i> Tổng hợp lương</h3>
                <span class="badge text-bg-{{ $salary->status_color }} fs-6">{{ $salary->status_label }}</span>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><td>Nhân viên</td><td class="fw-bold">{{ $salary->staff->full_name ?? '—' }}</td></tr>
                    <tr><td>Kho</td><td>{{ $salary->staff->warehouse->name ?? '—' }}</td></tr>
                    <tr><td>Kỳ lương</td><td>{{ $salary->period }}</td></tr>
                    <tr><td>Ngày công chuẩn</td><td>{{ $salary->standard_work_days }} ngày</td></tr>
                    <tr><td>Ngày công thực tế</td><td class="fw-bold text-primary">{{ $salary->total_work_days }} ngày</td></tr>
                    <tr><td>Lương cơ bản</td><td>{{ number_format($salary->base_salary) }} ₫</td></tr>
                    <tr><td>Lương thực (tính theo công)</td><td>{{ number_format($salary->actual_salary) }} ₫</td></tr>
                    <tr class="table-success"><td>Thưởng</td><td class="fw-bold text-success">+{{ number_format($salary->bonus) }} ₫</td></tr>
                    <tr class="table-danger"><td>Khấu trừ</td><td class="fw-bold text-danger">-{{ number_format($salary->deduction) }} ₫</td></tr>
                    <tr class="table-warning"><td class="fs-5 fw-bold">LƯƠNG CUỐI</td><td class="fs-5 fw-bold text-success">{{ number_format($salary->final_salary) }} ₫</td></tr>
                </table>

                @if($salary->note)
                    <div class="alert alert-light border mt-2 mb-0"><strong>Ghi chú:</strong> {{ $salary->note }}</div>
                @endif

                {{-- Form chỉnh thưởng/khấu trừ --}}
                @if($salary->status === 'draft')
                <hr>
                <form action="{{ route('salaries.update', $salary) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label small">Thưởng (₫)</label>
                            <input type="number" name="bonus" class="form-control form-control-sm" value="{{ $salary->bonus }}" min="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label small">Khấu trừ (₫)</label>
                            <input type="number" name="deduction" class="form-control form-control-sm" value="{{ $salary->deduction }}" min="0">
                        </div>
                        <div class="col-12">
                            <label class="form-label small">Ghi chú</label>
                            <input type="text" name="note" class="form-control form-control-sm" value="{{ $salary->note }}">
                        </div>
                        <div class="col-12"><button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-save"></i> Cập nhật</button></div>
                    </div>
                </form>
                @endif

                <div class="d-flex gap-2 mt-3">
                    @if($salary->status === 'draft')
                        <form action="{{ route('salaries.confirm', $salary) }}" method="POST" class="flex-fill">
                            @csrf
                            <button class="btn btn-warning w-100"><i class="bi bi-check-circle"></i> Xác nhận</button>
                        </form>
                    @elseif($salary->status === 'confirmed')
                        <form action="{{ route('salaries.pay', $salary) }}" method="POST" class="flex-fill"
                              onsubmit="return confirm('Xác nhận đã thanh toán lương?')">
                            @csrf
                            <button class="btn btn-success w-100"><i class="bi bi-cash"></i> Đã trả lương</button>
                        </form>
                    @endif
                    <a href="{{ route('salaries.index') }}" class="btn btn-secondary">Quay lại</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        {{-- Chi tiết chấm công --}}
        <div class="card card-outline card-info">
            <div class="card-header"><h3 class="card-title"><i class="bi bi-calendar3"></i> Chi tiết chấm công tháng {{ $salary->period }}</h3></div>
            <div class="card-body p-0">
                <table class="table table-sm align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Ngày</th>
                            <th>Ca</th>
                            <th class="text-center">Vào</th>
                            <th class="text-center">Ra</th>
                            <th class="text-center">Trạng thái</th>
                            <th class="text-center">Hệ số</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $statusColors = ['present'=>'success','absent'=>'danger','late'=>'warning','half_day'=>'info']; @endphp
                        @forelse($salary->staff->shiftLogs as $log)
                        <tr>
                            <td>{{ $log->work_date->format('d/m') }}</td>
                            <td>{{ $log->shift->name ?? '—' }}</td>
                            <td class="text-center">{{ $log->check_in ?? '—' }}</td>
                            <td class="text-center">{{ $log->check_out ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge text-bg-{{ $statusColors[$log->status] ?? 'secondary' }}">{{ $log->status_label }}</span>
                            </td>
                            <td class="text-center fw-bold">{{ $log->work_day_factor }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-3">Không có dữ liệu chấm công</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5" class="text-end">Tổng ngày công:</th>
                            <th class="text-center fw-bold text-primary">{{ $salary->total_work_days }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
