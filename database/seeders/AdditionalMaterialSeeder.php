<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;
use App\Models\Unit;

class AdditionalMaterialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = Unit::all();
        if ($units->isEmpty()) {
            return;
        }

        $materials = [
            ['name' => 'Ống nhựa PVC Phi 21', 'unit' => 'Mỗi', 'desc' => 'Ống nhựa Bình Minh chịu lực', 'min_stock' => 50],
            ['name' => 'Ống nhựa PVC Phi 27', 'unit' => 'Mỗi', 'desc' => 'Ống dẫn nước sạch', 'min_stock' => 50],
            ['name' => 'Xi măng Hà Tiên 1', 'unit' => 'Tấn', 'desc' => 'Xi măng xây dựng hỗn hợp PCB40', 'min_stock' => 10],
            ['name' => 'Xi măng Hoàng Thạch', 'unit' => 'Tấn', 'desc' => 'Xi măng đen', 'min_stock' => 15],
            ['name' => 'Thép cuộn CB240T', 'unit' => 'Tấn', 'desc' => 'Thép cán nóng Hòa Phát', 'min_stock' => 5],
            ['name' => 'Thép thanh vằn D10', 'unit' => 'Tấn', 'desc' => 'Thép Việt Nhật', 'min_stock' => 5],
            ['name' => 'Bộ giàn giáo chữ H', 'unit' => 'Bộ', 'desc' => 'Giàn giáo xây dựng mạ kẽm', 'min_stock' => 20],
            ['name' => 'Gạch thẻ Solid', 'unit' => 'Cái', 'desc' => 'Gạch xây đặc', 'min_stock' => 5000],
            ['name' => 'Gạch tuynel 2 lỗ', 'unit' => 'Cái', 'desc' => 'Gạch xây tường ngăn', 'min_stock' => 2000],
            ['name' => 'Gạch men lát nền 60x60', 'unit' => 'Cái', 'desc' => 'Gạch Ceramic', 'min_stock' => 500],
            ['name' => 'Băng keo điện NANO', 'unit' => 'Cái', 'desc' => 'Băng dán cách điện', 'min_stock' => 200],
            ['name' => 'Dây cáp điện CADIVI 2.5', 'unit' => 'Mỗi', 'desc' => 'Cáp điện đôi', 'min_stock' => 100],
            ['name' => 'Dây cáp điện CADIVI 4.0', 'unit' => 'Mỗi', 'desc' => 'Cáp điện loại dày', 'min_stock' => 50],
            ['name' => 'Sơn Dulux Weathershield', 'unit' => 'Cái', 'desc' => 'Sơn ngoại thất thùng 18L', 'min_stock' => 30],
            ['name' => 'Sơn Maxilite nội thất', 'unit' => 'Cái', 'desc' => 'Sơn lót thùng xanh', 'min_stock' => 50],
            ['name' => 'Bóng đèn Led âm trần 18W', 'unit' => 'Cái', 'desc' => 'Đèn Rạng Đông Ánh sáng trắng', 'min_stock' => 200],
            ['name' => 'Cờ lê 10-12', 'unit' => 'Cái', 'desc' => 'Dụng cụ sửa chữa', 'min_stock' => 45],
            ['name' => 'Tuốc nơ vít dẹp', 'unit' => 'Cái', 'desc' => 'Dụng cụ sửa chữa đa năng', 'min_stock' => 30],
            ['name' => 'Máy khoan pin Makita', 'unit' => 'Bộ', 'desc' => 'Máy khoan cầm tay 18V', 'min_stock' => 10],
            ['name' => 'Đinh thép 5 phân', 'unit' => 'Tấn', 'desc' => 'Đinh đóng gỗ', 'min_stock' => 2]
        ];

        foreach ($materials as $item) {
            $unit = $units->firstWhere('name', $item['unit']) ?? $units->first();

            Material::updateOrCreate(
                ['name' => $item['name']],
                [
                    'unit_id' => $unit->id,
                    'description' => $item['desc'],
                    'min_stock' => $item['min_stock'],
                ]
            );
        }
    }
}
