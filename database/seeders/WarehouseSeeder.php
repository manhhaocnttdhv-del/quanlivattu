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
        $unit = \App\Models\Unit::create(['name' => 'Cái']);
        \App\Models\Unit::create(['name' => 'Bộ']);
        \App\Models\Unit::create(['name' => 'Tấn']);
        \App\Models\Unit::create(['name' => 'Mỗi']);

        // 2. Tạo Admin tổng
        \App\Models\User::create([
            'name' => 'Admin Tổng',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'Admin tổng',
        ]);

        // 3. Tạo Kho
        $khoHN = \App\Models\Warehouse::create([
            'name' => 'Kho Hà Nội',
            'address' => 'Hà Nội',
            'status' => 'active',
        ]);

        $khoTH = \App\Models\Warehouse::create([
            'name' => 'Kho Thanh Hóa',
            'address' => 'Thanh Hóa',
            'status' => 'active',
        ]);

        // 4. Tạo Admin kho
        $adminKho = \App\Models\User::create([
            'name' => 'Quản lý Kho HN',
            'email' => 'adminkho@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'Admin kho',
            'warehouse_id' => $khoHN->id,
        ]);

        $khoHN->update(['manager_id' => $adminKho->id]);

        // 5. Tạo Nhân viên kho
        \App\Models\User::create([
            'name' => 'Nhân viên 1',
            'email' => 'nv1@gmail.com',
            'password' => bcrypt('password'),
            'role' => 'Nhân viên kho',
            'warehouse_id' => $khoHN->id,
        ]);

        // 6. Tạo Vật tư mẫu
        \App\Models\Material::create([
            'name' => 'Bulong M10',
            'unit_id' => $unit->id,
            'description' => 'Bulong thép mạ kẽm',
            'min_stock' => 100,
        ]);
    }
}
