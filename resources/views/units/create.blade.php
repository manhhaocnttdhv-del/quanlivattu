@extends('layouts.admin')

@section('title', 'Thêm mới Đơn vị tính')
@section('header', 'Thêm mới Đơn vị tính')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card card-primary card-outline mb-4">
            <div class="card-header">
                <div class="card-title">Nhập thông tin</div>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form action="{{ route('units.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Tên đơn vị tính <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!-- /.card-body -->

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Lưu lại</button>
                    <a href="{{ route('units.index') }}" class="btn btn-default float-end">Hủy bỏ</a>
                </div>
            </form>
        </div>
        <!-- /.card -->
    </div>
</div>
@endsection
