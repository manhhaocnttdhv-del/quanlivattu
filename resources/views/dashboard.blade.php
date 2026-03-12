@extends('layouts.admin')

@section('title', 'Bảng điều khiển')
@section('header', 'Bảng điều khiển')

@section('content')
<!--begin::Row-->
<div class="row">
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box text-bg-info">
            <div class="inner">
                <h3>{{ $stats['total_warehouses'] }}</h3>
                <p>Kho hàng</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-building"></i>
            </div>
            <a href="{{ route('warehouses.index') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                Xem chi tiết <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box text-bg-success">
            <div class="inner">
                <h3>{{ $stats['total_materials'] }}</h3>
                <p>Vật tư</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <a href="{{ route('materials.index') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                Xem chi tiết <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box text-bg-warning">
            <div class="inner">
                <h3>{{ $stats['total_entries'] }}</h3>
                <p>Phiếu Nhập</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-cart-plus"></i>
            </div>
            <a href="{{ route('inventory-entries.index') }}" class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                Xem chi tiết <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-3 col-6">
        <!-- small box -->
        <div class="small-box text-bg-danger">
            <div class="inner">
                <h3>{{ $stats['total_exits'] }}</h3>
                <p>Phiếu Xuất</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-cart-dash"></i>
            </div>
            <a href="{{ route('inventory-exits.index') }}" class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                Xem chi tiết <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    <!-- ./col -->
</div>
<!-- /.row -->

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card card-warning card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title">Nhập kho gần đây</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Mã PN</th>
                            <th>Ngày</th>
                            <th>Kho hàng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_entries as $entry)
                        <tr>
                            <td>#{{ $entry->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($entry->date)->format('d/m/Y') }}</td>
                            <td>{{ $entry->warehouse->name ?? 'N/A' }}</td>
                            <td><a href="{{ route('inventory-entries.show', $entry) }}" class="btn btn-xs btn-info"><i class="bi bi-eye text-white"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center">Chưa có giao dịch</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card card-primary card-outline mb-4">
            <div class="card-header">
                <h3 class="card-title">Xuất kho gần đây</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped table-sm">
                    <thead>
                        <tr>
                            <th>Mã PX</th>
                            <th>Ngày</th>
                            <th>Khách hàng</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_exits as $exit)
                        <tr>
                            <td>#{{ $exit->id }}</td>
                            <td>{{ \Carbon\Carbon::parse($exit->date)->format('d/m/Y') }}</td>
                            <td>{{ $exit->customer->name ?? 'N/A' }}</td>
                            <td><a href="{{ route('inventory-exits.show', $exit) }}" class="btn btn-xs btn-info"><i class="bi bi-eye text-white"></i></a></td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center">Chưa có giao dịch</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!--end::Row-->
@endsection
