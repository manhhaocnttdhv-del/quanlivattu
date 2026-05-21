<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Seed dữ liệu mẫu cho bảng categories (Nhóm vật tư).
     * Cấu trúc: Nhóm cha → Nhóm con (hỗ trợ parent_id).
     */
    public function run(): void
    {
        $categories = [
            // ── 1. Vật liệu xây dựng ──────────────────────────────────────
            [
                'name'        => 'Vật liệu xây dựng',
                'description' => 'Các loại vật liệu dùng trong thi công xây dựng',
                'parent_id'   => null,
                'children'    => [
                    ['name' => 'Xi măng',      'description' => 'Xi măng các loại (PCB30, PCB40...)'],
                    ['name' => 'Cát - Đá',     'description' => 'Cát xây, cát san lấp, đá dăm, đá 1x2'],
                    ['name' => 'Gạch - Ngói',  'description' => 'Gạch nung, gạch không nung, ngói lợp'],
                    ['name' => 'Thép xây dựng','description' => 'Thép cây, thép cuộn, thép hình'],
                ],
            ],

            // ── 2. Vật tư điện ────────────────────────────────────────────
            [
                'name'        => 'Vật tư điện',
                'description' => 'Thiết bị và vật tư hệ thống điện',
                'parent_id'   => null,
                'children'    => [
                    ['name' => 'Dây & Cáp điện',    'description' => 'Dây đơn, dây đôi, cáp ngầm, cáp treo'],
                    ['name' => 'Thiết bị đóng cắt',  'description' => 'CB, MCCB, RCCB, cầu dao, cầu chì'],
                    ['name' => 'Ống luồn dây điện',  'description' => 'Ống nhựa cứng, ống mềm, ống thép'],
                    ['name' => 'Đèn chiếu sáng',     'description' => 'Đèn LED, đèn huỳnh quang, đèn pha'],
                ],
            ],

            // ── 3. Vật tư cơ khí ──────────────────────────────────────────
            [
                'name'        => 'Vật tư cơ khí',
                'description' => 'Phụ kiện và vật tư cơ khí công nghiệp',
                'parent_id'   => null,
                'children'    => [
                    ['name' => 'Bu lông - Đai ốc',  'description' => 'Bu lông, đai ốc, vòng đệm các cỡ'],
                    ['name' => 'Ổ bi - Vòng bi',    'description' => 'Ổ bi đỡ, ổ bi chặn, ổ lăn'],
                    ['name' => 'Dây đai - Xích',    'description' => 'Dây đai truyền động, xích công nghiệp'],
                    ['name' => 'Dụng cụ cầm tay',   'description' => 'Búa, kìm, tua vít, cờ lê, mỏ lết'],
                ],
            ],

            // ── 4. Vật tư nước & vệ sinh ──────────────────────────────────
            [
                'name'        => 'Vật tư nước & vệ sinh',
                'description' => 'Ống nước, phụ kiện cấp thoát nước',
                'parent_id'   => null,
                'children'    => [
                    ['name' => 'Ống nước PVC',      'description' => 'Ống PVC cứng, ống uPVC cấp thoát nước'],
                    ['name' => 'Ống PPR - HDPE',    'description' => 'Ống PPR nóng lạnh, ống HDPE chịu áp'],
                    ['name' => 'Van - Khóa nước',   'description' => 'Van cổng, van bi, van một chiều'],
                    ['name' => 'Phụ kiện ống nước', 'description' => 'Co, tê, nối, măng sông, đầu nối'],
                ],
            ],

            // ── 5. Thiết bị bảo hộ lao động ──────────────────────────────
            [
                'name'        => 'Bảo hộ lao động',
                'description' => 'Trang thiết bị bảo hộ cá nhân cho công nhân',
                'parent_id'   => null,
                'children'    => [
                    ['name' => 'Bảo hộ đầu & mắt',  'description' => 'Mũ bảo hộ, kính bảo hộ, mặt nạ'],
                    ['name' => 'Bảo hộ tay & chân',  'description' => 'Găng tay, ủng bảo hộ, giày mũi thép'],
                    ['name' => 'Quần áo bảo hộ',     'description' => 'Áo phản quang, quần áo chống cháy'],
                ],
            ],

            // ── 6. Vật tư văn phòng & tiêu hao ───────────────────────────
            [
                'name'        => 'Vật tư văn phòng & tiêu hao',
                'description' => 'Văn phòng phẩm và vật tư tiêu hao hàng ngày',
                'parent_id'   => null,
                'children'    => [
                    ['name' => 'Văn phòng phẩm',    'description' => 'Giấy in, bút, mực, băng dính, hồ sơ'],
                    ['name' => 'Vật tư vệ sinh',    'description' => 'Chổi, cây lau nhà, nước tẩy rửa'],
                    ['name' => 'Vật tư tiêu hao',   'description' => 'Lưỡi cưa, đá mài, mũi khoan, lưỡi dao'],
                ],
            ],
        ];

        foreach ($categories as $parentData) {
            $children = $parentData['children'] ?? [];
            unset($parentData['children']);

            // Tạo nhóm cha
            $parent = Category::firstOrCreate(
                ['name' => $parentData['name']],
                [
                    'description' => $parentData['description'],
                    'parent_id'   => null,
                ]
            );

            // Tạo nhóm con
            foreach ($children as $child) {
                Category::firstOrCreate(
                    ['name' => $child['name']],
                    [
                        'description' => $child['description'],
                        'parent_id'   => $parent->id,
                    ]
                );
            }
        }

        $this->command->info('✅ CategorySeeder: Đã tạo ' . Category::count() . ' nhóm vật tư (cha + con).');
    }
}
