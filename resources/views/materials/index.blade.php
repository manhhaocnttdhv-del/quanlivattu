@extends('layouts.admin')

@section('title', 'Quản lý Vật tư')
@section('header', 'Danh sách Vật tư')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-header">
                <h3 class="card-title">Danh sách</h3>
                <div class="card-tools d-flex gap-2 align-items-center">
                    @if(auth()->check() && (auth()->user()->role === 'Admin tổng' || auth()->user()->role === 'Admin kho'))
                    <a href="{{ route('materials.template') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-file-earmark-arrow-down"></i> Tải file mẫu
                    </a>
                    <form action="{{ route('materials.import') }}" method="POST" enctype="multipart/form-data" class="d-flex gap-1 mb-0" onsubmit="if(!this.file.value) { alert('Vui lòng chọn file Excel!'); return false; }">
                        @csrf
                        <input type="file" name="file" class="form-control form-control-sm" accept=".xlsx, .xls, .csv" required style="max-width: 200px;">
                        <button type="submit" class="btn btn-sm btn-info text-white"><i class="bi bi-upload"></i> Import</button>
                    </form>
                    <a href="{{ route('materials.export') }}" class="btn btn-sm btn-success">
                        <i class="bi bi-download"></i> Export
                    </a>
                    <a href="{{ route('materials.create') }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg"></i> Thêm mới
                    </a>
                    @endif
                </div>
            </div>

            <!-- Filter Section -->
            <div class="card-body border-bottom pb-3">
                <form method="GET" action="{{ route('materials.index') }}" class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Tìm kiếm</label>
                        <input type="text" name="search" class="form-control form-control-sm" placeholder="Tên vật tư..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Đơn vị tính</label>
                        <select name="unit_id" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            @foreach($units as $unit)
                                <option value="{{ $unit->id }}" {{ request('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small mb-1">Nhóm vật tư</label>
                        <select name="category_id" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @foreach($cat->children as $child)
                                    <option value="{{ $child->id }}" {{ request('category_id') == $child->id ? 'selected' : '' }}>&nbsp;&nbsp;└ {{ $child->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Trạng thái tồn</label>
                        <select name="stock_status" class="form-select form-select-sm">
                            <option value="">-- Tất cả --</option>
                            <option value="below_min" {{ request('stock_status') == 'below_min' ? 'selected' : '' }}>Dưới tối thiểu</option>
                            <option value="above_max" {{ request('stock_status') == 'above_max' ? 'selected' : '' }}>Trên tối đa</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-search"></i> Lọc
                        </button>
                        <a href="{{ route('materials.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-x-lg"></i> Xóa lọc
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th style="width: 10px">#</th>
                            <th>Tên vật tư</th>
                            <th>Nhóm</th>
                            <th>Đơn vị tính</th>
                            <th class="text-end">Giá nhập</th>
                            <th class="text-end">Giá bán</th>
                            <th class="text-end">Lợi nhuận</th>
                            <th>Mô tả</th>
                            <th>Tồn tối thiểu</th>
                            <th>Tồn tối đa</th>
                            @if(auth()->check() && (auth()->user()->role === 'Admin tổng' || auth()->user()->role === 'Admin kho'))
                            <th style="width: 150px">Thao tác</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materials as $material)
                        <tr class="align-middle">
                            <td>{{ $loop->iteration + ($materials->currentPage() - 1) * $materials->perPage() }}</td>
                            <td>{{ $material->name }}</td>
                            <td>
                                @if($material->category)
                                    <span class="badge text-bg-secondary">{{ $material->category->full_name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>{{ $material->unit->name ?? 'N/A' }}</td>
                            <td class="text-end">{{ number_format($material->cost_price) }} ₫</td>
                            <td class="text-end">{{ number_format($material->selling_price) }} ₫</td>
                            <td class="text-end">
                                @if($material->profit > 0)
                                    <span class="text-success fw-bold">+{{ number_format($material->profit) }} ₫</span>
                                    <br><small class="badge text-bg-success">{{ $material->profit_margin }}%</small>
                                @elseif($material->profit < 0)
                                    <span class="text-danger fw-bold">{{ number_format($material->profit) }} ₫</span>
                                    <br><small class="badge text-bg-danger">{{ $material->profit_margin }}%</small>
                                @else
                                    <span class="text-muted">0 ₫</span>
                                @endif
                            </td>
                            <td>{{ $material->description }}</td>
                            <td>{{ $material->min_stock }}</td>
                            <td>{{ $material->max_stock }}</td>
                            @if(auth()->check() && (auth()->user()->role === 'Admin tổng' || auth()->user()->role === 'Admin kho'))
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
                            <td colspan="11" class="text-center">Chưa có dữ liệu</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer clearfix">
                {{ $materials->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
