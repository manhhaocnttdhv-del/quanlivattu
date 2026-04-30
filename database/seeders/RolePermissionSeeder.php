<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RolePermission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Admin tổng', 'Admin kho', 'Nhân viên kho'];
        $groups = config('permissions');

        // Xóa dữ liệu cũ để seed lại sạch
        RolePermission::truncate();

        foreach ($groups as $group) {
            foreach ($group['permissions'] as $perm) {
                foreach ($roles as $role) {
                    RolePermission::create([
                        'role'            => $role,
                        'permission_name' => $perm['name'],
                        'group_name'      => $group['group'],
                        'description'     => $perm['description'] ?? null,
                        'is_granted'      => in_array($role, $perm['roles']),
                    ]);
                }
            }
        }
    }
}
