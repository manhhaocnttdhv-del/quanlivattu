@extends('layouts.admin')

@section('title', 'Chi tiết Phiếu Kiểm kê')
@section('header', 'Chi tiết Phiếu Kiểm kê')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-primary card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title">
                    Phiếu Phát Kê #{{ $inventoryCheck->id }}
                    @if($inventoryCheck->status == 'completed')
                        <span class="badge text-bg-success ms-2">Đã xử lý</span>
                    @elseif($inventoryCheck->status == 'pending')
                        <span class="badge text-bg-warning ms-2">Chờ xử lý</span>
                    @else
                        <span class="badge text-bg-secondary ms-2">{{ $inventoryCheck->status }}</span>
                    @endif
                </h3>
            </div>
            
            <div class="card-body">
                <div class="row border-bottom pb-3 mb-3">
                    <div class="col-md-3"><strong>Ngày lập:</strong> {{ \Carbon\Carbon::parse($inventoryCheck->date)->format('d/m/Y') }}</div>
                    <div class="col-md-3"><strong>Kho hàng:</strong> {{ $inventoryCheck->warehouse->name ?? '' }}</div>
                    <div class="col-md-3"><strong>Người lập:</strong> {{ $inventoryCheck->user->name ?? '' }}</div>
                    <div class="col-md-3"><strong>Ghi chú:</strong> {{ $inventoryCheck->note ?: 'Không có' }}</div>
                </div>

                <table class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Mã Vật tư</th>
                            <th>Tên Vật tư</th>
                            <th>ĐVT</th>
                            <th class="text-center">Tồn Máy tính</th>
                            <th class="text-center text-primary">Tồn Thực tế</th>
                            <th class="text-center">Độ lệch</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventoryCheck->details as $index => $detail)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $detail->material->id }}</td>
                            <td>{{ $detail->material->name }}</td>
                            <td>{{ $detail->material->unit->name ?? '' }}</td>
                            <td class="text-center">{{ number_format($detail->system_stock, 2) }}</td>
                            <td class="text-center text-primary fw-bold">{{ number_format($detail->actual_stock, 2) }}</td>
                            <td class="text-center fw-bold">
                                @if($detail->variance > 0)
                                    <span class="text-success">+{{ number_format($detail->variance, 2) }}</span>
                                @elseif($detail->variance < 0)
                                    <span class="text-danger">{{ number_format($detail->variance, 2) }}</span>
                                @else
                                    <span class="text-secondary">0</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer text-end">
                <a href="{{ route('inventory-checks.index') }}" class="btn btn-secondary">Quay lại danh sách</a>
                @if($inventoryCheck->status === 'pending')
                    <form action="{{ route('inventory-checks.approve', $inventoryCheck) }}" method="POST" class="d-inline ms-2" onsubmit="return confirm('Duyệt kết quả và TỰ ĐỘNG ĐIỀU CHỈNH KHO (sẽ sinh ra Phiếu nhập/xuất bù trừ)?');">
                        @csrf
                        <button type="submit" class="btn btn-success">Duyệt thay đổi (Điều chỉnh kho)</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
