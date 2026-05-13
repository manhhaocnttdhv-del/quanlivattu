@extends('layouts.auth')

@section('title', 'Quên Mật Khẩu')

@section('content')
<div class="text-center">
    <p class="login-box-msg">Nhập email để nhận liên kết khôi phục mật khẩu</p>
</div>

@if (session('status'))
    <div class="alert alert-success" role="alert">
        {{ session('status') }}
    </div>
@endif

<form method="POST" action="{{ route('password.email') }}">
    @csrf

    <div class="input-group mb-4">
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Địa chỉ Email">
        <span class="input-group-text">
            <i class="bi bi-envelope"></i>
        </span>
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary shadow-sm">Gửi Liên Kết Khôi Phục</button>
            </div>
        </div>
    </div>
</form>

<div class="auth-links mt-4">
    <p class="mb-0">
        <a href="{{ route('login') }}">Quay lại Đăng nhập</a>
    </p>
</div>
@endsection
