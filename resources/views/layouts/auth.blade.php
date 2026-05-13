<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>@yield('title', 'Đăng nhập') | QL Vật Tư</title>

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Fonts-->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!--end::Fonts-->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/styles/overlayscrollbars.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(OverlayScrollbars)-->

    <!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
      crossorigin="anonymous"
    />
    <!--end::Third Party Plugin(Bootstrap Icons)-->

    <!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.css') }}" />
    <!--end::Required Plugin(AdminLTE)-->

    <style>
      body.login-page {
        background-color: #f1f5f9;
        background-image: radial-gradient(#cbd5e1 1px, transparent 1px);
        background-size: 20px 20px;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Inter', sans-serif;
      }
      .login-box {
        width: 450px;
        margin: 0 1rem;
      }
      .login-logo {
        margin-bottom: 2rem;
      }
      .login-logo a {
        color: #1e293b !important;
        text-shadow: none;
        font-weight: 800;
        letter-spacing: -0.5px;
        font-size: 2.2rem;
      }
      .login-logo b {
        font-weight: 900;
        color: #3b82f6;
      }
      .card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }
      .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.08), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
      }
      .login-card-body {
        padding: 3rem 2.5rem !important;
        background: transparent;
      }
      .login-box-msg {
        font-size: 1.1rem;
        color: #64748b;
        font-weight: 500;
        margin-bottom: 2rem;
        padding: 0;
      }
      .form-control {
        border-radius: 10px;
        padding: 0.8rem 1.25rem;
        border: 1.5px solid #e2e8f0;
        background-color: #fff;
        transition: all 0.3s ease;
        font-size: 1rem;
        color: #334155;
      }
      .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
        background-color: #fff;
      }
      /* Fix Chrome Autofill Background */
      input:-webkit-autofill,
      input:-webkit-autofill:hover, 
      input:-webkit-autofill:focus, 
      input:-webkit-autofill:active{
          -webkit-box-shadow: 0 0 0 30px white inset !important;
          -webkit-text-fill-color: #334155 !important;
          transition: background-color 5000s ease-in-out 0s;
      }
      .input-group-text {
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        border-left: none;
        background-color: #fff;
        color: #94a3b8;
        padding-right: 1.25rem;
        transition: all 0.3s ease;
      }
      .input-group:focus-within .input-group-text {
         border-color: #3b82f6;
         color: #3b82f6;
      }
      .input-group .form-control {
         border-right: none;
      }
      .btn-primary {
        background: #3b82f6;
        border: none;
        border-radius: 10px;
        padding: 0.8rem 1.5rem;
        font-weight: 600;
        font-size: 1rem;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        width: 100%;
        margin-top: 1rem;
        box-shadow: 0 4px 6px -1px rgba(59, 130, 246, 0.3);
      }
      .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.4);
        background: #2563eb;
      }
      .form-check-label {
        color: #475569;
        cursor: pointer;
        font-weight: 500;
        margin-top: 0.15rem;
      }
      .form-check-input:checked {
        background-color: #4f46e5;
        border-color: #4f46e5;
      }
      a {
        color: #3b82f6;
        text-decoration: none;
        transition: color 0.3s ease;
        font-weight: 500;
      }
      a:hover {
        color: #2563eb;
        text-decoration: underline;
      }
      .auth-links {
        margin-top: 1.5rem;
        text-align: center;
        font-size: 0.95rem;
      }
      .invalid-feedback {
        font-size: 0.85rem;
        margin-top: 0.4rem;
      }
    </style>
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="login-page bg-body-secondary">
    <div class="login-box">
      <div class="login-logo">
        <a href="{{ url('/') }}"><b>Quản Lý</b>Vật Tư</a>
      </div>
      <!-- /.login-logo -->
      <div class="card">
        <div class="card-body login-card-body">
          @yield('content')
        </div>
        <!-- /.login-card-body -->
      </div>
    </div>
    <!-- /.login-box -->

    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)-->
    <!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('adminlte/js/adminlte.js') }}"></script>
    <!--end::Required Plugin(AdminLTE)-->
  </body>
  <!--end::Body-->
</html>
