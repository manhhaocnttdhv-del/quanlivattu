<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        // Register Observers for Audit Logging
        \App\Models\InventoryEntry::observe(\App\Observers\AuditObserver::class);
        \App\Models\InventoryExit::observe(\App\Observers\AuditObserver::class);
        \App\Models\InventoryTransfer::observe(\App\Observers\AuditObserver::class);
        \App\Models\InventoryCheck::observe(\App\Observers\AuditObserver::class);
        \App\Models\Material::observe(\App\Observers\AuditObserver::class);
        \App\Models\Warehouse::observe(\App\Observers\AuditObserver::class);
        \App\Models\Supplier::observe(\App\Observers\AuditObserver::class);
        \App\Models\Customer::observe(\App\Observers\AuditObserver::class);
    }
}
