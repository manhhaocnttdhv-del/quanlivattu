@extends('layouts.admin')
@section('title', 'Ca làm việc')
@section('header', 'Quản lý Ca làm việc')

@section('content')
<div class="row">
    <div class="col-12">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title"><i class="bi bi-clock-history"></i> Danh sách Ca làm việc</h3>
                @if(!auth()->user()->isNhanVienKho())
                <a href="{{ route('shifts.create') }}" class="btn btn-sm btn-primary"><i class="bi bi-plus-lg"></i> Thêm ca mới</a>
                @endif
            </div>
            <div class="card-body p-0">
                <table class="table table-striped align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Tên ca</th>
                            <th>Kho</th>
                            <th class="text-center">Giờ bắt đầu</th>
                            <th class="text-center">Giờ kết thúc</th>
                            <th>Ghi chú</th>
                            @if(!auth()->user()->isNhanVienKho())
                            <th class="text-center">Thao tác</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shifts as $shift)
                        <tr>
                            <td>{{ $shifts->firstItem() + $loop->index }}</td>
                            <td class="fw-bold">{{ $shift->name }}</td>
                            <td><span class="badge text-bg-info">{{ $shift->warehouse->name ?? 'N/A' }}</span></td>
                            <td class="text-center"><span class="badge text-bg-success fs-6">{{ $shift->start_time }}</span></td>
                            <td class="text-center"><span class="badge text-bg-warning text-dark fs-6">{{ $shift->end_time }}</span></td>
                            <td class="text-muted small">{{ $shift->note ?? '—' }}</td>
                            @if(!auth()->user()->isNhanVienKho())
                            <td class="text-center">
                                <a href="{{ route('shifts.edit', $shift) }}" class="btn btn-xs btn-warning"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('shifts.destroy', $shift) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Xóa ca {{ $shift->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có ca làm việc nào</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $shifts->links() }}</div>
        </div>
    </div>
</div>
@endsection
