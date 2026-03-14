<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tạo Đơn vị tính
        $unit = \App\Models\Unit::firstOrCreate(['name' => 'Cái']);
        \App\Models\Unit::firstOrCreate(['name' => 'Bộ']);
        \App\Models\Unit::firstOrCreate(['name' => 'Tấn']);
        \App\Models\Unit::firstOrCreate(['name' => 'Mỗi']);

        // 2. Tạo Admin tổng
        $adminTotal = \App\Models\User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin Tổng',
                'password' => bcrypt('password'),
                'role' => 'Admin tổng',
            ]
        );

        // 3. Tạo Kho
        $khoHN = \App\Models\Warehouse::firstOrCreate(
            ['name' => 'Kho Hà Nội'],
            ['address' => 'Hà Nội', 'status' => 'active']
        );

        $khoTH = \App\Models\Warehouse::firstOrCreate(
            ['name' => 'Kho Thanh Hóa'],
            ['address' => 'Thanh Hóa', 'status' => 'active']
        );

        // 4. Tạo Admin kho
        $adminKho = \App\Models\User::updateOrCreate(
            ['email' => 'adminkho@gmail.com'],
            [
                'name' => 'Quản lý Kho HN',
                'password' => bcrypt('password'),
                'role' => 'Admin kho',
                'warehouse_id' => $khoHN->id,
            ]
        );

        $khoHN->update(['manager_id' => $adminKho->id]);

        // 5. Tạo Nhân viên kho
        \App\Models\User::updateOrCreate(
            ['email' => 'nv1@gmail.com'],
            [
                'name' => 'Nhân viên 1',
                'password' => bcrypt('password'),
                'role' => 'Nhân viên kho',
                'warehouse_id' => $khoHN->id,
            ]
        );

        // 6. Tạo Vật tư mẫu
        \App\Models\Material::updateOrCreate(
            ['name' => 'Bulong M10'],
            [
                'unit_id' => $unit->id,
                'description' => 'Bulong thép mạ kẽm',
                'min_stock' => 100,
            ]
        );
    }
}
