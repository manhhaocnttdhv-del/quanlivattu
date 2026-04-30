@extends('layouts.admin')

@section('title', 'Chi tiết Công trình')
@section('header', 'Chi tiết Công trình: ' . $project->name)

@section('content')
<div class="row">
    <div class="col-md-4">
        <!-- Thông tin công trình -->
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Thông tin chung</h3>
            </div>
            <div class="card-body">
                <strong><i class="bi bi-geo-alt me-1"></i> Địa chỉ</strong>
                <p class="text-muted">{{ $project->address ?? 'Không có thông tin' }}</p>
                <hr>
                <strong><i class="bi bi-telephone me-1"></i> Số điện thoại</strong>
                <p class="text-muted">{{ $project->phone ?? 'Không có thông tin' }}</p>
                <hr>
                <strong><i class="bi bi-building me-1"></i> Kho quản lý</strong>
                <p class="text-muted">{{ $project->warehouse->name ?? 'Tất cả kho' }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Bảng dự toán vật tư -->
        <div class="card">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item"><a class="nav-link active" href="#boq" data-bs-toggle="tab">Bảng Dự toán Vật tư (BoQ)</a></li>
                </ul>
            </div>
            <div class="card-body">
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

                <div class="tab-content">
                    <div class="tab-pane active" id="boq">
                        <form action="{{ route('projects.materials.update', $project) }}" method="POST">
                            @csrf
                            <table class="table table-bordered table-striped" id="materials-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Vật tư</th>
                                        <th>Đơn vị tính</th>
                                        <th style="width: 250px;">Số lượng Định mức (Dự toán)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($materials as $material)
                                        @php
                                            $pm = $project->projectMaterials->firstWhere('material_id', $material->id);
                                            $qty = $pm ? (float) $pm->estimated_quantity : 0;
                                        @endphp
                                        <tr>
                                            <td>{{ $material->name }}</td>
                                            <td>{{ $material->unit->name ?? '' }}</td>
                                            <td>
                                                <input type="number" step="0.01" min="0" class="form-control" name="materials[{{ $material->id }}]" value="{{ $qty > 0 ? $qty : '' }}" placeholder="Nhập để cấu hình định mức">
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="mt-3 text-end">
                                <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Lưu Thiết Lập Định Mức</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
