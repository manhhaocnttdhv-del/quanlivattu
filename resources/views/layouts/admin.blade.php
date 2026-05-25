<!doctype html>
<html lang="en">
  <!--begin::Head-->
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>@yield('title', 'Quản lý kho') | AdminLTE v4</title>

    <!--begin::Accessibility Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes" />
    <meta name="color-scheme" content="light dark" />
    <!--end::Accessibility Meta Tags-->

    <!--begin::Fonts-->
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
      integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q="
      crossorigin="anonymous"
    />
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

    @yield('styles')
    @stack('styles')
  </head>
  <!--end::Head-->
  <!--begin::Body-->
  <body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
      <!--begin::Header-->
      <nav class="app-header navbar navbar-expand bg-body">
        <!--begin::Container-->
        <div class="container-fluid">
          <!--begin::Start Navbar Links-->
          <ul class="navbar-nav">
            <li class="nav-item">
              <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                <i class="bi bi-list"></i>
              </a>
            </li>
          </ul>
          <!--end::Start Navbar Links-->

          <!--begin::End Navbar Links-->
          <ul class="navbar-nav ms-auto">
            <!--begin::User Menu Dropdown-->
            <li class="nav-item dropdown user-menu">
              <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                <img
                  src="{{ asset('adminlte/assets/img/user2-160x160.jpg') }}"
                  class="user-image rounded-circle shadow"
                  alt="User Image"
                />
                <span class="d-none d-md-inline">{{ Auth::user()->name ?? 'Guest' }}</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                <!--begin::User Image-->
                <li class="user-header text-bg-primary">
                  <img
                    src="{{ asset('adminlte/assets/img/user2-160x160.jpg') }}"
                    class="rounded-circle shadow"
                    alt="User Image"
                  />
                  <p>
                    {{ Auth::user()->name ?? 'Guest' }} - {{ Auth::user()->role ?? 'Unknown' }}
                  </p>
                </li>
                <!--end::User Image-->
                <!--begin::Menu Footer-->
                <li class="user-footer">
                  <a href="#" class="btn btn-default btn-flat">Hồ sơ</a>
                  <a href="{{ route('logout') }}" 
                     onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                     class="btn btn-default btn-flat float-end">
                     Đăng xuất
                  </a>
                  <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                      @csrf
                  </form>
                </li>
                <!--end::Menu Footer-->
              </ul>
            </li>
            <!--end::User Menu Dropdown-->
          </ul>
          <!--end::End Navbar Links-->
        </div>
        <!--end::Container-->
      </nav>
      <!--end::Header-->
      <!--begin::Sidebar-->
      <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
        <!--begin::Sidebar Brand-->
        <div class="sidebar-brand">
          <!--begin::Brand Link-->
          <a href="#" class="brand-link">
            <!--begin::Brand Image-->
            <img
              src="{{ asset('adminlte/assets/img/AdminLTELogo.png') }}"
              alt="AdminLTE Logo"
              class="brand-image opacity-75 shadow"
            />
            <!--end::Brand Image-->
            <!--begin::Brand Text-->
            <span class="brand-text fw-light">QL Vật Tư</span>
            <!--end::Brand Text-->
          </a>
          <!--end::Brand Link-->
        </div>
        <!--end::Sidebar Brand-->
        <!--begin::Sidebar Wrapper-->
        <div class="sidebar-wrapper">
          <nav class="mt-2">
            <!--begin::Sidebar Menu-->
            <ul
              class="nav sidebar-menu flex-column"
              data-lte-toggle="treeview"
              role="navigation"
              data-accordion="false"
            >
              <li class="nav-item">
                <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-speedometer"></i>
                  <p>Dashboard</p>
                </a>
              </li>
              
              <li class="nav-header">QUẢN LÝ</li>
              @can('Xem danh sách kho')
              <li class="nav-item">
                <a href="{{ route('warehouses.index') }}" class="nav-link {{ request()->routeIs('warehouses.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-house-door"></i>
                  <p>Kho hàng</p>
                </a>
              </li>
              @endcan
              
              @can('Xem danh sách vật tư')
              <li class="nav-item {{ request()->routeIs('materials.*') || request()->routeIs('units.*') || request()->routeIs('categories.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('materials.*') || request()->routeIs('units.*') || request()->routeIs('categories.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-box-seam"></i>
                  <p>
                    Vật tư
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        @if(auth()->check() && auth()->user()->isAdminTong())
                        <a href="{{ route('materials.index') }}" class="nav-link {{ request()->routeIs('materials.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-circle"></i>
                            <p>Danh sách Vật tư</p>
                        </a>
                        @elseif(auth()->check())
                        <a href="{{ route('materials.index') }}?kho={{ auth()->user()->warehouse_id }}" class="nav-link {{ request()->routeIs('materials.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-circle"></i>
                            <p>Danh sách Vật tư</p>
                        </a>
                        @endif
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-circle"></i>
                            <p>Nhóm vật tư</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('units.index') }}" class="nav-link {{ request()->routeIs('units.*') ? 'active' : '' }}">
                            <i class="nav-icon bi bi-circle"></i>
                            <p>Đơn vị tính</p>
                        </a>
                    </li>
                </ul>
              </li>
              
              <li class="nav-item {{ request()->routeIs('suppliers.*') || request()->routeIs('projects.*') ? 'menu-open' : '' }}">
                  <a href="#" class="nav-link {{ request()->routeIs('suppliers.*') || request()->routeIs('projects.*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-people"></i>
                      <p>
                          Đối tác
                          <i class="nav-arrow bi bi-chevron-right"></i>
                      </p>
                  </a>
                  <ul class="nav nav-treeview">
                      <li class="nav-item">
                          <a href="{{ route('suppliers.index') }}" class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}">
                              <i class="nav-icon bi bi-circle"></i>
                              <p>Nhà cung cấp</p>
                          </a>
                      </li>
                      <li class="nav-item">
                          <a href="{{ route('projects.index') }}" class="nav-link {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                              <i class="bi bi-circle nav-icon"></i>
                              <p>Công trình (Dự án)</p>
                          </a>
                      </li>
                  </ul>
              </li>
              @endcan
              
              @can('Xem danh sách người dùng')
              <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-person-badge"></i>
                  <p>Người dùng</p>
                </a>
              </li>
              @endcan
              @can('Phân quyền người dùng')
              <li class="nav-item">
                <a href="{{ route('permissions.index') }}" class="nav-link {{ request()->routeIs('permissions.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-shield-lock"></i>
                  <p>Phân quyền</p>
                </a>
              </li>
              @endcan

              {{-- Nhân viên kho --}}
              @canany(['Xem nhân viên kho', 'Quản lý ca làm việc', 'Quản lý lương'])
              <li class="nav-item {{ request()->routeIs('warehouse-staffs.*') || request()->routeIs('shifts.*') || request()->routeIs('shift-logs.*') || request()->routeIs('salaries.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('warehouse-staffs.*') || request()->routeIs('shifts.*') || request()->routeIs('shift-logs.*') || request()->routeIs('salaries.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-person-vcard"></i>
                  <p>
                    Nhân viên Kho
                    <i class="nav-arrow bi bi-chevron-right"></i>
                  </p>
                </a>
                <ul class="nav nav-treeview">
                  @can('Xem nhân viên kho')
                  <li class="nav-item">
                    <a href="{{ route('warehouse-staffs.index') }}" class="nav-link {{ request()->routeIs('warehouse-staffs.*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-circle"></i><p>Danh sách NV</p>
                    </a>
                  </li>
                  @endcan
                  
                  @can('Quản lý ca làm việc')
                  <li class="nav-item">
                    <a href="{{ route('shifts.index') }}" class="nav-link {{ request()->routeIs('shifts.*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-circle"></i><p>Ca làm việc</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('shift-logs.index') }}" class="nav-link {{ request()->routeIs('shift-logs.*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-circle"></i><p>Chấm công</p>
                    </a>
                  </li>
                  @endcan

                  @can('Quản lý lương')
                  <li class="nav-item">
                    <a href="{{ route('salaries.index') }}" class="nav-link {{ request()->routeIs('salaries.*') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-circle"></i><p>Bảng lương</p>
                    </a>
                  </li>
                  @endcan
                </ul>
              </li>
              @endcanany

              @canany(['view-inventory-entries', 'view-inventory-exits', 'view-inventory-transfers'])
              <li class="nav-header">NGHIỆP VỤ</li>
              @can('view-inventory-entries')
              <li class="nav-item">
                <a href="{{ route('inventory-entries.index') }}" class="nav-link {{ request()->routeIs('inventory-entries.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-cart-plus"></i>
                  <p>Nhập kho</p>
                </a>
              </li>
              @endcan
              @can('view-inventory-exits')
              <li class="nav-item">
                <a href="{{ route('inventory-exits.index') }}" class="nav-link {{ request()->routeIs('inventory-exits.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-cart-dash"></i>
                  <p>Xuất kho</p>
                </a>
              </li>
              @endcan
              @can('view-inventory-transfers')
              <li class="nav-item">
                <a href="{{ route('inventory-transfers.index') }}" class="nav-link {{ request()->routeIs('inventory-transfers.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-arrow-left-right"></i>
                  <p>Chuyển kho</p>
                </a>
              </li>
              @endcan
              @endcanany

              @can('view-inventory-checks')
              <li class="nav-header">KIỂM KÊ</li>
              <li class="nav-item">
                <a href="{{ route('inventory-checks.index') }}" class="nav-link {{ request()->routeIs('inventory-checks.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-list-check"></i>
                  <p>Kiểm kê kho</p>
                </a>
              </li>
              @endcan

              @can('Xem cảnh báo tồn kho')
              <li class="nav-header">CẢNH BÁO</li>
              <li class="nav-item">
                <a href="{{ route('inventory-alerts.index') }}" class="nav-link {{ request()->routeIs('inventory-alerts.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-exclamation-triangle text-danger"></i>
                  <p>Cảnh báo tồn kho</p>
                </a>
              </li>
              @endcan

              @can('Xem báo cáo tồn kho')
              <li class="nav-header">BÁO CÁO</li>
              <li class="nav-item {{ request()->routeIs('reports.*') ? 'menu-open' : '' }}">
                <a href="#" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-file-earmark-bar-graph"></i>
                  <p>Báo cáo<i class="nav-arrow bi bi-chevron-right"></i></p>
                </a>
                <ul class="nav nav-treeview">
                  <li class="nav-item">
                    <a href="{{ route('reports.inventory') }}" class="nav-link {{ request()->routeIs('reports.inventory') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-circle"></i><p>Tồn kho hiện tại</p>
                    </a>
                  </li>
                  <li class="nav-item">
                    <a href="{{ route('reports.material-history') }}" class="nav-link {{ request()->routeIs('reports.material-history') ? 'active' : '' }}">
                      <i class="nav-icon bi bi-circle"></i><p>Lịch sử vật tư</p>
                    </a>
                  </li>
                </ul>
              </li>
              @endcan

              <li class="nav-item">
                <hr class="mx-2 my-1 text-white-50">
              </li>

              @can('Phân quyền người dùng')
              <li class="nav-item">
                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                  <i class="nav-icon bi bi-gear-fill"></i>
                  <p>Cài đặt chung</p>
                </a>
              </li>
              @endcan

              <li class="nav-item">
                <a href="{{ route('logout') }}" 
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                   class="nav-link text-danger">
                  <i class="nav-icon bi bi-box-arrow-right"></i>
                  <p>Đăng xuất</p>
                </a>
              </li>
            </ul>
            <!--end::Sidebar Menu-->
          </nav>
        </div>
        <!--end::Sidebar Wrapper-->
      </aside>
      <!--end::Sidebar-->
      <!--begin::App Main-->
      <main class="app-main">
        <!--begin::App Content Header-->
        <div class="app-content-header">
          <!--begin::Container-->
          <div class="container-fluid">
            <!--begin::Row-->
            <div class="row">
              <div class="col-sm-6">
                <h3 class="mb-0">@yield('header', 'Dashboard')</h3>
              </div>
              <div class="col-sm-6">
                <ol class="breadcrumb float-sm-end">
                  <li class="breadcrumb-item"><a href="#">Home</a></li>
                  <li class="breadcrumb-item active" aria-current="page">@yield('header', 'Dashboard')</li>
                </ol>
              </div>
            </div>
            <!--end::Row-->
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content Header-->
        <!--begin::App Content-->
        <div class="app-content">
          <!--begin::Container-->
          <div class="container-fluid">
            @yield('content')
          </div>
          <!--end::Container-->
        </div>
        <!--end::App Content-->
      </main>
      <!--end::App Main-->
      <!--begin::Footer-->
      <footer class="app-footer">
        <!--begin::To the end-->
        <div class="float-end d-none d-sm-inline">Anything you want</div>
        <!--end::To the end-->
        <!--begin::Copyright-->
        <strong>
          Copyright &copy; 2014-2024&nbsp;
          <a href="https://adminlte.io" class="text-decoration-none">AdminLTE.io</a>.
        </strong>
        All rights reserved.
        <!--end::Copyright-->
      </footer>
      <!--end::Footer-->
    </div>
    <!--end::App Wrapper-->

    <!-- Low Stock Alert Modal -->
    <div class="modal fade" id="lowStockAlertModal" tabindex="-1" aria-labelledby="lowStockAlertModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="lowStockAlertModalLabel">
              <i class="bi bi-exclamation-triangle-fill me-2"></i> Cảnh Báo Tồn Kho Dưới Định Mức!
            </h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p>Hệ thống ghi nhận một số vật tư đã giảm xuống dưới mức tối thiểu quy định. Vui lòng kiểm tra và lên kế hoạch nhập hàng:</p>
            <div class="table-responsive">
              <table class="table table-striped table-bordered text-center align-middle">
                <thead class="table-danger">
                  <tr>
                    <th>Tên vật tư</th>
                    <th>Kho hàng</th>
                    <th>Tồn kho hiện tại</th>
                    <th>Định mức tối thiểu</th>
                  </tr>
                </thead>
                <tbody id="lowStockModalBody">
                  <!-- Dynamic rows -->
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="button" class="btn btn-warning" id="btnMuteAlerts">Tắt thông báo</button>
            @can('Xem cảnh báo tồn kho')
            <a href="{{ route('inventory-alerts.index') }}" class="btn btn-danger">Xem chi tiết cảnh báo</a>
            @endcan
          </div>
        </div>
      </div>
    </div>
    <!--begin::Script-->
    <!--begin::Third Party Plugin(OverlayScrollbars)-->
    <script
      src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.11.0/browser/overlayscrollbars.browser.es6.min.js"
      crossorigin="anonymous"
    ></script>
    <!--end::Third Party Plugin(OverlayScrollbars)-->
    <!--begin::Required Plugin(popperjs for Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
      integrity="sha384-I7E8VVD/emGFpoT6W8pu68c/PN9GofQvuzKMRqM+OrBnVFBL6DOitfPri4tjfHxa"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(popperjs for Bootstrap 5)-->
    <!--begin::Required Plugin(Bootstrap 5)-->
    <script
      src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"
      integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy"
      crossorigin="anonymous"
    ></script>
    <!--end::Required Plugin(Bootstrap 5)-->
    <!--begin::Required Plugin(AdminLTE)-->
    <script src="{{ asset('adminlte/js/adminlte.js') }}"></script>
    <!--end::Required Plugin(AdminLTE)-->
    <!--begin::OverlayScrollbars Configure-->
    <script>
      const SELECTOR_SIDEBAR_WRAPPER = '.sidebar-wrapper';
      const Default = {
        scrollbarTheme: 'os-theme-light',
        scrollbarAutoHide: 'leave',
        scrollbarClickScroll: true,
      };
      document.addEventListener('DOMContentLoaded', function () {
        const sidebarWrapper = document.querySelector(SELECTOR_SIDEBAR_WRAPPER);
        if (sidebarWrapper && typeof OverlayScrollbarsGlobal?.OverlayScrollbars !== 'undefined') {
          OverlayScrollbarsGlobal.OverlayScrollbars(sidebarWrapper, {
            scrollbars: {
              theme: Default.scrollbarTheme,
              autoHide: Default.scrollbarAutoHide,
              clickScroll: Default.scrollbarClickScroll,
            },
          });
        }
      });
    </script>
    <!--end::OverlayScrollbars Configure-->
    <!-- Global Session Toast Notifications -->
    @if(session('success') || session('error') || session('status'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
          const Toast = Swal.mixin({
              toast: true,
              position: 'top-end',
              showConfirmButton: false,
              timer: 4000,
              timerProgressBar: true,
              didOpen: (toast) => {
                  toast.addEventListener('mouseenter', Swal.stopTimer)
                  toast.addEventListener('mouseleave', Swal.resumeTimer)
              }
          });

          @if(session('success'))
          Toast.fire({
              icon: 'success',
              title: {!! json_encode(session('success')) !!}
          });
          @endif

          @if(session('error'))
          Toast.fire({
              icon: 'error',
              title: {!! json_encode(session('error')) !!}
          });
          @endif

          @if(session('status'))
          Toast.fire({
              icon: 'info',
              title: {!! json_encode(session('status')) !!}
          });
          @endif
      });
    </script>
    @endif

    @yield('scripts')
    @stack('scripts')
    
    <!-- Low Stock Alert Script -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
          const alertModalEl = document.getElementById('lowStockAlertModal');
          const muteBtn = document.getElementById('btnMuteAlerts');

          if (muteBtn) {
              muteBtn.addEventListener('click', function() {
                  localStorage.setItem('muteLowStockAlerts', 'true');
                  if (alertModalEl && typeof bootstrap !== 'undefined') {
                      const modal = bootstrap.Modal.getInstance(alertModalEl);
                      if (modal) {
                          modal.hide();
                      }
                  }
              });
          }

          function checkLowStock() {
              if (localStorage.getItem('muteLowStockAlerts') === 'true') {
                  return;
              }

              fetch('{{ route("api.low-stock-alerts") }}')
                  .then(response => response.json())
                  .then(data => {
                      if (data.alerts && data.alerts.length > 0) {
                          const tbody = document.getElementById('lowStockModalBody');
                          if (tbody) {
                              tbody.innerHTML = '';
                              data.alerts.forEach(item => {
                                  const row = `
                                      <tr>
                                          <td class="text-start fw-bold">${item.material_name}</td>
                                          <td>${item.warehouse_name}</td>
                                          <td class="text-danger fw-bold">${parseFloat(item.stock).toLocaleString('vi-VN')} ${item.unit}</td>
                                          <td class="text-secondary">${parseFloat(item.min_stock).toLocaleString('vi-VN')} ${item.unit}</td>
                                      </tr>
                                  `;
                                  tbody.insertAdjacentHTML('beforeend', row);
                              });
                              
                              // Show modal using Bootstrap instance
                              if (alertModalEl && typeof bootstrap !== 'undefined') {
                                  let modal = bootstrap.Modal.getInstance(alertModalEl);
                                  if (!modal) {
                                      modal = new bootstrap.Modal(alertModalEl);
                                  }
                                  modal.show();
                              }
                          }
                      }
                  })
                  .catch(error => console.error('Error fetching low stock alerts:', error));
          }

          // Initial check on load
          checkLowStock();

          // Check every 1 minute (60000 ms)
          setInterval(checkLowStock, 60000);
      });
    </script>
    <!--end::Script-->
  </body>
  <!--end::Body-->
</html>
