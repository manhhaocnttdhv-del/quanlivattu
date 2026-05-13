<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

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

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    // Management routes restricted to Admins (Create, Store, Edit, Update, Destroy)
    // Defined BEFORE base routes to ensure 'create' is not shadowed by '{material}'
    Route::middleware(['role:Admin tổng,Admin kho'])->group(function () {
        Route::get('materials-export', [MaterialController::class, 'export'])->name('materials.export');
        Route::post('materials-import', [MaterialController::class, 'import'])->name('materials.import');
        Route::resource('materials', MaterialController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
        Route::resource('units', UnitController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
        Route::resource('suppliers', SupplierController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
        Route::post('projects/{project}/materials', [ProjectController::class, 'updateMaterials'])->name('projects.materials.update');
        Route::resource('projects', ProjectController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    });

    // Base routes accessible to all authenticated users (Read)
    Route::resource('materials', MaterialController::class)->only(['index', 'show']);
    Route::resource('units', UnitController::class)->only(['index', 'show']);
    Route::resource('suppliers', SupplierController::class)->only(['index', 'show']);
    Route::resource('projects', ProjectController::class)->only(['index', 'show']);
    
    // Inventory Transactions
    // Approve/Cancel: chỉ Admin tổng và Admin kho
    Route::middleware(['role:Admin tổng,Admin kho'])->group(function () {
        Route::post('inventory-entries/{inventory_entry}/approve', [InventoryEntryController::class, 'approve'])->name('inventory-entries.approve');
        Route::post('inventory-entries/{inventory_entry}/cancel', [InventoryEntryController::class, 'cancel'])->name('inventory-entries.cancel');
        Route::post('inventory-exits/{inventory_exit}/approve', [InventoryExitController::class, 'approve'])->name('inventory-exits.approve');
        Route::post('inventory-exits/{inventory_exit}/cancel', [InventoryExitController::class, 'cancel'])->name('inventory-exits.cancel');
        Route::post('inventory-transfers/{inventory_transfer}/approve', [InventoryTransferController::class, 'approve'])->name('inventory-transfers.approve');
        Route::post('inventory-transfers/{inventory_transfer}/cancel', [InventoryTransferController::class, 'cancel'])->name('inventory-transfers.cancel');
    });
    Route::resource('inventory-entries', InventoryEntryController::class);
    Route::resource('inventory-exits', InventoryExitController::class);
    Route::resource('inventory-transfers', InventoryTransferController::class);
    
    // Inventory Checks (Kiểm kê)
    Route::middleware(['role:Admin tổng,Admin kho'])->group(function () {
        Route::post('inventory-checks/{inventory_check}/approve', [\App\Http\Controllers\InventoryCheckController::class, 'approve'])->name('inventory-checks.approve');
        Route::post('inventory-checks/{inventory_check}/cancel', [\App\Http\Controllers\InventoryCheckController::class, 'cancel'])->name('inventory-checks.cancel');
    });
    Route::resource('inventory-checks', \App\Http\Controllers\InventoryCheckController::class);
    
    // Inventory Alerts
    Route::get('/inventory-alerts', [InventoryAlertController::class, 'index'])->name('inventory-alerts.index')->middleware('role:Admin tổng,Admin kho');
    Route::post('/inventory-alerts/{inventory_alert}/resolve', [InventoryAlertController::class, 'resolve'])->name('inventory-alerts.resolve')->middleware('role:Admin tổng,Admin kho');
    
    // Reports
    Route::get('/reports/inventory', [\App\Http\Controllers\ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/inventory/export-excel', [\App\Http\Controllers\ReportController::class, 'exportExcel'])->name('reports.inventory.export-excel');
    Route::get('/reports/inventory/export-pdf', [\App\Http\Controllers\ReportController::class, 'exportPdf'])->name('reports.inventory.export-pdf');

    // Export Routes
    Route::get('/inventory-entries-export/excel', [InventoryEntryController::class, 'exportExcel'])->name('inventory-entries.export-excel');
    Route::get('/inventory-entries-export/pdf', [InventoryEntryController::class, 'exportPdf'])->name('inventory-entries.export-pdf');
    Route::get('/inventory-exits-export/excel', [InventoryExitController::class, 'exportExcel'])->name('inventory-exits.export-excel');
    Route::get('/inventory-exits-export/pdf', [InventoryExitController::class, 'exportPdf'])->name('inventory-exits.export-pdf');

    // Warehouses: Admin tổng toàn quyền, Admin kho chỉ xem
    Route::middleware(['role:Admin tổng'])->group(function () {
        Route::resource('warehouses', WarehouseController::class)->except(['index', 'show']);
        Route::resource('users', UserController::class);
        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index');
        Route::post('/permissions', [PermissionController::class, 'update'])->name('permissions.update');

    });
    Route::resource('warehouses', WarehouseController::class)->only(['index', 'show']);
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
