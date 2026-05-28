@extends('layouts.admin')

@section('title', 'Chi tiết Phiếu chuyển')
@section('header', 'Chi tiết Phiếu chuyển: #' . $inventoryTransfer->id)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="invoice p-3 mb-3 border rounded shadow-sm bg-body">
            <!-- title row -->
            <div class="row">
                <div class="col-12">
                    <h4>
                        <i class="bi bi-arrow-left-right"></i> PHIẾU CHUYỂN KHO
                        <small class="float-end">Ngày lập: {{ \Carbon\Carbon::parse($inventoryTransfer->date)->format('d/m/Y') }}</small>
                    </h4>
                </div>
                <!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info mb-4 mt-3">
                <div class="col-sm-4 invoice-col">
                    Từ Kho (Xuất)
                    <address>
                        <strong class="text-danger">{{ $inventoryTransfer->fromWarehouse->name ?? 'N/A' }}</strong><br>
                        {{ $inventoryTransfer->fromWarehouse->address ?? 'Không có địa chỉ' }}<br>
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    Đến Kho (Nhập)
                    <address>
                        <strong class="text-success">{{ $inventoryTransfer->toWarehouse->name ?? 'N/A' }}</strong><br>
                        {{ $inventoryTransfer->toWarehouse->address ?? 'Không có địa chỉ' }}<br>
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    Thông tin phiếu
                    <br>
                    <b>Mã phiếu:</b> PC-{{ str_pad($inventoryTransfer->id, 5, '0', STR_PAD_LEFT) }}<br>
                    <b>Người lập:</b> {{ $inventoryTransfer->user->name ?? 'N/A' }}<br>
                    <b>Lý do/Ghi chú:</b> {{ $inventoryTransfer->note ?? 'Không có' }}
                </div>
            </div>

            <!-- Shipping Info Row -->
            @if($inventoryTransfer->deliveryPartner)
            <div class="row bg-light border rounded p-3 mb-4 mx-0 shadow-sm align-items-center">
                <div class="col-md-3">
                    <h6 class="fw-bold text-secondary mb-1"><i class="bi bi-truck text-primary"></i> Phương tiện / Đối tác</h6>
                    <div class="fw-bold fs-6 text-primary">{{ $inventoryTransfer->deliveryPartner->name }}</div>
                    @if($inventoryTransfer->deliveryPartner->type == 'internal')
                        <span class="badge text-bg-primary mt-1"><i class="bi bi-truck me-1"></i> Xe nội bộ</span>
                    @else
                        <span class="badge text-bg-info mt-1"><i class="bi bi-building me-1"></i> Đối tác ngoài</span>
                    @endif
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold text-secondary mb-1"><i class="bi bi-file-earmark-text text-info"></i> Mã vận đơn / Số chuyến</h6>
                    <div class="fw-bold font-monospace text-dark">{{ $inventoryTransfer->delivery_code ?? 'N/A' }}</div>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold text-secondary mb-1"><i class="bi bi-cash-stack text-success"></i> Phí vận chuyển</h6>
                    <div class="fw-bold text-danger">{{ number_format($inventoryTransfer->delivery_fee) }} ₫</div>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold text-secondary mb-1">Trạng thái giao nhận</h6>
                    @php
                        $delStatusMap = [
                            'pending' => ['label' => 'Chờ giao hàng', 'color' => 'secondary'],
                            'in_transit' => ['label' => 'Đang vận chuyển', 'color' => 'warning'],
                            'delivered' => ['label' => 'Đã giao hàng', 'color' => 'success'],
                            'failed' => ['label' => 'Thất bại', 'color' => 'danger']
                        ];
                        $ds = $delStatusMap[$inventoryTransfer->delivery_status] ?? ['label' => $inventoryTransfer->delivery_status, 'color' => 'dark'];
                    @endphp
                    <span class="badge text-bg-{{ $ds['color'] }} fs-6">{{ $ds['label'] }}</span>
                </div>
                @if($inventoryTransfer->deliveryPartner->license_plate || $inventoryTransfer->deliveryPartner->driver_name)
                <div class="col-12 mt-2 pt-2 border-top">
                    <small class="text-muted">
                        @if($inventoryTransfer->deliveryPartner->license_plate)
                            <strong>Biển số xe:</strong> <span class="font-monospace text-dark">{{ $inventoryTransfer->deliveryPartner->license_plate }}</span> &nbsp;|&nbsp;
                        @endif
                        @if($inventoryTransfer->deliveryPartner->driver_name)
                            <strong>Tài xế:</strong> <span class="text-dark">{{ $inventoryTransfer->deliveryPartner->driver_name }} ({{ $inventoryTransfer->deliveryPartner->driver_phone ?? 'N/A' }})</span>
                        @endif
                    </small>
                </div>
                @endif
            </div>
            @endif
            <!-- Table row -->
            <div class="row">
                <div class="col-12 table-responsive">
                    <table class="table table-striped table-bordered text-center align-middle">
                        <thead class="table-info">
                            <tr>
                                <th>STT</th>
                                <th class="text-start">Tên vật tư</th>
                                <th>ĐVT</th>
                                <th>Số lượng chuyển</th>
                                <th>Vị trí tại kho nguồn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventoryTransfer->details as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start">{{ $detail->material->name ?? 'N/A' }}</td>
                                <td>{{ $detail->material->unit->name ?? 'N/A' }}</td>
                                <td class="fw-bold">{{ (float)$detail->quantity }}</td>
                                <td><span class="badge text-bg-info text-white">{{ $detail->location ?? 'N/A' }}</span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <div class="row d-print-none mt-4">
                <div class="col-12 text-end">
                    <a href="{{ route('inventory-transfers.index') }}" class="btn btn-default"><i class="bi bi-arrow-left"></i> Quay lại</a>
                    <button type="button" class="btn btn-info text-white" onclick="window.print()"><i class="bi bi-printer"></i> In Phiếu</button>
                </div>
            </div>
        </div>
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
