@extends('layouts.admin')

@section('title', 'Báo cáo Tồn kho')
@section('header', 'Báo cáo Tồn kho')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card card-outline card-success">
            <div class="card-header">
                <h3 class="card-title">Tồn kho hiện tại</h3>
                <div class="card-tools d-print-none">
                    <button type="button" class="btn btn-sm btn-success" onclick="window.print()">
                        <i class="bi bi-printer"></i> In báo cáo
                    </button>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
                <table class="table table-bordered table-striped text-center align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>STT</th>
                            <th class="text-start">Tên Vật tư</th>
                            <th>ĐVT</th>
                            <th class="text-primary">Tổng Nhập</th>
                            <th class="text-danger">Tổng Xuất</th>
                            <th class="text-success fs-5">Tồn Cuối</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materials as $material)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td class="text-start fw-bold">{{ $material->name }}</td>
                            <td>{{ $material->unit->name ?? 'N/A' }}</td>
                            <td class="text-primary fw-bold">{{ number_format($material->total_in, 2) }}</td>
                            <td class="text-danger fw-bold">{{ number_format($material->total_out, 2) }}</td>
                            <td class="text-success fs-5 fw-bolder">{{ number_format($material->stock, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Hệ thống chưa có dữ liệu vật tư</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection

@section('styles')
<style>
    @media print {
        body { font-size: 14pt; }
        .app-sidebar, .app-header, .app-footer, .app-title { display: none !important; }
        .app-main { margin-left: 0 !important; width: 100% !important; padding: 0;}
    }
</style>
@endsection
