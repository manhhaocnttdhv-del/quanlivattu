@extends('layouts.admin')

@section('title', 'Chi tiết Phiếu nhập')
@section('header', 'Chi tiết Phiếu nhập: #' . $inventoryEntry->id)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="invoice p-3 mb-3 border rounded shadow-sm bg-body">
            <!-- title row -->
            <div class="row">
                <div class="col-12">
                    <h4>
                        <i class="bi bi-box-arrow-in-right"></i> PHIẾU NHẬP KHO
                        <small class="float-end">Ngày lập: {{ \Carbon\Carbon::parse($inventoryEntry->date)->format('d/m/Y') }}</small>
                    </h4>
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
                            </tr>
                        </thead>
                        <tbody>
                            @php $total = 0; @endphp
                            @foreach($inventoryEntry->details as $detail)
                            @php 
                                $subtotal = $detail->quantity * $detail->unit_price;
                                $total += $subtotal;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start">{{ $detail->material->name ?? 'N/A' }}</td>
                                <td>{{ $detail->material->unit->name ?? 'N/A' }}</td>
                                <td>{{ number_format($detail->quantity, 2) }}</td>
                                <td class="text-end">{{ number_format($detail->unit_price) }} ₫</td>
                                <td><span class="badge text-bg-secondary">{{ $detail->location ?? 'N/A' }}</span></td>
                                <td class="text-end fw-bold">{{ number_format($subtotal) }} ₫</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="6" class="text-end fs-5">TỔNG CỘNG:</th>
                                <th class="text-end fs-5 text-danger fw-bolder">{{ number_format($total) }} ₫</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <div class="row d-print-none mt-4">
                <div class="col-12 text-end">
                    <a href="{{ route('inventory-entries.index') }}" class="btn btn-default"><i class="bi bi-arrow-left"></i> Quay lại</a>
                    <button type="button" class="btn btn-primary" onclick="window.print()"><i class="bi bi-printer"></i> In Phiếu</button>
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
