@extends('layouts.admin')

@section('title', 'Quản lý Phiếu chuyển kho')
@section('header', 'Danh sách Phiếu chuyển kho')

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Messages -->
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Danh sách Phiếu chuyển</h3>
                <div class="card-tools">
                    <a href="{{ route('inventory-transfers.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Lập phiếu chuyển
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">ID</th>
                            <th>Ngày chuyển</th>
                            <th>Từ Kho</th>
                            <th>Đến Kho</th>
                            <th>Người lập</th>
                            <th>Trạng thái</th>
                            <th style="width: 150px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transfers as $transfer)
                        <tr class="align-middle">
                            <td>#{{ $transfer->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($transfer->date)->format('d/m/Y') }}</td>
                            <td><span class="text-danger fw-bold">{{ $transfer->fromWarehouse->name ?? 'N/A' }}</span></td>
                            <td><span class="text-success fw-bold">{{ $transfer->toWarehouse->name ?? 'N/A' }}</span></td>
                            <td>{{ $transfer->user->name ?? 'N/A' }}</td>
                            <td>
                                @if($transfer->status == 'completed')
                                    <span class="badge text-bg-success">Hoàn thành</span>
                                @elseif($transfer->status == 'pending')
                                    <span class="badge text-bg-warning">Chờ xử lý</span>
                                @else
                                    <span class="badge text-bg-secondary">{{ $transfer->status }}</span>
                                @endif
                            </td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('inventory-transfers.show', $transfer) }}" class="btn btn-sm btn-info text-white">Xem</a>
                                @if(auth()->check() && auth()->user()->hasRole(['Admin tổng', 'Admin kho']))
                                    @if($transfer->status === 'pending')
                                        <form action="{{ route('inventory-transfers.approve', $transfer) }}" method="POST" class="d-inline" onsubmit="return confirm('Duyệt phiếu này? Sẽ trừ kho nguồn và cộng kho đích.');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Duyệt</button>
                                        </form>
                                        <form action="{{ route('inventory-transfers.cancel', $transfer) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn hủy phiếu này?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">Hủy</button>
                                        </form>
                                    @elseif($transfer->status === 'completed')
                                        <form action="{{ route('inventory-transfers.cancel', $transfer) }}" method="POST" class="d-inline" onsubmit="return confirm('CẢNH BÁO: Hủy phiếu đã duyệt sẽ TRẢ LẠI số lượng cho kho nguồn và TRỪ ĐI ở kho đích. Chắc chắn?');">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">Hủy</button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Chưa có dữ liệu phiếu chuyển kho</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                {{ $transfers->links() }}
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
