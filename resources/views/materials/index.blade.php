@extends('layouts.admin')

@section('title', 'Quản lý Vật tư')
@section('header', 'Danh sách Vật tư')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Danh sách</h3>
                <div class="card-tools">
                    @if(auth()->check() && auth()->user()->role === 'Admin tổng')
                    <a href="{{ route('materials.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Thêm mới
                    </a>
                    @endif
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Tên vật tư</th>
                            <th>Đơn vị tính</th>
                            <th>Mô tả</th>
                            <th>Tồn kho tối thiểu</th>
                            <th>Tồn kho tối đa</th>
                            @if(auth()->check() && auth()->user()->role === 'Admin tổng')
                            <th style="width: 150px">Thao tác</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materials as $material)
                        <tr class="align-middle">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $material->name }}</td>
                            <td>{{ $material->unit->name ?? 'N/A' }}</td>
                            <td>{{ $material->description }}</td>
                            <td>{{ $material->min_stock }}</td>
                            <td>{{ $material->max_stock }}</td>
                            @if(auth()->check() && auth()->user()->role === 'Admin tổng')
                            <td>
                                <a href="{{ route('materials.edit', $material) }}" class="btn btn-sm btn-warning">Sửa</a>
                                <form action="{{ route('materials.destroy', $material) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </td>
                            @endif
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
                {{ $materials->links() }}
            </div>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
