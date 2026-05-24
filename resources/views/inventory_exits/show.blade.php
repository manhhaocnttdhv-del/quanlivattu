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
                                <td class="fw-bold">{{ number_format($detail->quantity, 2) }}</td>
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
