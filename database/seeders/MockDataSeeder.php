<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Project;
use App\Models\Warehouse;
use App\Models\Material;
use App\Models\InventoryEntry;
use App\Models\InventoryExit;
use App\Models\InventoryTransfer;
use App\Models\InventoryCheck;
use App\Models\ProjectMaterial;
use App\Models\User;
use Carbon\Carbon;

class MockDataSeeder extends Seeder
{
    public function run(): void
    {
        $khoHN = Warehouse::where('name', 'Kho Hà Nội')->first();
        $khoTH = Warehouse::where('name', 'Kho Thanh Hóa')->first();
        $admin = User::where('role', 'Admin tổng')->first();

        if (!$khoHN || !$khoTH || !$admin) {
            $this->command->warn('⚠️ Thiếu dữ liệu kho/admin. Bỏ qua MockDataSeeder.');
            return;
        }

        // =============================================
        // 1. NHÀ CUNG CẤP BỔ SUNG
        // =============================================
        $suppliers = [
            ['name' => 'Công ty TNHH Thép Hòa Phát',             'address' => 'Hưng Yên',   'phone' => '0243456789',  'warehouse_id' => $khoHN->id],
            ['name' => 'Công ty CP Nhựa Bình Minh',              'address' => 'TP.HCM',     'phone' => '0283845678',  'warehouse_id' => null], // Toàn quốc
            ['name' => 'Tổng Công ty Xi măng Việt Nam (VICEM)',   'address' => 'Hà Nội',     'phone' => '0243123456',  'warehouse_id' => $khoHN->id],
            ['name' => 'Công ty TNHH Thiết bị xây dựng Việt',    'address' => 'Thanh Hóa',  'phone' => '0237123456',  'warehouse_id' => $khoTH->id],
        ];

        foreach ($suppliers as $s) {
            Supplier::firstOrCreate(['name' => $s['name']], $s);
        }

        // =============================================
        // 2. CÔNG TRÌNH BỔ SUNG
        // =============================================
        $projects = [
            ['name' => 'Công trình Tòa nhà A',   'phone' => '0981110001', 'address' => 'Quận Hoàn Kiếm, Hà Nội'],
            ['name' => 'Dự án Cầu Trường Tiền',   'phone' => '0982220002', 'address' => 'TP Huế, Thừa Thiên Huế'],
            ['name' => 'Biệt thự ABC',            'phone' => '0983330003', 'address' => 'Quận Long Biên, Hà Nội'],
        ];

        foreach ($projects as $p) {
            Project::firstOrCreate(['name' => $p['name']], $p);
        }

        // =============================================
        // 3. PHIẾU NHẬP KHO (5 phiếu, nhiều vật tư hơn)
        // =============================================
        $materials = Material::take(5)->get();
        $supplier  = Supplier::first();

        if ($materials->isNotEmpty() && $supplier) {
            for ($i = 0; $i < 5; $i++) {
                $mat = $materials[$i % $materials->count()];

                $entry = InventoryEntry::create([
                    'warehouse_id' => $khoHN->id,
                    'supplier_id'  => $supplier->id,
                    'user_id'      => $admin->id,
                    'date'         => Carbon::now()->subDays(rand(5, 30)),
                    'status'       => 'completed',
                    'note'         => 'Nhập hàng mẫu đợt ' . ($i + 1),
                ]);

                $qty   = rand(100, 500);
                $warehouseStock = \App\Models\MaterialWarehouse::where('warehouse_id', $khoHN->id)
                    ->where('material_id', $mat->id)
                    ->first();
                $price = $warehouseStock && $warehouseStock->cost_price > 0 ? $warehouseStock->cost_price : rand(10000, 50000);

                $entry->details()->create([
                    'material_id' => $mat->id,
                    'quantity'    => $qty,
                    'unit_price'  => $price,
                ]);

                // Cập nhật tồn kho
                $stock = \App\Models\MaterialWarehouse::firstOrCreate(
                    ['warehouse_id' => $khoHN->id, 'material_id' => $mat->id],
                    ['stock' => 0]
                );
                $stock->increment('stock', $qty);
            }

            $this->command->info('✅ Đã tạo 5 phiếu nhập kho mẫu.');
        }

        // =============================================
        // 4. DỰ TOÁN VẬT TƯ CHO CÔNG TRÌNH (ProjectMaterial)
        //    Cần có trước khi tạo phiếu xuất
        // =============================================
        $allProjects  = Project::all();
        $allMaterials = Material::take(8)->get();

        foreach ($allProjects as $project) {
            foreach ($allMaterials as $mat) {
                ProjectMaterial::firstOrCreate(
                    ['project_id' => $project->id, 'material_id' => $mat->id],
                    ['estimated_quantity' => rand(500, 5000)]
                );
            }
        }

        $this->command->info('✅ Đã tạo dự toán vật tư cho ' . $allProjects->count() . ' công trình.');

        // =============================================
        // 5. PHIẾU CHUYỂN KHO
        // =============================================
        if ($materials->isNotEmpty()) {
            InventoryTransfer::firstOrCreate(
                ['note' => 'Chuyển vật tư hỗ trợ kho Thanh Hóa'],
                [
                    'from_warehouse_id' => $khoHN->id,
                    'to_warehouse_id'   => $khoTH->id,
                    'user_id'           => $admin->id,
                    'date'              => Carbon::now(),
                    'status'            => 'pending',
                ]
            );

            $this->command->info('✅ Đã tạo 1 phiếu chuyển kho mẫu (pending).');
        }

        // =============================================
        // 6. PHIẾU XUẤT KHO (3 phiếu, có giá xuất)
        // =============================================
        $project = Project::first();
        $matForExit = $materials->first();

        if ($matForExit && $project) {
            for ($i = 0; $i < 3; $i++) {
                $exit = InventoryExit::create([
                    'warehouse_id' => $khoHN->id,
                    'project_id'   => $project->id,
                    'user_id'      => $admin->id,
                    'date'         => Carbon::now()->subDays(rand(1, 10)),
                    'status'       => 'completed',
                    'note'         => 'Xuất hàng cho dự án đợt ' . ($i + 1),
                ]);

                $qty   = rand(10, 50);
                $warehouseStock = \App\Models\MaterialWarehouse::where('warehouse_id', $khoHN->id)
                    ->where('material_id', $matForExit->id)
                    ->first();
                $price = $warehouseStock && $warehouseStock->selling_price > 0 ? $warehouseStock->selling_price : 0;

                $exit->details()->create([
                    'material_id' => $matForExit->id,
                    'quantity'    => $qty,
                    'unit_price'  => $price,
                ]);

                // Cập nhật tồn kho
                $stock = \App\Models\MaterialWarehouse::where([
                    'warehouse_id' => $khoHN->id,
                    'material_id'  => $matForExit->id,
                ])->first();
                if ($stock && $stock->stock >= $qty) {
                    $stock->decrement('stock', $qty);
                }
            }

            $this->command->info('✅ Đã tạo 3 phiếu xuất kho mẫu.');
        }

        // =============================================
        // 7. PHIẾU KIỂM KÊ
        // =============================================
        if ($matForExit) {
            $check = InventoryCheck::create([
                'warehouse_id' => $khoHN->id,
                'user_id'      => $admin->id,
                'date'         => Carbon::now(),
                'status'       => 'completed',
                'note'         => 'Kiểm kê định kỳ tháng ' . Carbon::now()->format('m/Y'),
            ]);

            $currentStock = \App\Models\MaterialWarehouse::where([
                'warehouse_id' => $khoHN->id,
                'material_id'  => $matForExit->id,
            ])->value('stock') ?? 0;

            $check->details()->create([
                'material_id'  => $matForExit->id,
                'system_stock' => $currentStock,
                'actual_stock' => $currentStock + 5,
                'note'         => 'Dư 5 cái do chưa nhập kho kịp',
            ]);

            $this->command->info('✅ Đã tạo 1 phiếu kiểm kê mẫu.');
        }
    }
}
