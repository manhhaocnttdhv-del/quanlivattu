@extends('layouts.admin')

@section('title', 'Báo cáo Tồn kho')
@section('header', 'Báo cáo Tồn kho')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-success">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title w-100">Tồn kho hiện tại</h3>
                <div class="card-tools d-flex gap-2">
                    @if(auth()->user()->role === 'Admin tổng' && count($warehouses) > 0)
                    <form action="{{ route('reports.inventory') }}" method="GET" class="d-flex d-print-none gap-2" style="min-width: 300px;">
                        <select name="warehouse_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">-- Tất cả kho --</option>
                            @foreach($warehouses as $wh)
                                <option value="{{ $wh->id }}" {{ request('warehouse_id') == $wh->id ? 'selected' : '' }}>
                                    {{ $wh->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                    @endif
                    <a href="{{ route('reports.inventory.export-excel', request()->all()) }}" class="btn btn-sm btn-success d-print-none text-nowrap">
                        <i class="bi bi-file-earmark-excel"></i> Xuất Excel
                    </a>
                    <a href="{{ route('reports.inventory.export-pdf', request()->all()) }}" class="btn btn-sm btn-danger d-print-none text-nowrap">
                        <i class="bi bi-file-earmark-pdf"></i> Xuất PDF
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary d-print-none text-nowrap" onclick="window.print()">
                        <i class="bi bi-printer"></i> In báo cáo
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>STT</th>
                            <th>Kho hàng</th>
                            <th class="text-start">Tên Vật tư</th>
                            <th>ĐVT</th>
                            <th>Vị trí</th>
                            <th class="text-success fs-5">Tồn Hiện Tại</th>
                            <th class="text-end">Giá vốn</th>
                            <th class="text-end">Giá trị tồn</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $totalReportValue = 0; @endphp
                        @forelse($stockData as $item)
                        @php 
                            $itemTotalValue = $item->stock * $item->average_cost;
                            $totalReportValue += $itemTotalValue;
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->warehouse->name ?? '' }}</td>
                            <td class="text-start fw-bold">{{ $item->material->name ?? '' }}</td>
                            <td>{{ $item->material->unit->name ?? 'N/A' }}</td>
                            <td><span class="badge text-bg-secondary">{{ $item->location ?? 'N/A' }}</span></td>
                            <td class="text-success fs-5 fw-bolder">{{ (float)$item->stock }}</td>
                            <td class="text-end">{{ number_format($item->average_cost) }} ₫</td>
                            <td class="text-end fw-bold">{{ number_format($itemTotalValue) }} ₫</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Hệ thống chưa có dữ liệu tồn kho</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="7" class="text-end fs-5">TỔNG GIÁ TRỊ TÀI SẢN KHO:</th>
                            <th class="text-end fs-5 text-danger fw-bolder">{{ number_format($totalReportValue) }} ₫</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection

@section('styles')
<style>
    @media print {
        body { font-size: 14pt; }
        .app-sidebar, .app-header, .app-footer, .app-title { display: none !important; }
        .app-main { margin-left: 0 !important; width: 100% !important; padding: 0;}
    }
</style>
@endsection
