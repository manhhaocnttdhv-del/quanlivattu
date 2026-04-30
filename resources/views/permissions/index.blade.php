@extends('layouts.admin')

@section('title', 'Quản lý phân quyền')
@section('header', 'Quản lý phân quyền')

@push('styles')
<style>
    .permission-table th.role-col { text-align: center; width: 140px; }
    .permission-table td.check-col { text-align: center; vertical-align: middle; }
    .permission-table .group-header {
        background-color: #f0f4ff;
        font-weight: 700;
        font-size: 0.82rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #3b5bdb;
    }
    .perm-desc { font-size: 0.78rem; color: #6c757d; margin-top: 2px; }
    .form-check-input { width: 1.2em; height: 1.2em; cursor: pointer; }
</style>
@endpush

@section('content')

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<form action="{{ route('permissions.update') }}" method="POST">
@csrf
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0"><i class="bi bi-shield-lock me-2"></i>Ma trận phân quyền theo Role</h5>
                    <small class="opacity-75">Tích vào ô để cấp quyền cho từng nhóm người dùng</small>
                </div>
                <button type="submit" class="btn btn-light btn-sm fw-semibold">
                    <i class="bi bi-floppy me-1"></i>Lưu thay đổi
                </button>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0 permission-table">
                        <thead class="table-dark">
                            <tr>
                                <th style="min-width:280px;">Quyền hạn</th>
                                @foreach($roles as $role)
                                    <th class="role-col">{{ $role }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($permissions as $group)
                                <tr>
                                    <td colspan="{{ count($roles) + 1 }}" class="group-header ps-3">
                                        <i class="bi bi-folder2-open me-2"></i>{{ $group['group'] }}
                                    </td>
                                </tr>
                                @foreach($group['permissions'] as $perm)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-semibold">{{ $perm['name'] }}</div>
                                            <div class="perm-desc">{{ $perm['description'] }}</div>
                                        </td>
                                        @foreach($roles as $role)
                                            <td class="check-col">
                                                <input
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    name="perms[{{ $role }}][{{ $perm['name'] }}]"
                                                    value="1"
                                                    title="{{ $role }}: {{ $perm['name'] }}"
                                                    {{ ($perm['by_role'][$role] ?? false) ? 'checked' : '' }}
                                                >
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer d-flex justify-content-between align-items-center">
                <span class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i>
                    Thay đổi áp dụng cho tất cả tài khoản thuộc role tương ứng (trừ tài khoản có quyền riêng).
                </span>
                <button type="submit" class="btn btn-primary btn-sm">
                    <i class="bi bi-floppy me-1"></i>Lưu thay đổi
                </button>
            </div>
        </div>
    </div>
</div>
</form>
@endsection
