@extends('layouts.auth')

@section('content')
<div class="text-center">
    <p class="login-box-msg">Đăng nhập để bắt đầu phiên làm việc</p>
</div>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="input-group mb-4">
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
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
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Mật khẩu">
        <span class="input-group-text">
            <i class="bi bi-lock"></i>
        </span>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <!--begin::Row-->
    <div class="row align-items-center mt-4">
        <div class="col-12 mb-3">
            <div class="form-check d-flex align-items-center">
                <input class="form-check-input me-2" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} />
                <label class="form-check-label" for="remember"> Ghi nhớ đăng nhập </label>
            </div>
        </div>
        <!-- /.col -->
        <div class="col-12">
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary shadow-sm">Đăng nhập</button>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!--end::Row-->
</form>

<div class="auth-links mt-4">
    @if (Route::has('password.request'))
        <p class="mb-0">
            <a href="{{ route('password.request') }}">Tôi quên mật khẩu</a>
        </p>
    @endif
</div>
@endsection
