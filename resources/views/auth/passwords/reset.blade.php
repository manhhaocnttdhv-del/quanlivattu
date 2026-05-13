@extends('layouts.auth')

@section('title', 'Khôi Phục Mật Khẩu')

@section('content')
<div class="text-center">
    <p class="login-box-msg">Tạo mật khẩu mới cho tài khoản của bạn</p>
</div>

<form method="POST" action="{{ route('password.update') }}">
    @csrf

    <input type="hidden" name="token" value="{{ $token }}">

    <div class="input-group mb-4">
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus placeholder="Địa chỉ Email">
        <span class="input-group-text">
            <i class="bi bi-envelope"></i>
        </span>
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="input-group mb-4">
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Mật khẩu mới">
        <span class="input-group-text">
            <i class="bi bi-lock"></i>
        </span>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="input-group mb-4">
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Xác nhận mật khẩu mới">
        <span class="input-group-text">
            <i class="bi bi-lock-fill"></i>
        </span>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary shadow-sm">Khôi Phục Mật Khẩu</button>
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
