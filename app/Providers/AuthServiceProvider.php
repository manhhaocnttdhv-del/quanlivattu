<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Register dynamic gates based on config/permissions.php
        $permissions = collect(config('permissions', []))
            ->pluck('permissions')
            ->collapse()
            ->pluck('name')
            ->unique();

        foreach ($permissions as $permName) {
            \Illuminate\Support\Facades\Gate::define($permName, function ($user) use ($permName) {
                return $user->hasPermission($permName);
            });
        }

        // Composite gates for index/show screens
        \Illuminate\Support\Facades\Gate::define('view-inventory-entries', function ($user) {
            return $user->hasPermission('Tạo phiếu nhập kho') 
                || $user->hasPermission('Duyệt / Hủy phiếu nhập kho')
                || $user->hasPermission('Xuất Excel / PDF nhập kho');
        });

        \Illuminate\Support\Facades\Gate::define('view-inventory-exits', function ($user) {
            return $user->hasPermission('Tạo phiếu xuất kho') 
                || $user->hasPermission('Duyệt / Hủy phiếu xuất kho')
                || $user->hasPermission('Xuất Excel / PDF xuất kho');
        });

        \Illuminate\Support\Facades\Gate::define('view-inventory-transfers', function ($user) {
            return $user->hasPermission('Tạo phiếu chuyển kho') 
                || $user->hasPermission('Duyệt / Hủy phiếu chuyển kho');
        });

        \Illuminate\Support\Facades\Gate::define('view-inventory-checks', function ($user) {
            return $user->hasPermission('Tạo phiếu kiểm kê') 
                || $user->hasPermission('Duyệt / Hủy phiếu kiểm kê');
        });

        \Illuminate\Support\Facades\Gate::define('view-reports', function ($user) {
            return $user->hasPermission('Xem báo cáo tồn kho') 
                || $user->hasPermission('Xuất báo cáo Excel / PDF');
        });
    }
}
