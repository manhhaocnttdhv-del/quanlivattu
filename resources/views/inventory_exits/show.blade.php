@extends('layouts.admin')

@section('title', 'Chi tiết Phiếu xuất')
@section('header', 'Chi tiết Phiếu xuất: #' . $inventoryExit->id)

@section('content')
<div class="row">
    <div class="col-12">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="invoice p-3 mb-3 border rounded shadow-sm bg-body">
            <!-- title row -->
            <div class="row">
                <div class="col-12">
                    <h4>
                        <i class="bi bi-box-arrow-right"></i> PHIẾU XUẤT KHO
                        <small class="float-end">Ngày lập: {{ \Carbon\Carbon::parse($inventoryExit->date)->format('d/m/Y') }}</small>
                    </h4>
                    <div class="mt-2">
                        @php
                            $statusMap = ['pending' => ['label' => 'Chờ duyệt', 'color' => 'warning'], 'completed' => ['label' => 'Hoàn thành', 'color' => 'success'], 'cancelled' => ['label' => 'Đã hủy', 'color' => 'danger']];
                            $s = $statusMap[$inventoryExit->status] ?? ['label' => $inventoryExit->status, 'color' => 'secondary'];
                        @endphp
                        <span class="badge text-bg-{{ $s['color'] }} fs-6">{{ $s['label'] }}</span>
                    </div>
                </div>
            </div>

            <!-- info row -->
            <div class="row invoice-info mb-4 mt-3">
                <div class="col-sm-4 invoice-col">
                    Xuất cho Công trình
                    <address>
                        <strong>{{ $inventoryExit->project->name ?? 'N/A' }}</strong><br>
                        {{ $inventoryExit->project->address ?? 'Không có địa chỉ' }}<br>
                        SĐT: {{ $inventoryExit->project->phone ?? 'N/A' }}<br>
                    </address>
                </div>
                <div class="col-sm-4 invoice-col">
                    Từ Kho hàng
                    <address>
                        <strong>{{ $inventoryExit->warehouse->name ?? 'N/A' }}</strong><br>
                        {{ $inventoryExit->warehouse->address ?? 'Không có địa chỉ' }}<br>
                    </address>
                </div>
                <div class="col-sm-4 invoice-col">
                    Thông tin phiếu<br>
                    <b>Mã phiếu:</b> PX-{{ str_pad($inventoryExit->id, 5, '0', STR_PAD_LEFT) }}<br>
                    <b>Người lập:</b> {{ $inventoryExit->user->name ?? 'N/A' }}<br>
                    <b>Ghi chú:</b> {{ $inventoryExit->note ?? 'Không có' }}
                </div>
            </div>

            <!-- Shipping Info Row -->
            @if($inventoryExit->deliveryPartner)
            <div class="row bg-light border rounded p-3 mb-4 mx-0 shadow-sm align-items-center">
                <div class="col-md-3">
                    <h6 class="fw-bold text-secondary mb-1"><i class="bi bi-truck text-primary"></i> Phương tiện / Đối tác</h6>
                    <div class="fw-bold fs-6 text-primary">{{ $inventoryExit->deliveryPartner->name }}</div>
                    @if($inventoryExit->deliveryPartner->type == 'internal')
                        <span class="badge text-bg-primary mt-1"><i class="bi bi-truck me-1"></i> Xe nội bộ</span>
                    @else
                        <span class="badge text-bg-info mt-1"><i class="bi bi-building me-1"></i> Đối tác ngoài</span>
                    @endif
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold text-secondary mb-1"><i class="bi bi-file-earmark-text text-info"></i> Mã vận đơn / Số chuyến</h6>
                    <div class="fw-bold font-monospace text-dark">{{ $inventoryExit->delivery_code ?? 'N/A' }}</div>
                </div>
                <div class="col-md-3">
                    <h6 class="fw-bold text-secondary mb-1"><i class="bi bi-cash-stack text-success"></i> Phí vận chuyển</h6>
                    <div class="fw-bold text-danger">{{ number_format($inventoryExit->delivery_fee) }} ₫</div>
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
                        $ds = $delStatusMap[$inventoryExit->delivery_status] ?? ['label' => $inventoryExit->delivery_status, 'color' => 'dark'];
                    @endphp
                    <span class="badge text-bg-{{ $ds['color'] }} fs-6">{{ $ds['label'] }}</span>
                </div>
                @if($inventoryExit->deliveryPartner->license_plate || $inventoryExit->deliveryPartner->driver_name)
                <div class="col-12 mt-2 pt-2 border-top">
                    <small class="text-muted">
                        @if($inventoryExit->deliveryPartner->license_plate)
                            <strong>Biển số xe:</strong> <span class="font-monospace text-dark">{{ $inventoryExit->deliveryPartner->license_plate }}</span> &nbsp;|&nbsp;
                        @endif
                        @if($inventoryExit->deliveryPartner->driver_name)
                            <strong>Tài xế:</strong> <span class="text-dark">{{ $inventoryExit->deliveryPartner->driver_name }} ({{ $inventoryExit->deliveryPartner->driver_phone ?? 'N/A' }})</span>
                        @endif
                    </small>
                </div>
                @endif
            </div>
            @endif

            <div class="row">
                <div class="col-12 table-responsive">
                    <table class="table table-striped table-bordered text-center align-middle">
                        <thead class="table-warning">
                            <tr>
                                <th>STT</th>
                                <th class="text-start">Tên vật tư</th>
                                <th>ĐVT</th>
                                <th>Số lượng</th>
                                <th class="text-end">Đơn giá xuất</th>
                                <th>Vị trí</th>
                                <th class="text-end">Thành tiền</th>
                                <th class="text-center">Trạng thái</th>
                                @if($inventoryExit->status === 'pending')
                                @can('Duyệt / Hủy phiếu xuất kho')
                                <th class="text-center d-print-none">Thao tác</th>
                                @endcan
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach($inventoryExit->details as $detail)
                            @php
                                $subtotal = $detail->quantity * $detail->unit_price;
                                $total += ($detail->status === 'approved') ? $subtotal : 0;
                            @endphp
                            <tr class="{{ $detail->status === 'approved' ? 'table-success' : '' }}">
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start">{{ $detail->material->name ?? 'N/A' }}</td>
                                <td>{{ $detail->material->unit->name ?? 'N/A' }}</td>
                                <td class="fw-bold">{{ (float)$detail->quantity }}</td>
                                <td class="text-end">{{ number_format($detail->unit_price) }} ₫</td>
                                <td><span class="badge text-bg-warning">{{ $detail->location ?? 'N/A' }}</span></td>
                                <td class="text-end fw-bold">{{ number_format($subtotal) }} ₫</td>
                                <td class="text-center">
                                    @if($detail->status === 'approved')
                                        <span class="badge text-bg-success"><i class="bi bi-check-circle"></i> Đã duyệt</span>
                                    @else
                                        <span class="badge text-bg-secondary"><i class="bi bi-hourglass-split"></i> Chờ duyệt</span>
                                    @endif
                                </td>
                                @if($inventoryExit->status === 'pending')
                                @can('Duyệt / Hủy phiếu xuất kho')
                                <td class="text-center d-print-none">
                                    @if($detail->status === 'pending')
                                        {{-- Nút Duyệt --}}
                                        <form action="{{ route('inventory-exits.details.approve', [$inventoryExit, $detail]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success"
                                                    onclick="return confirm('Duyệt dòng vật tư này và trừ tồn kho?')">
                                                <i class="bi bi-check-lg"></i> Duyệt
                                            </button>
                                        </form>
                                        {{-- Nút Xóa khỏi phiếu --}}
                                        <form action="{{ route('inventory-exits.details.remove', [$inventoryExit, $detail]) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Xóa dòng vật tư &quot;{{ $detail->material->name }}&quot; khỏi phiếu? Hành động này không thể hoàn tác.')">
                                                <i class="bi bi-x-lg"></i> Xóa
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                @endcan
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="{{ $inventoryExit->status === 'pending' ? '8' : '7' }}" class="text-end fs-5">
                                    TỔNG CỘNG (các dòng đã duyệt):
                                </th>
                                <th class="text-end fs-5 text-danger fw-bolder">{{ number_format($total) }} ₫</th>
                                @if($inventoryExit->status === 'pending')
                                    @can('Duyệt / Hủy phiếu xuất kho')
                                        <th></th>
                                    @endcan
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- Nút hành động tổng phiếu --}}
            <div class="row d-print-none mt-4">
                <div class="col-12 text-end d-flex gap-2 justify-content-end">
                    <a href="{{ route('inventory-exits.index') }}" class="btn btn-default"><i class="bi bi-arrow-left"></i> Quay lại</a>
                    <button type="button" class="btn btn-warning" onclick="window.print()"><i class="bi bi-printer"></i> In Phiếu</button>

                    @if($inventoryExit->status === 'pending')
                    @can('Duyệt / Hủy phiếu xuất kho')
                    {{-- Duyệt toàn bộ phiếu --}}
                    <form action="{{ route('inventory-exits.approve', $inventoryExit) }}" method="POST" class="d-inline" onsubmit="return confirm('Duyệt toàn bộ các dòng còn lại trong phiếu xuất này và trừ số lượng khỏi tồn kho?');">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-all"></i> Duyệt toàn bộ
                        </button>
                    </form>
                    {{-- Hủy toàn bộ phiếu --}}
                    <form action="{{ route('inventory-exits.cancel', $inventoryExit) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Hủy toàn bộ phiếu xuất này?')">
                            <i class="bi bi-x-circle"></i> Hủy cả phiếu
                        </button>
                    </form>
                    @endcan
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    @@media print {
        body { font-size: 14pt; }
        .app-sidebar, .app-header, .app-footer, .app-title { display: none !important; }
        .app-main { margin-left: 0 !important; width: 100% !important; padding: 0;}
    }
</style>
@endsection
