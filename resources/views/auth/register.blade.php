@extends('layouts.auth')

@section('title', 'Đăng ký')

@section('content')
<p class="login-box-msg">Đăng ký thành viên mới</p>

<form method="POST" action="{{ route('register') }}">
    @csrf

    <div class="input-group mb-3">
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus placeholder="Họ và tên">
        <div class="input-group-text">
            <span class="bi bi-person"></span>
        </div>
        @error('name')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="input-group mb-3">
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
        <div class="input-group-text">
            <span class="bi bi-envelope"></span>
        </div>
        @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="input-group mb-3">
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password" placeholder="Mật khẩu">
        <div class="input-group-text">
            <span class="bi bi-lock-fill"></span>
        </div>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <div class="input-group mb-3">
        <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password" placeholder="Nhập lại mật khẩu">
        <div class="input-group-text">
            <span class="bi bi-lock-fill"></span>
        </div>
    </div>

    <!--begin::Row-->
    <div class="row">
        <!-- /.col -->
        <div class="col-12">
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Đăng ký</button>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!--end::Row-->
</form>

<p class="mb-0 mt-3">
    <a href="{{ route('login') }}" class="text-center"> Tôi đã có tài khoản </a>
</p>
@endsection
