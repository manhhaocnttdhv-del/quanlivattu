@extends('layouts.admin')

@section('title', 'Bảng điều khiển')
@section('header', 'Bảng điều khiển')

@section('content')
<!--begin::Row-->
<div class="row">
    <!-- ./col -->
    <div class="col-lg-2 col-6">
        <!-- small box -->
        <div class="small-box text-bg-info">
            <div class="inner">
                <h3>{{ $stats['total_warehouses'] }}</h3>
                <p>Kho hàng</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-building"></i>
            </div>
            <a href="{{ route('warehouses.index') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                Xem chi tiết <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-2 col-6">
        <!-- small box -->
        <div class="small-box text-bg-success">
            <div class="inner">
                <h3>{{ $stats['total_materials'] }}</h3>
                <p>Vật tư</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <a href="{{ route('materials.index') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                Xem chi tiết <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box text-bg-primary">
            <div class="inner">
                <h3>{{ number_format($stats['total_value']) }} <small>₫</small></h3>
                <p>Giá trị kho</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-cash-stack"></i>
            </div>
            <a href="{{ route('reports.inventory') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                Xem báo cáo <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-2 col-6">
        <!-- small box -->
        <div class="small-box text-bg-warning">
            <div class="inner">
                <h3>{{ $stats['total_entries'] }}</h3>
                <p>Phiếu Nhập</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-cart-plus"></i>
            </div>
            <a href="{{ route('inventory-entries.index') }}" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                Xem chi tiết <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box text-bg-danger">
            <div class="inner">
                <h3>{{ $stats['total_exits'] }}</h3>
                <p>Phiếu Xuất</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-cart-dash"></i>
            </div>
            <a href="{{ route('inventory-exits.index') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                Xem chi tiết <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card card-warning card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title">Nhập kho gần đây</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Mã PN</th>
                            <th>Ngày</th>
                            <th>Kho hàng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_entries as $entry)
                        <tr>
                            <td>#{{ $entry->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($entry->date)->format('d/m/Y') }}</td>
                            <td>{{ $entry->warehouse->name ?? 'N/A' }}</td>
                            <td><a href="{{ route('inventory-entries.show', $entry) }}" class="btn btn-xs btn-info"><i class="bi bi-eye text-white"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center">Chưa có giao dịch</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-primary card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title">Xuất kho gần đây</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Mã PX</th>
                            <th>Ngày</th>
                            <th>Khách hàng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_exits as $exit)
                        <tr>
                            <td>#{{ $exit->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($exit->date)->format('d/m/Y') }}</td>
                            <td>{{ $exit->project->name ?? 'N/A' }}</td>
                            <td><a href="{{ route('inventory-exits.show', $exit) }}" class="btn btn-xs btn-info"><i class="bi bi-eye text-white"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center">Chưa có giao dịch</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-8">
        <div class="card card-info card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title">Xu hướng Nhập - Xuất (7 ngày qua)</h3>
            </div>
            <div class="card-body">
                <canvas id="trendChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-success card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title">Cơ cấu Tồn kho theo Kho</h3>
            </div>
            <div class="card-body">
                <canvas id="distChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card card-danger card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Cảnh báo Hết vật tư (Dưới mức tối thiểu)</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Kho hàng</th>
                            <th>Tên vật tư</th>
                            <th>Vị trí</th>
                            <th class="text-end">Tồn kho hiện tại</th>
                            <th class="text-end">Mức tối thiểu (Min Stock)</th>
                            <th class="text-center">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockItems as $item)
                        <tr>
                            <td>{{ $item->warehouse_name }}</td>
                            <td>{{ $item->material_name }}</td>
                            <td><span class="badge text-bg-secondary">{{ $item->location ?? 'N/A' }}</span></td>
                            <td class="text-end text-danger fw-bold">{{ number_format($item->stock, 2) }}</td>
                            <td class="text-end fw-semibold">{{ number_format($item->min_stock, 2) }}</td>
                            <td class="text-center">
                                @if($item->stock <= 0)
                                    <span class="badge text-bg-danger">Hết hàng</span>
                                @else
                                    <span class="badge text-bg-warning">Sắp hết</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-success py-3"><i class="bi bi-check-circle"></i> Không có vật tư nào dưới mức tối thiểu.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!--end::Row-->
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($days) !!},
                datasets: [
                    {
                        label: 'Nhập kho',
                        data: {!! json_encode($entryCounts) !!},
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Xuất kho',
                        data: {!! json_encode($exitCounts) !!},
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: { beginAtZero: true, ticks: { stepSize: 1 } }
                }
            }
        });

        // Distribution Chart
        const distCtx = document.getElementById('distChart').getContext('2d');
        new Chart(distCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($stockDist->pluck('name')) !!},
                datasets: [{
                    data: {!! json_encode($stockDist->pluck('total_stock')) !!},
                    backgroundColor: ['#20c997', '#0dcaf0', '#6f42c1', '#fd7e14', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    });
</script>
@endpush
