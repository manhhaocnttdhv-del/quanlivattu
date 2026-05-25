@extends('layouts.admin')

@section('title', 'Phân loại Vật tư')
@section('header', 'Nhóm Vật tư')

@section('content')
<div class="row">
    <div class="col-12">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Danh sách Nhóm vật tư</h3>
                <div class="card-tools">
                    @if(auth()->check() && auth()->user()->hasRole(['Admin tổng', 'Admin kho']))
                    <a href="{{ route('categories.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Thêm nhóm
                    </a>
                    @endif
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Tên nhóm</th>
                            <th>Phân cấp</th>
                            <th>Mô tả</th>
                            <th>Số vật tư</th>
                            @if(auth()->check() && auth()->user()->hasRole(['Admin tổng', 'Admin kho']))
                            <th style="width: 150px">Thao tác</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr class="align-middle">
                            <td>{{ $loop->iteration + ($categories->currentPage() - 1) * $categories->perPage() }}</td>
                            <td>
                                @if($category->parent_id)
                                    <span class="text-muted ms-3">└—</span> {{ $category->name }}
                                @else
                                    <strong>{{ $category->name }}</strong>
                                @endif
                            </td>
                            <td>
                                @if($category->parent_id)
                                    <span class="badge text-bg-light border text-secondary">Nhóm con của: {{ $category->parent->name }}</span>
                                @else
                                    <span class="badge text-bg-primary">Nhóm gốc</span>
                                @endif
                            </td>
                            <td>{{ $category->description ?? '-' }}</td>
                            <td>
                                @if($category->parent_id)
                                    <span class="badge text-bg-info text-white">{{ $category->materials->count() }}</span>
                                @else
                                    <span class="badge text-bg-secondary">{{ $category->materials->count() + $category->children->sum(fn($c) => $c->materials->count()) }}</span>
                                @endif
                            </td>
                            @if(auth()->check() && auth()->user()->hasRole(['Admin tổng', 'Admin kho']))
                            <td>
                                <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-warning text-white">Sửa</a>
                                <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ $category->parent_id ? 'Bạn có chắc chắn muốn xóa nhóm con này?' : 'Bạn có chắc chắn muốn xóa nhóm gốc này và tất cả các nhóm con?' }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Chưa có nhóm vật tư nào</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
