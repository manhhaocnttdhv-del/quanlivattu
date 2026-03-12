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
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryEntryController;
use App\Http\Controllers\InventoryExitController;
use App\Http\Controllers\InventoryTransferController;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('materials', MaterialController::class);
    Route::resource('units', UnitController::class);
    Route::resource('suppliers', SupplierController::class);
    Route::resource('customers', CustomerController::class);
    
    // Inventory Transactions
    Route::resource('inventory-entries', InventoryEntryController::class);
    Route::resource('inventory-exits', InventoryExitController::class);
    Route::resource('inventory-transfers', InventoryTransferController::class);
    
    // Reports
    Route::get('/reports/inventory', [\App\Http\Controllers\ReportController::class, 'inventory'])->name('reports.inventory');

    Route::middleware(['role:Admin tổng'])->group(function () {
        Route::resource('warehouses', WarehouseController::class);
        Route::resource('users', UserController::class);
    });
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
