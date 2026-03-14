@extends('layouts.admin')

@section('title', 'Nhật ký hoạt động')
@section('header', 'Nhật ký hoạt động hệ thống')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-info">
            <div class="card-header">
                <h3 class="card-title">Lịch sử thay đổi dữ liệu</h3>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Thời gian</th>
                                <th>Người thực hiện</th>
                                <th>Sự kiện</th>
                                <th>Đối tượng</th>
                                <th>Chi tiết thay đổi</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                            <tr>
                                <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                                <td>
                                    <strong>{{ $log->user->name ?? 'System' }}</strong><br>
                                    <small class="text-muted">{{ $log->user->role ?? '-' }}</small>
                                </td>
                                <td>
                                    @php
                                        $badgeClass = match($log->event) {
                                            'created' => 'success',
                                            'updated' => 'info',
                                            'deleted' => 'danger',
                                            'completed' => 'primary',
                                            'cancelled' => 'secondary',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $badgeClass }}">{{ strtoupper($log->event) }}</span>
                                </td>
                                <td>
                                    <small>{{ class_basename($log->auditable_type) }}</small><br>
                                    ID: {{ $log->auditable_id }}
                                </td>
                                <td>
                                    @if($log->old_values || $log->new_values)
                                        <button type="button" class="btn btn-xs btn-outline-info" data-bs-toggle="modal" data-bs-target="#logModal{{ $log->id }}">
                                            Xem chi tiết
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade" id="logModal{{ $log->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Chi tiết thay đổi #{{ $log->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <h6>Dữ liệu cũ:</h6>
                                                                <pre class="bg-light p-2 rounded" style="font-size: 0.8rem;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <h6>Dữ liệu mới:</h6>
                                                                <pre class="bg-light p-2 rounded" style="font-size: 0.8rem;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $log->ip_address }}</small>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Chưa có nhật ký hoạt động nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer clearfix">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
