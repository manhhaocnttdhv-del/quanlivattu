<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Warehouse;
use App\Models\Material;
use App\Models\InventoryEntry;
use App\Models\InventoryExit;
use App\Models\InventoryTransfer;
use App\Models\InventoryCheck;
use App\Models\User;
use Carbon\Carbon;

class MockDataSeeder extends Seeder
{
    public function run(): void
    {
        $khoHN = Warehouse::where('name', 'Kho Hà Nội')->first();
        $khoTH = Warehouse::where('name', 'Kho Thanh Hóa')->first();
        $admin = User::where('role', 'Admin tổng')->first();

        if (!$khoHN || !$khoTH) return;

        // 1. Seed Suppliers
        $suppliers = [
            ['name' => 'Công ty TNHH Thép Hòa Phát', 'address' => 'Hưng Yên', 'phone' => '0243456789', 'warehouse_id' => $khoHN->id],
            ['name' => 'Công ty CP Nhựa Bình Minh', 'address' => 'TP.HCM', 'phone' => '0283845678', 'warehouse_id' => null], // Global
            ['name' => 'Tổng Công ty Xi măng Việt Nam (VICEM)', 'address' => 'Hà Nội', 'phone' => '0243123456', 'warehouse_id' => $khoHN->id],
            ['name' => 'Công ty TNHH Thiết bị xây dựng Việt', 'address' => 'Thanh Hóa', 'phone' => '0237123456', 'warehouse_id' => $khoTH->id],
        ];

        foreach ($suppliers as $s) {
            Supplier::firstOrCreate(['name' => $s['name']], $s);
        }

        // 2. Seed Projects
        $projects = [
            ['name' => 'Công trình Tòa nhà A', 'phone' => '098111', 'address' => 'Quận 1'],
            ['name' => 'Dự án Cầu Trường Tiền', 'phone' => '098222', 'address' => 'Quận 2'],
            ['name' => 'Biệt thự ABC', 'phone' => '098333', 'address' => 'Quận 3'],
        ];

        foreach ($projects as $p) {
            \App\Models\Project::firstOrCreate(['name' => $p['name']], $p);
        }

        // 3. Transactions (Entries)
        $material = Material::first();
        if ($material) {
            $supplier = Supplier::first();
            
            // Create some entries
            for ($i = 0; $i < 5; $i++) {
                $entry = InventoryEntry::create([
                    'warehouse_id' => $khoHN->id,
                    'supplier_id' => $supplier->id,
                    'user_id' => $admin->id,
                    'date' => Carbon::now()->subDays(rand(1, 30)),
                    'status' => 'completed',
                    'note' => 'Nhập hàng mẫu đợt ' . ($i + 1),
                ]);

                $entry->details()->create([
                    'material_id' => $material->id,
                    'quantity' => rand(100, 500),
                    'price' => rand(10000, 50000),
                ]);
                
                // Manually update stock for seeder (assuming service logic is not triggered automatically on create if using raw Eloquent)
                $stock = \App\Models\MaterialWarehouse::firstOrCreate(
                    ['warehouse_id' => $khoHN->id, 'material_id' => $material->id],
                    ['stock' => 0]
                );
                $stock->increment('stock', $entry->details->sum('quantity'));
            }
        }

        // 4. Transfers (already added - pending)
        if ($material) {
             InventoryTransfer::firstOrCreate(
                ['note' => 'Chuyển vật tư hỗ trợ kho Thanh Hóa'],
                [
                    'from_warehouse_id' => $khoHN->id,
                    'to_warehouse_id' => $khoTH->id,
                    'user_id' => $admin->id,
                    'date' => Carbon::now(),
                    'status' => 'pending',
                ]
            );
        }

        // 5. Exits (Shipments)
        if ($material) {
            $project = \App\Models\Project::first();
            for ($i = 0; $i < 3; $i++) {
                $exit = InventoryExit::create([
                    'warehouse_id' => $khoHN->id,
                    'project_id' => $project->id,
                    'user_id' => $admin->id,
                    'date' => Carbon::now()->subDays(rand(1, 10)),
                    'status' => 'completed',
                    'note' => 'Xuất hàng cho dự án đợt ' . ($i + 1),
                ]);

                $exit->details()->create([
                    'material_id' => $material->id,
                    'quantity' => rand(10, 50),
                ]);

                // Update stock
                $stock = \App\Models\MaterialWarehouse::where([
                    'warehouse_id' => $khoHN->id,
                    'material_id' => $material->id
                ])->first();
                if ($stock) {
                    $stock->decrement('stock', $exit->details->sum('quantity'));
                }
            }
        }

        // 6. Inventory Checks (Audit)
        if ($material) {
            $check = InventoryCheck::create([
                'warehouse_id' => $khoHN->id,
                'user_id' => $admin->id,
                'date' => Carbon::now(),
                'status' => 'completed',
                'note' => 'Kiểm kê định kỳ tháng 3',
            ]);

            $currentStock = \App\Models\MaterialWarehouse::where([
                'warehouse_id' => $khoHN->id,
                'material_id' => $material->id
            ])->value('stock') ?? 0;

            $check->details()->create([
                'material_id' => $material->id,
                'system_stock' => $currentStock,
                'actual_stock' => $currentStock + 5, // Found 5 extra items
                'note' => 'Dư 5 cái do chưa nhập kho kịp',
            ]);
        }
    }
}
