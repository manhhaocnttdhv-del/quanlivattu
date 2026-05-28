@extends('layouts.admin')

@section('title', 'Bảng điều khiển')
@section('header', 'Bảng điều khiển')

@section('content')
<!--begin::Row-->
<div class="row">
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box text-bg-info">
            <div class="inner">
                <h3>{{ $stats['total_warehouses'] }}</h3>
                <p>Kho hàng hoạt động</p>
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
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box text-bg-success">
            <div class="inner">
                <h3>{{ $stats['total_materials'] }}</h3>
                <p>Tổng số mặt hàng</p>
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
                <p>Giá trị tồn kho</p>
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
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box text-bg-secondary" style="background-color: #6f42c1 !important;">
            <div class="inner text-white">
                <h3 class="text-white">{{ number_format($stats['total_delivery_fees']) }} <small>₫</small></h3>
                <p class="text-white-50">Tổng phí vận chuyển</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-truck text-white-50"></i>
            </div>
            <a href="{{ route('delivery-partners.index') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                Xem đối tác <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<div class="row mt-3">
    <!-- ./col -->
    <div class="col-lg-4 col-6">
        <!-- small box -->
        <div class="small-box text-bg-warning">
            <div class="inner text-dark">
                <h3 class="text-dark">{{ number_format($stats['total_import_value']) }} <small>₫</small></h3>
                <p class="text-dark-50">Tổng giá trị Nhập ({{ $stats['total_entries'] }} phiếu)</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-cart-plus text-dark-50"></i>
            </div>
            <a href="{{ route('inventory-entries.index') }}" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                Lịch sử nhập kho <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-6">
        <!-- small box -->
        <div class="small-box text-bg-danger">
            <div class="inner">
                <h3>{{ number_format($stats['total_export_value']) }} <small>₫</small></h3>
                <p class="text-white-50">Tổng giá trị Xuất ({{ $stats['total_exits'] }} phiếu)</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-cart-dash"></i>
            </div>
            <a href="{{ route('inventory-exits.index') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                Lịch sử xuất kho <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-12">
        <!-- small box -->
        <div class="small-box text-bg-dark">
            <div class="inner">
                <h3>{{ $stats['active_delivery_partners'] }}</h3>
                <p>Đối tác & Xe vận chuyển</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-people"></i>
            </div>
            <a href="{{ route('delivery-partners.index') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                Danh mục vận chuyển <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>
<!-- /.row -->

<!-- Activity Row -->
<div class="row mt-4">
    <div class="col-md-4">
        <div class="card card-warning card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title fw-bold"><i class="bi bi-cart-plus text-warning"></i> Nhập kho gần đây</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-sm text-center align-middle">
                    <thead>
                        <tr>
                            <th>Mã PN</th>
                            <th>Ngày</th>
                            <th>Kho hàng</th>
                            <th>Xem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_entries as $entry)
                        <tr>
                            <td class="font-monospace">#{{ str_pad($entry->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ \Carbon\Carbon::parse($entry->date)->format('d/m/Y') }}</td>
                            <td>{{ $entry->warehouse->name ?? 'N/A' }}</td>
                            <td><a href="{{ route('inventory-entries.show', $entry) }}" class="btn btn-xs btn-outline-info p-1 py-0"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-3">Chưa có giao dịch</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-danger card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title fw-bold"><i class="bi bi-cart-dash text-danger"></i> Xuất kho gần đây</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-sm text-center align-middle">
                    <thead>
                        <tr>
                            <th>Mã PX</th>
                            <th>Ngày</th>
                            <th>Công trình</th>
                            <th>Xem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_exits as $exit)
                        <tr>
                            <td class="font-monospace">#{{ str_pad($exit->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ \Carbon\Carbon::parse($exit->date)->format('d/m/Y') }}</td>
                            <td>{{ $exit->project->name ?? 'N/A' }}</td>
                            <td><a href="{{ route('inventory-exits.show', $exit) }}" class="btn btn-xs btn-outline-info p-1 py-0"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-3">Chưa có giao dịch</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-primary card-outline mb-4" style="border-color: #6f42c1 !important;">
            <div class="card-header">
                <h3 class="card-title fw-bold" style="color: #6f42c1;"><i class="bi bi-truck"></i> Chuyến vận chuyển gần đây</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-sm text-center align-middle">
                    <thead>
                        <tr>
                            <th>Loại / Mã</th>
                            <th>Đối tác / Xe</th>
                            <th>Phí</th>
                            <th>Xem</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentShipments as $shipment)
                        <tr>
                            <td class="text-start">
                                <span class="badge text-bg-light font-monospace">{{ $shipment['id'] }}</span>
                                <small class="text-muted d-block">{{ $shipment['type'] }}</small>
                            </td>
                            <td>
                                <strong>{{ $shipment['partner'] }}</strong>
                                <small class="text-muted d-block font-monospace">Mã: {{ $shipment['code'] }}</small>
                            </td>
                            <td class="text-end fw-bold text-danger">{{ number_format($shipment['fee']) }} ₫</td>
                            <td><a href="{{ $shipment['url'] }}" class="btn btn-xs btn-outline-info p-1 py-0"><i class="bi bi-eye"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-3">Chưa có chuyến vận chuyển</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card card-info card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title fw-bold"><i class="bi bi-graph-up-arrow"></i> Xu hướng Nhập - Xuất theo giá trị (7 ngày qua)</h3>
            </div>
            <div class="card-body">
                <canvas id="trendChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-success card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title fw-bold"><i class="bi bi-pie-chart"></i> Cơ cấu Tồn kho</h3>
            </div>
            <div class="card-body">
                <canvas id="distChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-secondary card-outline mb-4" style="border-color: #6f42c1 !important;">
            <div class="card-header">
                <h3 class="card-title fw-bold" style="color: #6f42c1;"><i class="bi bi-compass"></i> Trạng thái Giao hàng</h3>
            </div>
            <div class="card-body">
                <canvas id="deliveryChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
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
                            <td class="text-end text-danger fw-bold">{{ (float)$item->stock }}</td>
                            <td class="text-end fw-semibold">{{ (float)$item->min_stock }}</td>
                            <td class="text-center">
                                @if($item->stock <= 0)
                                    <span class="badge text-bg-danger">Hết hàng</span>
                                @else
                                    <span class="badge text-bg-warning">Sắp hết</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-success py-3"><i class="bi bi-check-circle"></i> Không có vật tư nào dưới mức tối thiểu.</td></tr>
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
                        label: 'Giá trị Nhập',
                        data: {!! json_encode($entryValues) !!},
                        borderColor: '#ffc107',
                        backgroundColor: 'rgba(255, 193, 7, 0.1)',
                        fill: true,
                        tension: 0.4
                    },
                    {
                        label: 'Giá trị Xuất',
                        data: {!! json_encode($exitValues) !!},
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.1)',
                        fill: true,
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value, index, values) {
                                return new Intl.NumberFormat('vi-VN', { notation: 'compact', compactDisplay: 'short' }).format(value) + ' ₫';
                            }
                        }
                    }
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

        // Delivery Status Chart
        const delCtx = document.getElementById('deliveryChart').getContext('2d');
        new Chart(delCtx, {
            type: 'doughnut',
            data: {
                labels: ['Chờ giao', 'Đang giao', 'Đã giao', 'Thất bại'],
                datasets: [{
                    data: [
                        {{ $statuses['pending'] }},
                        {{ $statuses['in_transit'] }},
                        {{ $statuses['delivered'] }},
                        {{ $statuses['failed'] }}
                    ],
                    backgroundColor: ['#6c757d', '#ffc107', '#198754', '#dc3545']
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
