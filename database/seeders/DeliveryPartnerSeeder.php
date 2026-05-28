<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DeliveryPartner;
use App\Models\InventoryEntry;
use App\Models\InventoryExit;
use App\Models\InventoryTransfer;
use Illuminate\Support\Facades\DB;

class DeliveryPartnerSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Delivery Partners & Vehicles
        $partnersData = [
            [
                'name' => 'Xe tải Suzuki Carry 1.5 Tấn',
                'type' => 'internal',
                'license_plate' => '29C-452.18',
                'driver_name' => 'Nguyễn Văn Hải',
                'driver_phone' => '0981223344',
                'status' => 'active',
                'note' => 'Xe chuyên dụng đi dự án nội thành',
            ],
            [
                'name' => 'Xe tải Isuzu NPR85 3.5 Tấn',
                'type' => 'internal',
                'license_plate' => '29C-998.44',
                'driver_name' => 'Lê Minh Tuấn',
                'driver_phone' => '0915667788',
                'status' => 'active',
                'note' => 'Xe tải nặng chuyển hàng liên tỉnh',
            ],
            [
                'name' => 'Xe bán tải Ford Ranger Wildtrak',
                'type' => 'internal',
                'license_plate' => '29D-333.66',
                'driver_name' => 'Trần Quang Hùng',
                'driver_phone' => '0978990011',
                'status' => 'active',
                'note' => 'Hỗ trợ giao nhanh vật tư nhỏ',
            ],
            [
                'name' => 'Giao Hàng Nhanh (GHN)',
                'type' => 'external',
                'contact_name' => 'Nguyễn Thị Hương',
                'contact_phone' => '1900636677',
                'status' => 'active',
                'note' => 'Đối tác ký hợp đồng giao lẻ',
            ],
            [
                'name' => 'Tổng công ty Cổ phần Bưu chính Viettel (Viettel Post)',
                'type' => 'external',
                'contact_name' => 'Phạm Văn Nam',
                'contact_phone' => '19008095',
                'status' => 'active',
                'note' => 'Đối tác giao các tỉnh miền Trung & Nam',
            ],
            [
                'name' => 'Nhà xe Vận tải Hưng Thịnh',
                'type' => 'external',
                'contact_name' => 'Vũ Đình Cường',
                'contact_phone' => '0904123456',
                'status' => 'active',
                'note' => 'Đối tác thuê ngoài xe tải lớn ghép chuyến',
            ],
            [
                'name' => 'Xe tải Huyndai HD120 (Tạm ngưng)',
                'type' => 'internal',
                'license_plate' => '30F-555.22',
                'driver_name' => 'Nguyễn Hữu Đạt',
                'driver_phone' => '0944556677',
                'status' => 'inactive',
                'note' => 'Xe đang đi bảo dưỡng định kỳ dài ngày',
            ]
        ];

        foreach ($partnersData as $data) {
            DeliveryPartner::updateOrCreate(['name' => $data['name']], $data);
        }

        // Get active partners
        $partners = DeliveryPartner::where('status', 'active')->get();

        if ($partners->isEmpty()) {
            return;
        }

        // 2. Backfill existing completed & pending entries
        $entries = InventoryEntry::all();
        foreach ($entries as $index => $entry) {
            // Assign delivery info to 80% of entries
            if ($index % 5 !== 0) {
                $partner = $partners->random();
                $entry->update([
                    'delivery_partner_id' => $partner->id,
                    'delivery_status' => $this->getRandomStatus($entry->status),
                    'delivery_fee' => rand(1, 15) * 50000, // 50k to 750k
                    'delivery_code' => 'SHIP-IN-' . str_pad($entry->id, 4, '0', STR_PAD_LEFT)
                ]);
            }
        }

        // 3. Backfill exits
        $exits = InventoryExit::all();
        foreach ($exits as $index => $exit) {
            if ($index % 5 !== 0) {
                $partner = $partners->random();
                // Fee proportional to value or random
                $deliveryFee = rand(2, 20) * 50000;
                $entryStatus = $this->getRandomStatus($exit->status);
                
                $exit->update([
                    'delivery_partner_id' => $partner->id,
                    'delivery_status' => $entryStatus,
                    'delivery_fee' => $deliveryFee,
                    'delivery_code' => 'SHIP-OUT-' . str_pad($exit->id, 4, '0', STR_PAD_LEFT)
                ]);
            }
        }

        // 4. Backfill transfers
        $transfers = InventoryTransfer::all();
        foreach ($transfers as $index => $transfer) {
            if ($index % 4 !== 0) {
                $partner = $partners->random();
                $transfer->update([
                    'delivery_partner_id' => $partner->id,
                    'delivery_status' => $this->getRandomStatus($transfer->status),
                    'delivery_fee' => rand(2, 10) * 50000,
                    'delivery_code' => 'SHIP-TR-' . str_pad($transfer->id, 4, '0', STR_PAD_LEFT)
                ]);
            }
        }
    }

    private function getRandomStatus(string $transactionStatus): string
    {
        if ($transactionStatus === 'completed') {
            // Mostly delivered, some in_transit
            $r = rand(1, 10);
            if ($r <= 8) return 'delivered';
            if ($r === 9) return 'in_transit';
            return 'failed';
        } elseif ($transactionStatus === 'pending') {
            // Mostly pending or in_transit
            return rand(1, 2) === 1 ? 'pending' : 'in_transit';
        }
        return 'failed';
    }
}
