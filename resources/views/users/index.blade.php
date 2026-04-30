@extends('layouts.admin')

@section('title', 'Quản lý Người dùng')
@section('header', 'Danh sách Người dùng')

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
                <h3 class="card-title">Danh sách</h3>
                <div class="card-tools">
                    <a href="{{ route('users.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Thêm mới
                    </a>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Họ và tên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Kho quản lý/làm việc</th>
                            <th>Trạng thái</th>
                            <th style="width: 210px">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr class="align-middle">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role == 'Admin tổng')
                                    <span class="badge text-bg-danger">{{ $user->role }}</span>
                                @elseif($user->role == 'Admin kho')
                                    <span class="badge text-bg-warning">{{ $user->role }}</span>
                                @else
                                    <span class="badge text-bg-info">{{ $user->role }}</span>
                                @endif
                            </td>
                            <td>{{ $user->warehouse->name ?? 'N/A' }}</td>
                            <td>
                                @if($user->status == 'active')
                                    <span class="badge text-bg-success">Đang hoạt động</span>
                                @else
                                    <span class="badge text-bg-secondary">Đã khóa</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">Sửa</a>

                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa user này không?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" {{ $user->id == auth()->id() ? 'disabled' : '' }}>Xóa</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Chưa có dữ liệu</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                {{ $users->links() }}
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
