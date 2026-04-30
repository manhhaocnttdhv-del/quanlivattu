<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\MaterialWarehouse;

class WarehouseSeeder extends Seeder
{
    public function run(): void
    {
        // =============================================
        // 1. ĐƠN VỊ TÍNH
        // =============================================
        $donViCai   = Unit::firstOrCreate(['name' => 'Cái']);
        $donViBo    = Unit::firstOrCreate(['name' => 'Bộ']);
        $donViTan   = Unit::firstOrCreate(['name' => 'Tấn']);
        $donViMet   = Unit::firstOrCreate(['name' => 'Mét']);
        $donViKg    = Unit::firstOrCreate(['name' => 'Kg']);
        $donViThung = Unit::firstOrCreate(['name' => 'Thùng']);
        $donViCuon  = Unit::firstOrCreate(['name' => 'Cuộn']);

        // =============================================
        // 2. ADMIN TỔNG
        // =============================================
        $adminTotal = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'     => 'Admin Tổng',
                'password' => bcrypt('password'),
                'role'     => 'Admin tổng',
                'status'   => 'active',
            ]
        );

        // =============================================
        // 3. KHO HÀNG
        // =============================================
        $khoHN = Warehouse::firstOrCreate(
            ['name' => 'Kho Hà Nội'],
            ['address' => 'Số 12 Phố Huế, Hà Nội', 'status' => 'active']
        );

        $khoTH = Warehouse::firstOrCreate(
            ['name' => 'Kho Thanh Hóa'],
            ['address' => 'Khu công nghiệp Lễ Môn, Thanh Hóa', 'status' => 'active']
        );

        // =============================================
        // 4. ADMIN KHO HÀ NỘI
        // =============================================
        $adminKhoHN = User::updateOrCreate(
            ['email' => 'adminkho@gmail.com'],
            [
                'name'         => 'Quản lý Kho HN',
                'password'     => bcrypt('password'),
                'role'         => 'Admin kho',
                'warehouse_id' => $khoHN->id,
                'status'       => 'active',
            ]
        );
        $khoHN->update(['manager_id' => $adminKhoHN->id]);

        // =============================================
        // 5. ADMIN KHO THANH HÓA
        // =============================================
        $adminKhoTH = User::updateOrCreate(
            ['email' => 'adminkho2@gmail.com'],
            [
                'name'         => 'Quản lý Kho Thanh Hóa',
                'password'     => bcrypt('password'),
                'role'         => 'Admin kho',
                'warehouse_id' => $khoTH->id,
                'status'       => 'active',
            ]
        );
        $khoTH->update(['manager_id' => $adminKhoTH->id]);

        // =============================================
        // 6. NHÂN VIÊN KHO
        // =============================================
        User::updateOrCreate(
            ['email' => 'nv1@gmail.com'],
            [
                'name'         => 'Nguyễn Văn An',
                'password'     => bcrypt('password'),
                'role'         => 'Nhân viên kho',
                'warehouse_id' => $khoHN->id,
                'status'       => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'nv2@gmail.com'],
            [
                'name'         => 'Trần Thị Bình',
                'password'     => bcrypt('password'),
                'role'         => 'Nhân viên kho',
                'warehouse_id' => $khoHN->id,
                'status'       => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'nv3@gmail.com'],
            [
                'name'         => 'Lê Văn Cường',
                'password'     => bcrypt('password'),
                'role'         => 'Nhân viên kho',
                'warehouse_id' => $khoTH->id,
                'status'       => 'active',
            ]
        );

        // =============================================
        // 7. NHÀ CUNG CẤP
        // =============================================
        Supplier::updateOrCreate(
            ['name' => 'Công ty Thép Miền Bắc'],
            [
                'address'      => 'Khu CN Bắc Thăng Long, Hà Nội',
                'phone'        => '024.3869.1234',
                'warehouse_id' => $khoHN->id,
            ]
        );

        Supplier::updateOrCreate(
            ['name' => 'Công ty Cơ khí Hà Nội'],
            [
                'address'      => '56 Lê Duẩn, Hoàn Kiếm, Hà Nội',
                'phone'        => '024.3822.4567',
                'warehouse_id' => $khoHN->id,
            ]
        );

        Supplier::updateOrCreate(
            ['name' => 'Công ty Vật liệu Thanh Hóa'],
            [
                'address'      => 'KCN Lễ Môn, TP Thanh Hóa',
                'phone'        => '0237.3857.890',
                'warehouse_id' => $khoTH->id,
            ]
        );

        Supplier::updateOrCreate(
            ['name' => 'Công ty TNHH Điện Máy Phúc An'],
            [
                'address'      => '102 Đường Láng, Đống Đa, Hà Nội',
                'phone'        => '024.3534.2200',
                'warehouse_id' => $khoHN->id,
            ]
        );

        // =============================================
        // 8. KHÁCH HÀNG
        // =============================================
        Customer::updateOrCreate(
            ['name' => 'Công ty Xây dựng Hoàng Long'],
            [
                'address'      => '25 Nguyễn Trãi, Thanh Xuân, Hà Nội',
                'phone'        => '024.3868.5566',
                'warehouse_id' => $khoHN->id,
            ]
        );

        Customer::updateOrCreate(
            ['name' => 'Công ty TNHH Cơ khí Đại Thành'],
            [
                'address'      => 'KCN Nghi Sơn, Thanh Hóa',
                'phone'        => '0237.3898.7788',
                'warehouse_id' => $khoTH->id,
            ]
        );

        Customer::updateOrCreate(
            ['name' => 'Công ty CP Sản xuất Minh Đức'],
            [
                'address'      => '89 Giải Phóng, Hai Bà Trưng, Hà Nội',
                'phone'        => '024.3652.9900',
                'warehouse_id' => $khoHN->id,
            ]
        );

        // =============================================
        // 9. VẬT TƯ MẪU
        // =============================================
        $vatTu = [
            [
                'name'      => 'Bulong M10',
                'unit_id'   => $donViCai->id,
                'desc'      => 'Bulong thép mạ kẽm M10x30',
                'min_stock' => 100,
                'max_stock' => 5000,
            ],
            [
                'name'      => 'Ốc vít M6',
                'unit_id'   => $donViCai->id,
                'desc'      => 'Ốc vít inox M6x20',
                'min_stock' => 200,
                'max_stock' => 10000,
            ],
            [
                'name'      => 'Tấm thép CT3 3mm',
                'unit_id'   => $donViTan->id,
                'desc'      => 'Thép tấm cán nóng CT3, dày 3mm',
                'min_stock' => 5,
                'max_stock' => 200,
            ],
            [
                'name'      => 'Dây điện đơn 2.5mm²',
                'unit_id'   => $donViMet->id,
                'desc'      => 'Dây điện lõi đồng bọc nhựa PVC 2.5mm²',
                'min_stock' => 500,
                'max_stock' => 20000,
            ],
            [
                'name'      => 'Ống nhựa PVC Φ60',
                'unit_id'   => $donViMet->id,
                'desc'      => 'Ống nhựa PVC cứng đường kính 60mm',
                'min_stock' => 100,
                'max_stock' => 3000,
            ],
            [
                'name'      => 'Sơn chống rỉ màu đỏ',
                'unit_id'   => $donViThung->id,
                'desc'      => 'Sơn chống rỉ Joton, thùng 17 lít',
                'min_stock' => 10,
                'max_stock' => 200,
            ],
            [
                'name'      => 'Dây thép cuộn Φ6',
                'unit_id'   => $donViCuon->id,
                'desc'      => 'Dây thép cuộn phi 6, mỗi cuộn 100kg',
                'min_stock' => 5,
                'max_stock' => 100,
            ],
            [
                'name'      => 'Bi trụ bạc đạn 6205',
                'unit_id'   => $donViCai->id,
                'desc'      => 'Vòng bi trụ đũa 6205, NSK hoặc tương đương',
                'min_stock' => 20,
                'max_stock' => 500,
            ],
        ];

        $createdMaterials = [];
        foreach ($vatTu as $vt) {
            $createdMaterials[] = Material::updateOrCreate(
                ['name' => $vt['name']],
                [
                    'unit_id'     => $vt['unit_id'],
                    'description' => $vt['desc'],
                    'min_stock'   => $vt['min_stock'],
                    'max_stock'   => $vt['max_stock'],
                ]
            );
        }

        // =============================================
        // 10. TỒN KHO BAN ĐẦU
        // =============================================
        $tonKhoHN = [
            [$createdMaterials[0]->id, 1500, 'Khu A - Kệ 01', 2500],   // Bulong M10
            [$createdMaterials[1]->id, 3200, 'Khu A - Kệ 02', 800],    // Ốc vít M6
            [$createdMaterials[2]->id, 25,   'Khu B - Sân ngoài', 18500000], // Tấm thép
            [$createdMaterials[3]->id, 8000, 'Khu A - Kệ 04', 35000],  // Dây điện
            [$createdMaterials[5]->id, 45,   'Khu C - Kệ 01', 1850000], // Sơn chống rỉ
            [$createdMaterials[7]->id, 120,  'Khu A - Kệ 06', 185000], // Bi trụ
        ];

        foreach ($tonKhoHN as [$matId, $stock, $loc, $cost]) {
            MaterialWarehouse::updateOrCreate(
                ['warehouse_id' => $khoHN->id, 'material_id' => $matId],
                ['stock' => $stock, 'location' => $loc, 'average_cost' => $cost]
            );
        }

        $tonKhoTH = [
            [$createdMaterials[0]->id, 800,  'Khu A - Kệ 01', 2500],   // Bulong M10
            [$createdMaterials[2]->id, 15,   'Khu B - Sân ngoài', 18500000], // Tấm thép
            [$createdMaterials[4]->id, 2000, 'Khu A - Kệ 03', 28000],  // Ống nhựa
            [$createdMaterials[6]->id, 30,   'Khu B - Kệ 01', 3200000], // Dây thép cuộn
        ];

        foreach ($tonKhoTH as [$matId, $stock, $loc, $cost]) {
            MaterialWarehouse::updateOrCreate(
                ['warehouse_id' => $khoTH->id, 'material_id' => $matId],
                ['stock' => $stock, 'location' => $loc, 'average_cost' => $cost]
            );
        }
    }
}
