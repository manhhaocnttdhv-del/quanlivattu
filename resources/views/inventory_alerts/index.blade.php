@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Cảnh báo Tồn kho</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Danh sách vật tư dưới mức an toàn</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Mã vật tư</th>
                            <th>Tên vật tư</th>
                            <th>Tồn tối thiểu</th>
                            <th>Tồn thực tế</th>
                            <th>Trạng thái</th>
                            <th>Ngày cảnh báo</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($alerts as $alert)
                            <tr>
                                <td>{{ $alert->material_id }}</td>
                                <td>{{ $alert->material->name ?? 'N/A' }}</td>
                                <td class="text-primary font-weight-bold">{{ $alert->min_stock_level }}</td>
                                <td class="text-danger font-weight-bold">{{ $alert->current_stock }}</td>
                                <td>
                                    @if($alert->is_resolved)
                                        <span class="badge badge-success">Đã xử lý</span>
                                    @else
                                        <span class="badge badge-warning">Cần nhập hàng</span>
                                    @endif
                                </td>
                                <td>{{ $alert->updated_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if(!$alert->is_resolved)
                                        <form action="{{ route('inventory-alerts.resolve', $alert) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-success" onclick="return confirm('Xác nhận đã xử lý/đặt hàng cho vật tư này?')">
                                                <i class="fas fa-check"></i> Xử lý
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">Tuyệt vời! Không có cảnh báo tồn kho nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $alerts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
