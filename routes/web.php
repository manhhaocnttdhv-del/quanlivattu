<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryEntryController;
use App\Http\Controllers\InventoryExitController;
use App\Http\Controllers\InventoryTransferController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\InventoryAlertController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryCheckController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AppSettingController;
use App\Http\Controllers\WarehouseStaffController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ShiftLogController;
use App\Http\Controllers\SalaryController;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // ──────────────────────────────────────────────────
    // 1. WRITE / MANAGEMENT ROUTES (Defined first to prevent shadowing)
    // ──────────────────────────────────────────────────

    Route::middleware(['can:Thêm / Sửa / Xóa vật tư'])->group(function () {
        Route::get('materials-export', [MaterialController::class, 'export'])->name('materials.export');
        Route::get('materials-template', [MaterialController::class, 'downloadTemplate'])->name('materials.template');
        Route::post('materials-import', [MaterialController::class, 'import'])->name('materials.import');
        Route::post('materials/update-stock', [MaterialController::class, 'updateStock'])->name('materials.update-stock');
        Route::resource('materials', MaterialController::class)->except(['index', 'show']);
        Route::resource('categories', CategoryController::class)->except(['index']);
    });

    Route::middleware(['can:Quản lý đơn vị tính'])->group(function () {
        Route::resource('units', UnitController::class)->except(['index', 'show']);
    });

    Route::middleware(['can:Quản lý nhà cung cấp'])->group(function () {
        Route::resource('suppliers', SupplierController::class)->except(['index', 'show']);
    });

    Route::middleware(['can:Quản lý khách hàng'])->group(function () {
        Route::post('projects/{project}/materials', [ProjectController::class, 'updateMaterials'])->name('projects.materials.update');
        Route::resource('projects', ProjectController::class)->except(['index', 'show']);
    });

    Route::middleware(['can:Thêm / Sửa / Xóa kho'])->group(function () {
        Route::resource('warehouses', WarehouseController::class)->except(['index', 'show']);
    });

    Route::middleware(['can:Thêm / Sửa / Xóa người dùng'])->group(function () {
        Route::resource('users', UserController::class)->except(['index', 'show']);
    });

    // ──────────────────────────────────────────────────
    // 2. READ / BASE ROUTES (Defined after write routes to avoid shadowing)
    // ──────────────────────────────────────────────────

    Route::middleware(['can:Xem danh sách vật tư'])->group(function () {
        Route::get('materials', [MaterialController::class, 'index'])->name('materials.index');
        Route::get('materials/{material}', [MaterialController::class, 'show'])->name('materials.show');
        Route::get('units', [UnitController::class, 'index'])->name('units.index');
        Route::get('units/{unit}', [UnitController::class, 'show'])->name('units.show');
        Route::get('categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
        Route::get('projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
    });

    Route::middleware(['can:Xem danh sách kho'])->group(function () {
        Route::get('warehouses', [WarehouseController::class, 'index'])->name('warehouses.index');
        Route::get('warehouses/{warehouse}', [WarehouseController::class, 'show'])->name('warehouses.show');
    });

    Route::middleware(['can:Xem danh sách người dùng'])->group(function () {
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');
    });

    // ──────────────────────────────────────────────────
    // 3. INVENTORY OPERATIONS & OTHER ROUTES
    // ──────────────────────────────────────────────────

    // Inventory Entries
    Route::middleware(['can:Tạo phiếu nhập kho'])->group(function () {
        Route::get('inventory-entries/create', [InventoryEntryController::class, 'create'])->name('inventory-entries.create');
        Route::post('inventory-entries', [InventoryEntryController::class, 'store'])->name('inventory-entries.store');
    });
    Route::middleware(['can:view-inventory-entries'])->group(function () {
        Route::get('inventory-entries', [InventoryEntryController::class, 'index'])->name('inventory-entries.index');
        Route::get('inventory-entries/{inventory_entry}', [InventoryEntryController::class, 'show'])->name('inventory-entries.show');
    });
    Route::middleware(['can:Duyệt / Hủy phiếu nhập kho'])->group(function () {
        Route::post('inventory-entries/{inventory_entry}/approve', [InventoryEntryController::class, 'approve'])->name('inventory-entries.approve');
        Route::post('inventory-entries/{inventory_entry}/cancel', [InventoryEntryController::class, 'cancel'])->name('inventory-entries.cancel');
    });
    Route::middleware(['can:Xuất Excel / PDF nhập kho'])->group(function () {
        Route::get('inventory-entries-export/excel', [InventoryEntryController::class, 'exportExcel'])->name('inventory-entries.export-excel');
        Route::get('inventory-entries-export/pdf', [InventoryEntryController::class, 'exportPdf'])->name('inventory-entries.export-pdf');
    });

    // Inventory Exits
    Route::middleware(['can:Tạo phiếu xuất kho'])->group(function () {
        Route::get('inventory-exits/create', [InventoryExitController::class, 'create'])->name('inventory-exits.create');
        Route::post('inventory-exits', [InventoryExitController::class, 'store'])->name('inventory-exits.store');
    });
    Route::middleware(['can:view-inventory-exits'])->group(function () {
        Route::get('inventory-exits', [InventoryExitController::class, 'index'])->name('inventory-exits.index');
        Route::get('inventory-exits/{inventory_exit}', [InventoryExitController::class, 'show'])->name('inventory-exits.show');
    });
    Route::middleware(['can:Duyệt / Hủy phiếu xuất kho'])->group(function () {
        Route::post('inventory-exits/{inventory_exit}/approve', [InventoryExitController::class, 'approve'])->name('inventory-exits.approve');
        Route::post('inventory-exits/{inventory_exit}/cancel', [InventoryExitController::class, 'cancel'])->name('inventory-exits.cancel');
    });
    Route::middleware(['can:Xuất Excel / PDF xuất kho'])->group(function () {
        Route::get('inventory-exits-export/excel', [InventoryExitController::class, 'exportExcel'])->name('inventory-exits.export-excel');
        Route::get('inventory-exits-export/pdf', [InventoryExitController::class, 'exportPdf'])->name('inventory-exits.export-pdf');
    });

    // Inventory Transfers
    Route::middleware(['can:Tạo phiếu chuyển kho'])->group(function () {
        Route::get('inventory-transfers/create', [InventoryTransferController::class, 'create'])->name('inventory-transfers.create');
        Route::post('inventory-transfers', [InventoryTransferController::class, 'store'])->name('inventory-transfers.store');
    });
    Route::middleware(['can:view-inventory-transfers'])->group(function () {
        Route::get('inventory-transfers', [InventoryTransferController::class, 'index'])->name('inventory-transfers.index');
        Route::get('inventory-transfers/{inventory_transfer}', [InventoryTransferController::class, 'show'])->name('inventory-transfers.show');
    });
    Route::middleware(['can:Duyệt / Hủy phiếu chuyển kho'])->group(function () {
        Route::post('inventory-transfers/{inventory_transfer}/approve', [InventoryTransferController::class, 'approve'])->name('inventory-transfers.approve');
        Route::post('inventory-transfers/{inventory_transfer}/cancel', [InventoryTransferController::class, 'cancel'])->name('inventory-transfers.cancel');
    });

    // Inventory Checks
    Route::middleware(['can:Tạo phiếu kiểm kê'])->group(function () {
        Route::get('inventory-checks/create', [InventoryCheckController::class, 'create'])->name('inventory-checks.create');
        Route::post('inventory-checks', [InventoryCheckController::class, 'store'])->name('inventory-checks.store');
    });
    Route::middleware(['can:view-inventory-checks'])->group(function () {
        Route::get('inventory-checks', [InventoryCheckController::class, 'index'])->name('inventory-checks.index');
        Route::get('inventory-checks/{inventory_check}', [InventoryCheckController::class, 'show'])->name('inventory-checks.show');
    });
    Route::middleware(['can:Duyệt / Hủy phiếu kiểm kê'])->group(function () {
        Route::post('inventory-checks/{inventory_check}/approve', [InventoryCheckController::class, 'approve'])->name('inventory-checks.approve');
        Route::post('inventory-checks/{inventory_check}/cancel', [InventoryCheckController::class, 'cancel'])->name('inventory-checks.cancel');
    });

    // Inventory Alerts
    Route::get('/api/low-stock-alerts', [InventoryAlertController::class, 'getLowStockAlerts'])->name('api.low-stock-alerts');
    Route::middleware(['can:Xem cảnh báo tồn kho'])->group(function () {
        Route::get('/inventory-alerts', [InventoryAlertController::class, 'index'])->name('inventory-alerts.index');
    });
    Route::middleware(['can:Xử lý cảnh báo tồn kho'])->group(function () {
        Route::post('/inventory-alerts/{inventory_alert}/resolve', [InventoryAlertController::class, 'resolve'])->name('inventory-alerts.resolve');
    });

    // Reports
    Route::middleware(['can:Xem báo cáo tồn kho'])->group(function () {
        Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    });
    Route::middleware(['can:Xuất báo cáo Excel / PDF'])->group(function () {
        Route::get('/reports/inventory/export-excel', [ReportController::class, 'exportExcel'])->name('reports.inventory.export-excel');
        Route::get('/reports/inventory/export-pdf', [ReportController::class, 'exportPdf'])->name('reports.inventory.export-pdf');
    });

    // ──────────────────────────────────────────────────
    // 4. NEW FEATURES ROUTES
    // ──────────────────────────────────────────────────

    // Cài đặt chung (Admin tổng)
    Route::middleware(['can:Phân quyền người dùng'])->group(function () {
        Route::get('/settings', [AppSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [AppSettingController::class, 'update'])->name('settings.update');
    });

    // Duyệt / Xóa từng dòng phiếu xuất
    Route::middleware(['can:Duyệt / Hủy phiếu xuất kho'])->group(function () {
        Route::post('inventory-exits/{inventoryExit}/details/{detail}/approve', [InventoryExitController::class, 'approveDetail'])->name('inventory-exits.details.approve');
        Route::delete('inventory-exits/{inventoryExit}/details/{detail}', [InventoryExitController::class, 'removeDetail'])->name('inventory-exits.details.remove');
    });

    // Duyệt / Xóa từng dòng phiếu nhập
    Route::middleware(['can:Duyệt / Hủy phiếu nhập kho'])->group(function () {
        Route::post('inventory-entries/{inventoryEntry}/details/{detail}/approve', [InventoryEntryController::class, 'approveDetail'])->name('inventory-entries.details.approve');
        Route::delete('inventory-entries/{inventoryEntry}/details/{detail}', [InventoryEntryController::class, 'removeDetail'])->name('inventory-entries.details.remove');
    });

    // Nhân viên kho
    Route::middleware(['can:Xem nhân viên kho'])->group(function () {
        Route::get('warehouse-staffs', [WarehouseStaffController::class, 'index'])->name('warehouse-staffs.index');
    });

    Route::middleware(['can:Thêm / Sửa / Xóa nhân viên kho'])->group(function () {
        Route::get('warehouse-staffs/create', [WarehouseStaffController::class, 'create'])->name('warehouse-staffs.create');
        Route::post('warehouse-staffs', [WarehouseStaffController::class, 'store'])->name('warehouse-staffs.store');
    });

    Route::middleware(['can:Xem nhân viên kho'])->group(function () {
        Route::get('warehouse-staffs/{warehouseStaff}', [WarehouseStaffController::class, 'show'])->name('warehouse-staffs.show');
    });

    Route::middleware(['can:Thêm / Sửa / Xóa nhân viên kho'])->group(function () {
        Route::get('warehouse-staffs/{warehouseStaff}/edit', [WarehouseStaffController::class, 'edit'])->name('warehouse-staffs.edit');
        Route::put('warehouse-staffs/{warehouseStaff}', [WarehouseStaffController::class, 'update'])->name('warehouse-staffs.update');
        Route::delete('warehouse-staffs/{warehouseStaff}', [WarehouseStaffController::class, 'destroy'])->name('warehouse-staffs.destroy');
    });

    // Ca làm việc
    Route::middleware(['can:Quản lý ca làm việc'])->group(function () {
        Route::resource('shifts', ShiftController::class)->except(['show']);
        Route::get('shift-logs', [ShiftLogController::class, 'index'])->name('shift-logs.index');
        Route::get('shift-logs/create', [ShiftLogController::class, 'create'])->name('shift-logs.create');
        Route::post('shift-logs', [ShiftLogController::class, 'store'])->name('shift-logs.store');
        Route::get('shift-logs/bulk', [ShiftLogController::class, 'bulkCreate'])->name('shift-logs.bulk');
        Route::post('shift-logs/bulk', [ShiftLogController::class, 'bulkStore'])->name('shift-logs.bulk-store');
        Route::delete('shift-logs/{shiftLog}', [ShiftLogController::class, 'destroy'])->name('shift-logs.destroy');
    });

    // Lương
    Route::middleware(['can:Quản lý lương'])->group(function () {
        Route::get('salaries', [SalaryController::class, 'index'])->name('salaries.index');
        Route::post('salaries/generate', [SalaryController::class, 'generate'])->name('salaries.generate');
        Route::get('salaries/{salary}', [SalaryController::class, 'show'])->name('salaries.show');
        Route::put('salaries/{salary}', [SalaryController::class, 'update'])->name('salaries.update');
        Route::post('salaries/{salary}/confirm', [SalaryController::class, 'confirm'])->name('salaries.confirm');
        Route::post('salaries/{salary}/pay', [SalaryController::class, 'pay'])->name('salaries.pay');
    });

    // Báo cáo lịch sử vật tư
    Route::middleware(['can:Xem báo cáo tồn kho'])->group(function () {
        Route::get('/reports/material-history', [ReportController::class, 'materialHistory'])->name('reports.material-history');
    });

    // Users & Permissions (Matrix)
    Route::middleware(['can:Phân quyền người dùng'])->group(function () {
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('/permissions', [PermissionController::class, 'update'])->name('permissions.update');
    });
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
