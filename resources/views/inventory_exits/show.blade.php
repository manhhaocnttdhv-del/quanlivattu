@extends('layouts.admin')

@section('title', 'Chi tiết Phiếu xuất')
@section('header', 'Chi tiết Phiếu xuất: #' . $inventoryExit->id)

@section('content')
<div class="row">
    <div class="col-12">
        <div class="invoice p-3 mb-3 border rounded shadow-sm bg-body">
            <!-- title row -->
            <div class="row">
                <div class="col-12">
                    <h4>
                        <i class="bi bi-box-arrow-right"></i> PHIẾU XUẤT KHO
                        <small class="float-end">Ngày lập: {{ \Carbon\Carbon::parse($inventoryExit->date)->format('d/m/Y') }}</small>
                    </h4>
                </div>
                <!-- /.col -->
            </div>
            <!-- info row -->
            <div class="row invoice-info mb-4 mt-3">
                <div class="col-sm-4 invoice-col">
                    Xuất cho Khách hàng
                    <address>
                        <strong>{{ $inventoryExit->project->name ?? 'N/A' }}</strong><br>
                        {{ $inventoryExit->project->address ?? 'Không có địa chỉ' }}<br>
                        SĐT: {{ $inventoryExit->project->phone ?? 'N/A' }}<br>
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    Từ Kho hàng
                    <address>
                        <strong>{{ $inventoryExit->warehouse->name ?? 'N/A' }}</strong><br>
                        {{ $inventoryExit->warehouse->address ?? 'Không có địa chỉ' }}<br>
                    </address>
                </div>
                <!-- /.col -->
                <div class="col-sm-4 invoice-col">
                    Thông tin phiếu
                    <br>
                    <b>Mã phiếu:</b> PX-{{ str_pad($inventoryExit->id, 5, '0', STR_PAD_LEFT) }}<br>
                    <b>Người lập:</b> {{ $inventoryExit->user->name ?? 'N/A' }}<br>
                    <b>Ghi chú:</b> {{ $inventoryExit->note ?? 'Không có' }}
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->

            <!-- Table row -->
            <div class="row">
                <div class="col-12 table-responsive">
                    <table class="table table-striped table-bordered text-center align-middle">
                        <thead class="table-warning">
                            <tr>
                                <th>STT</th>
                                <th class="text-start">Tên vật tư</th>
                                <th>ĐVT</th>
                                <th>Số lượng</th>
                                <th>Vị trí</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inventoryExit->details as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="text-start">{{ $detail->material->name ?? 'N/A' }}</td>
                                <td>{{ $detail->material->unit->name ?? 'N/A' }}</td>
                                <td class="fw-bold">{{ number_format($detail->quantity, 2) }}</td>
                                <td><span class="badge text-bg-warning">{{ $detail->location ?? 'N/A' }}</span></td>
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
                    <a href="{{ route('inventory-exits.index') }}" class="btn btn-default"><i class="bi bi-arrow-left"></i> Quay lại</a>
                    <button type="button" class="btn btn-warning" onclick="window.print()"><i class="bi bi-printer"></i> In Phiếu</button>
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
