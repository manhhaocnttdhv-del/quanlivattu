@extends('layouts.admin')
@section('title', 'Thống kê lịch sử vật tư')
@section('header', 'Thống kê lịch sử vật tư theo thời gian')

@section('content')
<div class="row">
    {{-- Form lọc --}}
    <div class="col-12">
        <div class="card card-outline card-primary mb-4">
            <div class="card-header"><h3 class="card-title"><i class="bi bi-funnel-fill"></i> Điều kiện thống kê</h3></div>
            <div class="card-body">
                <form method="GET" action="{{ route('reports.material-history') }}" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Vật tư <span class="text-danger">*</span></label>
                        <select name="material_id" class="form-select" required>
                            <option value="">-- Chọn vật tư --</option>
                            @foreach($materials as $mat)
                                <option value="{{ $mat->id }}" {{ request('material_id') == $mat->id ? 'selected' : '' }}>
                                    {{ $mat->name }} ({{ $mat->unit->name ?? '' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @if(auth()->user()->isAdminTong())
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Kho</label>
                        <select name="warehouse_id" class="form-select">
                            <option value="">-- Tất cả kho --</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Từ ngày <span class="text-danger">*</span></label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from', now()->startOfMonth()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">Đến ngày <span class="text-danger">*</span></label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Kết quả --}}
        @if($selectedMaterial)
        <div class="card card-outline card-success">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">
                    <i class="bi bi-box-seam"></i>
                    Lịch sử: <strong>{{ $selectedMaterial->name }}</strong>
                    ({{ $selectedMaterial->unit->name ?? '' }})
                    — {{ request('date_from') }} đến {{ request('date_to') }}
                </h3>
                <span class="badge text-bg-primary fs-6">{{ $history->count() }} giao dịch</span>
            </div>
            <div class="card-body p-0">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Ngày</th>
                            <th class="text-center">Loại</th>
                            <th>Mã phiếu</th>
                            <th>Kho</th>
                            <th class="text-end text-success">Nhập</th>
                            <th class="text-end text-danger">Xuất</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalIn = 0; $totalOut = 0; @endphp
                        @forelse($history as $row)
                        @php $totalIn += $row['in']; $totalOut += $row['out']; @endphp
                        <tr>
                            <td class="fw-bold">{{ \Carbon\Carbon::parse($row['date'])->format('d/m/Y') }}</td>
                            <td class="text-center">
                                <i class="bi {{ $row['type_icon'] }} me-1"></i>
                                {{ $row['type'] }}
                            </td>
                            <td><code>{{ $row['ref'] }}</code></td>
                            <td><span class="badge text-bg-secondary">{{ $row['warehouse'] }}</span></td>
                            <td class="text-end fw-bold text-success">
                                {{ $row['in'] > 0 ? '+' . number_format($row['in'], 2) : '—' }}
                            </td>
                            <td class="text-end fw-bold text-danger">
                                {{ $row['out'] > 0 ? '-' . number_format($row['out'], 2) : '—' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                Không có giao dịch nào trong khoảng thời gian này
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    @if($history->count() > 0)
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td colspan="4" class="text-end fs-6">TỔNG CỘNG:</td>
                            <td class="text-end text-success fs-6">+{{ number_format($totalIn, 2) }}</td>
                            <td class="text-end text-danger fs-6">-{{ number_format($totalOut, 2) }}</td>
                        </tr>
                        <tr class="table-info">
                            <td colspan="4" class="text-end fs-6">Biến động thuần (Nhập - Xuất):</td>
                            <td colspan="2" class="text-end fs-5 fw-bolder {{ ($totalIn - $totalOut) >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ ($totalIn - $totalOut) >= 0 ? '+' : '' }}{{ number_format($totalIn - $totalOut, 2) }}
                            </td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        </div>
        @elseif(request()->hasAny(['material_id','date_from','date_to']))
        <div class="alert alert-info"><i class="bi bi-info-circle"></i> Vui lòng chọn đầy đủ vật tư và khoảng thời gian để xem kết quả.</div>
        @else
        <div class="alert alert-light border text-center py-4">
            <i class="bi bi-bar-chart-line fs-1 text-muted"></i>
            <div class="mt-2 text-muted">Chọn vật tư và khoảng thời gian bên trên để xem lịch sử giao dịch.</div>
        </div>
        @endif
    </div>
</div>
@endsection
