<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\Material;
use App\Models\Supplier;
use App\Models\Project;
use App\Models\MaterialWarehouse;
use App\Models\Category;
use App\Models\WarehouseStaff;
use App\Models\Shift;
use Illuminate\Support\Carbon;

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
        $donViHop   = Unit::firstOrCreate(['name' => 'Hộp']);
        $donViCay   = Unit::firstOrCreate(['name' => 'Cây']);

        $this->command->info('✅ Đã tạo ' . Unit::count() . ' đơn vị tính.');

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

        $this->command->info('✅ Đã tạo ' . User::count() . ' tài khoản người dùng.');

        // =============================================
        // 6.1 HỒ SƠ NHÂN VIÊN KHO (WarehouseStaff)
        // =============================================
        $nv1 = User::where('email', 'nv1@gmail.com')->first();
        if ($nv1) {
            WarehouseStaff::updateOrCreate(['user_id' => $nv1->id], [
                'warehouse_id' => $nv1->warehouse_id,
                'full_name'    => 'Nguyễn Văn An',
                'phone'        => '0987654321',
                'id_card'      => '001090123456',
                'gender'       => 'male',
                'position'     => 'Nhân viên thủ kho',
                'start_date'   => Carbon::now()->subMonths(6),
                'base_salary'  => 8000000,
                'status'       => 'active'
            ]);
        }

        $nv2 = User::where('email', 'nv2@gmail.com')->first();
        if ($nv2) {
            WarehouseStaff::updateOrCreate(['user_id' => $nv2->id], [
                'warehouse_id' => $nv2->warehouse_id,
                'full_name'    => 'Trần Thị Bình',
                'phone'        => '0912345678',
                'id_card'      => '001090654321',
                'gender'       => 'female',
                'position'     => 'Nhân viên kiểm kê',
                'start_date'   => Carbon::now()->subMonths(3),
                'base_salary'  => 7500000,
                'status'       => 'active'
            ]);
        }

        $nv3 = User::where('email', 'nv3@gmail.com')->first();
        if ($nv3) {
            WarehouseStaff::updateOrCreate(['user_id' => $nv3->id], [
                'warehouse_id' => $nv3->warehouse_id,
                'full_name'    => 'Lê Văn Cường',
                'phone'        => '0909123456',
                'id_card'      => '001090987654',
                'gender'       => 'male',
                'position'     => 'Nhân viên xuất nhập',
                'start_date'   => Carbon::now()->subYear(),
                'base_salary'  => 8500000,
                'status'       => 'active'
            ]);
        }
        $this->command->info('✅ Đã tạo ' . WarehouseStaff::count() . ' hồ sơ nhân viên kho.');

        // =============================================
        // 6.2 CA LÀM VIỆC (Shift)
        // =============================================
        $shifts = [
            ['name' => 'Ca sáng', 'start_time' => '07:30:00', 'end_time' => '11:30:00'],
            ['name' => 'Ca chiều', 'start_time' => '13:00:00', 'end_time' => '17:00:00'],
        ];

        foreach ([$khoHN, $khoTH] as $kho) {
            foreach ($shifts as $shift) {
                Shift::updateOrCreate(
                    ['warehouse_id' => $kho->id, 'name' => $shift['name']],
                    ['start_time' => $shift['start_time'], 'end_time' => $shift['end_time']]
                );
            }
        }
        $this->command->info('✅ Đã tạo ' . Shift::count() . ' ca làm việc mẫu.');

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

        $this->command->info('✅ Đã tạo ' . Supplier::count() . ' nhà cung cấp.');

        // =============================================
        // 8. CÔNG TRÌNH / DỰ ÁN (thay thế Khách hàng cũ)
        // =============================================
        Project::updateOrCreate(
            ['name' => 'Công trình Tòa nhà Hoàng Long'],
            [
                'address'      => '25 Nguyễn Trãi, Thanh Xuân, Hà Nội',
                'phone'        => '024.3868.5566',
                'warehouse_id' => $khoHN->id,
            ]
        );

        Project::updateOrCreate(
            ['name' => 'Dự án Nhà máy Đại Thành'],
            [
                'address'      => 'KCN Nghi Sơn, Thanh Hóa',
                'phone'        => '0237.3898.7788',
                'warehouse_id' => $khoTH->id,
            ]
        );

        Project::updateOrCreate(
            ['name' => 'Công trình Khu dân cư Minh Đức'],
            [
                'address'      => '89 Giải Phóng, Hai Bà Trưng, Hà Nội',
                'phone'        => '024.3652.9900',
                'warehouse_id' => $khoHN->id,
            ]
        );

        $this->command->info('✅ Đã tạo ' . Project::count() . ' công trình / dự án.');

        // =============================================
        // 9. VẬT TƯ MẪU (gắn nhóm vật tư category_id)
        // =============================================
        // Lấy category_id từ nhóm con đã tạo bởi CategorySeeder
        $catBulong    = Category::where('name', 'Bu lông - Đai ốc')->first();
        $catThep      = Category::where('name', 'Thép xây dựng')->first();
        $catDayDien   = Category::where('name', 'Dây & Cáp điện')->first();
        $catOngNuoc   = Category::where('name', 'Ống nước PVC')->first();
        $catOBi       = Category::where('name', 'Ổ bi - Vòng bi')->first();

        $vatTu = [
            [
                'name'        => 'Bulong M10',
                'unit_id'     => $donViCai->id,
                'category_id' => $catBulong?->id,
                'desc'        => 'Bulong thép mạ kẽm M10x30',
                'cost_price'  => 2500,
                'min_stock'   => 100,
                'max_stock'   => 5000,
            ],
            [
                'name'        => 'Ốc vít M6',
                'unit_id'     => $donViCai->id,
                'category_id' => $catBulong?->id,
                'desc'        => 'Ốc vít inox M6x20',
                'cost_price'  => 800,
                'min_stock'   => 200,
                'max_stock'   => 10000,
            ],
            [
                'name'        => 'Tấm thép CT3 3mm',
                'unit_id'     => $donViTan->id,
                'category_id' => $catThep?->id,
                'desc'        => 'Thép tấm cán nóng CT3, dày 3mm',
                'cost_price'  => 18500000,
                'min_stock'   => 5,
                'max_stock'   => 200,
            ],
            [
                'name'        => 'Dây điện đơn 2.5mm²',
                'unit_id'     => $donViMet->id,
                'category_id' => $catDayDien?->id,
                'desc'        => 'Dây điện lõi đồng bọc nhựa PVC 2.5mm²',
                'cost_price'  => 35000,
                'min_stock'   => 500,
                'max_stock'   => 20000,
            ],
            [
                'name'        => 'Ống nhựa PVC Φ60',
                'unit_id'     => $donViMet->id,
                'category_id' => $catOngNuoc?->id,
                'desc'        => 'Ống nhựa PVC cứng đường kính 60mm',
                'cost_price'  => 28000,
                'min_stock'   => 100,
                'max_stock'   => 3000,
            ],
            [
                'name'        => 'Sơn chống rỉ màu đỏ',
                'unit_id'     => $donViThung->id,
                'category_id' => null, // Chưa có nhóm phù hợp
                'desc'        => 'Sơn chống rỉ Joton, thùng 17 lít',
                'cost_price'  => 1850000,
                'min_stock'   => 10,
                'max_stock'   => 200,
            ],
            [
                'name'        => 'Dây thép cuộn Φ6',
                'unit_id'     => $donViCuon->id,
                'category_id' => $catThep?->id,
                'desc'        => 'Dây thép cuộn phi 6, mỗi cuộn 100kg',
                'cost_price'  => 3200000,
                'min_stock'   => 5,
                'max_stock'   => 100,
            ],
            [
                'name'        => 'Bi trụ bạc đạn 6205',
                'unit_id'     => $donViCai->id,
                'category_id' => $catOBi?->id,
                'desc'        => 'Vòng bi trụ đũa 6205, NSK hoặc tương đương',
                'cost_price'  => 185000,
                'min_stock'   => 20,
                'max_stock'   => 500,
            ],
        ];

        $createdMaterials = [];
        foreach ($vatTu as $vt) {
            $createdMaterials[] = Material::updateOrCreate(
                ['name' => $vt['name']],
                [
                    'unit_id'     => $vt['unit_id'],
                    'category_id' => $vt['category_id'],
                    'description' => $vt['desc'],
                    'min_stock'   => $vt['min_stock'],
                    'max_stock'   => $vt['max_stock'],
                ]
            );
        }

        $this->command->info('✅ Đã tạo/cập nhật ' . count($createdMaterials) . ' vật tư cơ bản.');

        // =============================================
        // 10. TỒN KHO BAN ĐẦU
        //     [$material_id, $stock, $location, $average_cost, $cost_price, $selling_price]
        // =============================================
        $tonKhoHN = [
            [$createdMaterials[0]->id, 1500, 'Khu A - Kệ 01', 2500, 2500, round(2500 * 1.1)],      // Bulong M10
            [$createdMaterials[1]->id, 3200, 'Khu A - Kệ 02', 800, 800, round(800 * 1.1)],       // Ốc vít M6
            [$createdMaterials[2]->id, 25,   'Khu B - Sân ngoài', 18500000, 18500000, round(18500000 * 1.1)], // Tấm thép
            [$createdMaterials[3]->id, 8000, 'Khu A - Kệ 04', 35000, 35000, round(35000 * 1.1)],     // Dây điện
            [$createdMaterials[5]->id, 45,   'Khu C - Kệ 01', 1850000, 1850000, round(1850000 * 1.1)],   // Sơn chống rỉ
            [$createdMaterials[7]->id, 120,  'Khu A - Kệ 06', 185000, 185000, round(185000 * 1.1)],    // Bi trụ
        ];

        foreach ($tonKhoHN as [$matId, $stock, $loc, $cost, $costPrice, $sellingPrice]) {
            MaterialWarehouse::updateOrCreate(
                ['warehouse_id' => $khoHN->id, 'material_id' => $matId],
                [
                    'stock' => $stock,
                    'location' => $loc,
                    'average_cost' => $cost,
                    'cost_price' => $costPrice,
                    'selling_price' => $sellingPrice
                ]
            );
        }

        $tonKhoTH = [
            [$createdMaterials[0]->id, 800,  'Khu A - Kệ 01', 2500, 2500, round(2500 * 1.1)],      // Bulong M10
            [$createdMaterials[2]->id, 15,   'Khu B - Sân ngoài', 18500000, 18500000, round(18500000 * 1.1)], // Tấm thép
            [$createdMaterials[4]->id, 2000, 'Khu A - Kệ 03', 28000, 28000, round(28000 * 1.1)],     // Ống nhựa
            [$createdMaterials[6]->id, 30,   'Khu B - Kệ 01', 3200000, 3200000, round(3200000 * 1.1)],   // Dây thép cuộn
        ];

        foreach ($tonKhoTH as [$matId, $stock, $loc, $cost, $costPrice, $sellingPrice]) {
            MaterialWarehouse::updateOrCreate(
                ['warehouse_id' => $khoTH->id, 'material_id' => $matId],
                [
                    'stock' => $stock,
                    'location' => $loc,
                    'average_cost' => $cost,
                    'cost_price' => $costPrice,
                    'selling_price' => $sellingPrice
                ]
            );
        }

        $this->command->info('✅ Đã tạo tồn kho ban đầu cho ' . MaterialWarehouse::count() . ' bản ghi.');
    }
}
