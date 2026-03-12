@extends('layouts.auth')

@section('content')
<p class="login-box-msg">Đăng nhập để bắt đầu phiên làm việc</p>

<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="input-group mb-3">
        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email">
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
        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Mật khẩu">
        <div class="input-group-text">
            <span class="bi bi-lock-fill"></span>
        </div>
        @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
        @enderror
    </div>

    <!--begin::Row-->
    <div class="row">
        <div class="col-8">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} />
                <label class="form-check-label" for="remember"> Ghi nhớ đăng nhập </label>
            </div>
        </div>
        <!-- /.col -->
        <div class="col-4">
            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Đăng nhập</button>
            </div>
        </div>
        <!-- /.col -->
    </div>
    <!--end::Row-->
</form>

<p class="mb-1 mt-3">
    @if (Route::has('password.request'))
        <a href="{{ route('password.request') }}">Tôi quên mật khẩu</a>
    @endif
</p>
@endsection
