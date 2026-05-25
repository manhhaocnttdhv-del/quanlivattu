@extends('layouts.admin')

@section('title', 'Chi tiết Phiếu nhập')
@section('header', 'Chi tiết Phiếu nhập: #' . $inventoryEntry->id)

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
                        <i class="bi bi-box-arrow-in-right"></i> PHIẾU NHẬP KHO
                        <small class="float-end">Ngày lập: {{ \Carbon\Carbon::parse($inventoryEntry->date)->format('d/m/Y') }}</small>
                    </h4>
                    <div class="mt-2">
                        @php
                            $statusMap = ['pending' => ['label' => 'Chờ duyệt', 'color' => 'warning'], 'completed' => ['label' => 'Hoàn thành', 'color' => 'success'], 'cancelled' => ['label' => 'Đã hủy', 'color' => 'danger']];
                            $s = $statusMap[$inventoryEntry->status] ?? ['label' => $inventoryEntry->status, 'color' => 'secondary'];
                        @endphp
                        <span class="badge text-bg-{{ $s['color'] }} fs-6">{{ $s['label'] }}</span>
                    </div>
                </div>
                <!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info mb-4 mt-3">
                <div class="col-sm-4 invoice-col">
                    Từ Nhà cung cấp
                    <address>
                        <strong>{{ $inventoryEntry->supplier->name ?? 'N/A' }}</strong><br>
                        {{ $inventoryEntry->supplier->address ?? 'Không có địa chỉ' }}<br>
                        SĐT: {{ $inventoryEntry->supplier->phone ?? 'N/A' }}<br>
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    Nhập vào Kho
                    <address>
                        <strong>{{ $inventoryEntry->warehouse->name ?? 'N/A' }}</strong><br>
                        {{ $inventoryEntry->warehouse->address ?? 'Không có địa chỉ' }}<br>
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    Thông tin phiếu
                    <br>
                    <b>Mã phiếu:</b> PN-{{ str_pad($inventoryEntry->id, 5, '0', STR_PAD_LEFT) }}<br>
                    <b>Người lập:</b> {{ $inventoryEntry->user->name ?? 'N/A' }}<br>
                    <b>Ghi chú:</b> {{ $inventoryEntry->note ?? 'Không có' }}
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Table row -->
            <div class="row">
                <div class="col-12 table-responsive">
                    <table class="table table-striped table-bordered text-center align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>STT</th>
                                <th class="text-start">Tên vật tư</th>
                                <th>ĐVT</th>
                                <th>Số lượng</th>
                                <th class="text-end">Đơn giá nhập</th>
                                <th>Vị trí kệ</th>
                                <th class="text-end">Thành tiền</th>
                                <th class="text-center">Trạng thái</th>
                                @if($inventoryEntry->status === 'pending')
                                @can('Duyệt / Hủy phiếu nhập kho')
                                <th class="text-center d-print-none">Thao tác</th>
                                @endcan
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach($inventoryEntry->details as $detail)
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
                                @if($inventoryEntry->status === 'pending')
                                @can('Duyệt / Hủy phiếu nhập kho')
                                <td class="text-center d-print-none">
                                    @if($detail->status === 'pending')
                                        {{-- Nút Duyệt --}}
                                        <form action="{{ route('inventory-entries.details.approve', [$inventoryEntry, $detail]) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success"
                                                    onclick="return confirm('Duyệt dòng vật tư này và cộng tồn kho?')">
                                                <i class="bi bi-check-lg"></i> Duyệt
                                            </button>
                                        </form>
                                        {{-- Nút Xóa khỏi phiếu --}}
                                        <form action="{{ route('inventory-entries.details.remove', [$inventoryEntry, $detail]) }}" method="POST" class="d-inline">
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
                                <th colspan="{{ $inventoryEntry->status === 'pending' ? '8' : '7' }}" class="text-end fs-5">
                                    TỔNG CỘNG (các dòng đã duyệt):
                                </th>
                                <th class="text-end fs-5 text-danger fw-bolder">{{ number_format($total) }} ₫</th>
                                @if($inventoryEntry->status === 'pending')
                                    @can('Duyệt / Hủy phiếu nhập kho')
                                        <th></th>
                                    @endcan
                                @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <div class="row d-print-none mt-4">
                <div class="col-12 text-end d-flex gap-2 justify-content-end">
                    <a href="{{ route('inventory-entries.index') }}" class="btn btn-default"><i class="bi bi-arrow-left"></i> Quay lại</a>
                    <button type="button" class="btn btn-warning" onclick="window.print()"><i class="bi bi-printer"></i> In Phiếu</button>

                    @if($inventoryEntry->status === 'pending')
                    @can('Duyệt / Hủy phiếu nhập kho')
                    {{-- Duyệt toàn bộ phiếu --}}
                    <form action="{{ route('inventory-entries.approve', $inventoryEntry) }}" method="POST" class="d-inline" onsubmit="return confirm('Duyệt toàn bộ các dòng còn lại trong phiếu này và cộng số lượng vào tồn kho?');">
                        @csrf
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-all"></i> Duyệt toàn bộ
                        </button>
                    </form>
                    {{-- Hủy toàn bộ phiếu --}}
                    <form action="{{ route('inventory-entries.cancel', $inventoryEntry) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger"
                                onclick="return confirm('Hủy toàn bộ phiếu nhập này?')">
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
    @media print {
        body { font-size: 14pt; }
        .app-sidebar, .app-header, .app-footer, .app-title { display: none !important; }
        .app-main { margin-left: 0 !important; width: 100% !important; padding: 0;}
    }
</style>
@endsection
