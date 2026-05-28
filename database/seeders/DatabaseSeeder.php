<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed toàn bộ dữ liệu mẫu cho hệ thống Quản lý Vật tư.
     *
     * Thứ tự chạy:
     * 1. CategorySeeder       → Nhóm vật tư (cha/con)
     * 2. WarehouseSeeder      → ĐVT, Users, Kho, NCC, Công trình, Vật tư cơ bản, Tồn kho
     * 3. AdditionalMaterialSeeder → Vật tư mở rộng (gắn nhóm + giá)
     * 4. RolePermissionSeeder → Phân quyền theo vai trò
     * 5. MockDataSeeder       → Phiếu nhập/xuất/chuyển/kiểm kê mẫu + Dự toán
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            WarehouseSeeder::class,
            AdditionalMaterialSeeder::class,
            RolePermissionSeeder::class,
            MockDataSeeder::class,
            DeliveryPartnerSeeder::class,
        ]);
    }
}
