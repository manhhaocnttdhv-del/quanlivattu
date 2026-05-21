<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Unit;
use App\Models\Category;

class AdditionalMaterialSeeder extends Seeder
{
    /**
     * Seed thêm vật tư mở rộng (bổ sung sau WarehouseSeeder).
     * Mỗi vật tư gắn đúng đơn vị tính, nhóm vật tư, và đơn giá tham khảo.
     */
    public function run(): void
    {
        $units = Unit::all();
        if ($units->isEmpty()) {
            $this->command->warn('⚠️ Chưa có đơn vị tính. Bỏ qua AdditionalMaterialSeeder.');
            return;
        }

        // ── Lấy category theo tên nhóm con ──
        $catXiMang   = Category::where('name', 'Xi măng')->first();
        $catGach     = Category::where('name', 'Gạch - Ngói')->first();
        $catThep     = Category::where('name', 'Thép xây dựng')->first();
        $catDayCap   = Category::where('name', 'Dây & Cáp điện')->first();
        $catOngLuon  = Category::where('name', 'Ống luồn dây điện')->first();
        $catDen      = Category::where('name', 'Đèn chiếu sáng')->first();
        $catOngNuoc  = Category::where('name', 'Ống nước PVC')->first();
        $catDungCu   = Category::where('name', 'Dụng cụ cầm tay')->first();
        $catTieuHao  = Category::where('name', 'Vật tư tiêu hao')->first();
        $catBulong   = Category::where('name', 'Bu lông - Đai ốc')->first();

        $materials = [
            // ── Vật tư nước ──
            ['name' => 'Ống nhựa PVC Phi 21', 'unit' => 'Cây', 'cat' => $catOngNuoc,  'desc' => 'Ống nhựa Bình Minh chịu lực',      'cost_price' => 15000,    'min_stock' => 50],
            ['name' => 'Ống nhựa PVC Phi 27', 'unit' => 'Cây', 'cat' => $catOngNuoc,  'desc' => 'Ống dẫn nước sạch',                 'cost_price' => 22000,    'min_stock' => 50],

            // ── Xi măng ──
            ['name' => 'Xi măng Hà Tiên 1',   'unit' => 'Tấn', 'cat' => $catXiMang,   'desc' => 'Xi măng xây dựng hỗn hợp PCB40',   'cost_price' => 1850000,  'min_stock' => 10],
            ['name' => 'Xi măng Hoàng Thạch',  'unit' => 'Tấn', 'cat' => $catXiMang,   'desc' => 'Xi măng đen',                      'cost_price' => 1720000,  'min_stock' => 15],

            // ── Thép ──
            ['name' => 'Thép cuộn CB240T',     'unit' => 'Tấn', 'cat' => $catThep,     'desc' => 'Thép cán nóng Hòa Phát',           'cost_price' => 14500000, 'min_stock' => 5],
            ['name' => 'Thép thanh vằn D10',   'unit' => 'Tấn', 'cat' => $catThep,     'desc' => 'Thép Việt Nhật',                   'cost_price' => 15200000, 'min_stock' => 5],

            // ── Giàn giáo (chưa có nhóm phù hợp) ──
            ['name' => 'Bộ giàn giáo chữ H',  'unit' => 'Bộ',  'cat' => null,          'desc' => 'Giàn giáo xây dựng mạ kẽm',       'cost_price' => 450000,   'min_stock' => 20],

            // ── Gạch ──
            ['name' => 'Gạch thẻ Solid',       'unit' => 'Cái', 'cat' => $catGach,     'desc' => 'Gạch xây đặc',                     'cost_price' => 1200,     'min_stock' => 5000],
            ['name' => 'Gạch tuynel 2 lỗ',     'unit' => 'Cái', 'cat' => $catGach,     'desc' => 'Gạch xây tường ngăn',              'cost_price' => 950,      'min_stock' => 2000],
            ['name' => 'Gạch men lát nền 60x60','unit' => 'Cái','cat' => $catGach,     'desc' => 'Gạch Ceramic',                     'cost_price' => 85000,    'min_stock' => 500],

            // ── Điện ──
            ['name' => 'Băng keo điện NANO',     'unit' => 'Cái', 'cat' => $catTieuHao, 'desc' => 'Băng dán cách điện',               'cost_price' => 8000,     'min_stock' => 200],
            ['name' => 'Dây cáp điện CADIVI 2.5','unit' => 'Mét', 'cat' => $catDayCap,  'desc' => 'Cáp điện đôi',                    'cost_price' => 42000,    'min_stock' => 100],
            ['name' => 'Dây cáp điện CADIVI 4.0','unit' => 'Mét', 'cat' => $catDayCap,  'desc' => 'Cáp điện loại dày',               'cost_price' => 68000,    'min_stock' => 50],

            // ── Sơn ──
            ['name' => 'Sơn Dulux Weathershield', 'unit' => 'Thùng', 'cat' => null,     'desc' => 'Sơn ngoại thất thùng 18L',        'cost_price' => 2450000,  'min_stock' => 30],
            ['name' => 'Sơn Maxilite nội thất',   'unit' => 'Thùng', 'cat' => null,     'desc' => 'Sơn lót thùng xanh',              'cost_price' => 1680000,  'min_stock' => 50],

            // ── Đèn ──
            ['name' => 'Bóng đèn Led âm trần 18W','unit' => 'Cái', 'cat' => $catDen,   'desc' => 'Đèn Rạng Đông Ánh sáng trắng',   'cost_price' => 75000,    'min_stock' => 200],

            // ── Dụng cụ cầm tay ──
            ['name' => 'Cờ lê 10-12',             'unit' => 'Cái', 'cat' => $catDungCu, 'desc' => 'Dụng cụ sửa chữa',               'cost_price' => 35000,    'min_stock' => 45],
            ['name' => 'Tuốc nơ vít dẹp',         'unit' => 'Cái', 'cat' => $catDungCu, 'desc' => 'Dụng cụ sửa chữa đa năng',      'cost_price' => 25000,    'min_stock' => 30],
            ['name' => 'Máy khoan pin Makita',     'unit' => 'Bộ',  'cat' => $catDungCu, 'desc' => 'Máy khoan cầm tay 18V',          'cost_price' => 3500000,  'min_stock' => 10],

            // ── Khác ──
            ['name' => 'Đinh thép 5 phân',         'unit' => 'Kg',  'cat' => $catBulong, 'desc' => 'Đinh đóng gỗ',                   'cost_price' => 22000,    'min_stock' => 50],
        ];

        $count = 0;
        foreach ($materials as $item) {
            $unit = $units->firstWhere('name', $item['unit']) ?? $units->first();

            Material::updateOrCreate(
                ['name' => $item['name']],
                [
                    'unit_id'     => $unit->id,
                    'category_id' => $item['cat']?->id,
                    'description' => $item['desc'],
                    'cost_price'  => $item['cost_price'],
                    'selling_price' => round($item['cost_price'] * 1.15),
                    'min_stock'   => $item['min_stock'],
                ]
            );
            $count++;
        }

        $this->command->info("✅ AdditionalMaterialSeeder: Đã tạo/cập nhật {$count} vật tư bổ sung.");
    }
}
