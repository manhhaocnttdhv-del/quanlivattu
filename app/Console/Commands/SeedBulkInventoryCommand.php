<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeedBulkInventoryCommand extends Command
{
    /**
     * php artisan seed:bulk-inventory --entries=100000 --exits=100000 --chunk=2000
     */
    protected $signature = 'seed:bulk-inventory
                            {--entries=100000 : Số phiếu nhập kho cần tạo}
                            {--exits=100000 : Số phiếu xuất kho cần tạo}
                            {--chunk=2000 : Số dòng / batch insert}
                            {--fresh : Xoá dữ liệu phiếu nhập/xuất cũ trước khi seed}';

    protected $description = 'Seed phiếu nhập / xuất kho khối lượng lớn (bulk insert nhanh)';

    public function handle(): int
    {
        $totalEntries = (int) $this->option('entries');
        $totalExits   = (int) $this->option('exits');
        $chunk        = max(100, (int) $this->option('chunk'));
        $fresh        = (bool) $this->option('fresh');

        // ===== Lấy dữ liệu nền =====
        $warehouseIds = DB::table('warehouses')->pluck('id')->all();
        $supplierIds  = DB::table('suppliers')->pluck('id')->all();
        $projectIds   = DB::table('projects')->pluck('id')->all();
        $materialIds  = DB::table('materials')->pluck('id')->all();
        $userIds      = DB::table('users')->pluck('id')->all();
        $partnerIds   = DB::table('delivery_partners')->pluck('id')->all();

        if (empty($warehouseIds) || empty($materialIds) || empty($userIds)) {
            $this->error('Thiếu dữ liệu nền (warehouses / materials / users). Hãy chạy seed cơ bản trước.');
            return self::FAILURE;
        }
        if (empty($supplierIds)) {
            $this->error('Thiếu suppliers — cần ít nhất 1 nhà cung cấp.');
            return self::FAILURE;
        }
        if (empty($projectIds)) {
            $this->error('Thiếu projects — cần ít nhất 1 công trình.');
            return self::FAILURE;
        }

        if ($fresh) {
            $this->warn('Xoá dữ liệu phiếu nhập / xuất cũ ...');
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::table('inventory_entry_details')->truncate();
            DB::table('inventory_exit_details')->truncate();
            DB::table('inventory_entries')->truncate();
            DB::table('inventory_exits')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
        }

        // Tắt log query để tránh ngốn RAM
        DB::disableQueryLog();

        if ($totalEntries > 0) {
            $this->seedEntries($totalEntries, $chunk, $warehouseIds, $supplierIds, $userIds, $partnerIds, $materialIds);
        }

        if ($totalExits > 0) {
            $this->seedExits($totalExits, $chunk, $warehouseIds, $projectIds, $userIds, $partnerIds, $materialIds);
        }

        $this->info('🎉 HOÀN TẤT.');
        return self::SUCCESS;
    }

    /* =====================================================
     * PHIẾU NHẬP
     * ===================================================== */
    private function seedEntries(int $total, int $chunk, array $whs, array $sups, array $users, array $partners, array $mats): void
    {
        $this->info("→ Sinh {$total} phiếu NHẬP kho ...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $now      = Carbon::now();
        $statuses = ['completed', 'completed', 'completed', 'pending', 'cancelled']; // bias completed
        $delStatuses = ['delivered', 'delivered', 'in_transit', 'pending'];

        $created = 0;
        while ($created < $total) {
            $batchSize = min($chunk, $total - $created);

            DB::transaction(function () use ($batchSize, $whs, $sups, $users, $partners, $mats, $now, $statuses, $delStatuses, &$created, $bar) {
                $headers = [];
                for ($i = 0; $i < $batchSize; $i++) {
                    $headers[] = [
                        'date'                => $now->copy()->subDays(random_int(0, 365))->toDateString(),
                        'warehouse_id'        => $whs[array_rand($whs)],
                        'supplier_id'         => $sups[array_rand($sups)],
                        'user_id'             => $users[array_rand($users)],
                        'status'              => $statuses[array_rand($statuses)],
                        'note'                => 'Bulk seed nhập #' . ($created + $i + 1),
                        'delivery_partner_id' => !empty($partners) && random_int(0, 1) ? $partners[array_rand($partners)] : null,
                        'delivery_status'     => $delStatuses[array_rand($delStatuses)],
                        'delivery_fee'        => random_int(0, 5_000_000),
                        'delivery_code'       => 'VD' . random_int(10000, 99999),
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ];
                }

                // bulk insert headers, rồi lấy id liên tiếp (MySQL: LAST_INSERT_ID là id đầu tiên)
                DB::table('inventory_entries')->insert($headers);
                $firstId = (int) DB::getPdo()->lastInsertId();

                // Sinh details (1-3 dòng / phiếu)
                $details = [];
                for ($i = 0; $i < $batchSize; $i++) {
                    $entryId  = $firstId + $i;
                    $lineCnt  = random_int(1, 3);
                    for ($j = 0; $j < $lineCnt; $j++) {
                        $details[] = [
                            'inventory_entry_id' => $entryId,
                            'material_id'        => $mats[array_rand($mats)],
                            'quantity'           => random_int(10, 500),
                            'unit_price'         => random_int(10000, 500000),
                            'batch_code'         => 'B' . random_int(1000, 9999),
                            'expiry_date'        => null,
                            'location'           => 'A' . random_int(1, 20),
                            'status'             => 'approved',
                            'created_at'         => $now,
                            'updated_at'         => $now,
                        ];
                    }
                }

                // Insert details theo lô con (MySQL max_allowed_packet)
                foreach (array_chunk($details, 1000) as $piece) {
                    DB::table('inventory_entry_details')->insert($piece);
                }
            });

            $created += $batchSize;
            $bar->advance($batchSize);
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Đã tạo {$total} phiếu nhập kho.");
    }

    /* =====================================================
     * PHIẾU XUẤT
     * ===================================================== */
    private function seedExits(int $total, int $chunk, array $whs, array $projs, array $users, array $partners, array $mats): void
    {
        $this->info("→ Sinh {$total} phiếu XUẤT kho ...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $now         = Carbon::now();
        $statuses    = ['completed', 'completed', 'completed', 'pending', 'cancelled'];
        $delStatuses = ['delivered', 'delivered', 'in_transit', 'pending', 'failed'];

        $created = 0;
        while ($created < $total) {
            $batchSize = min($chunk, $total - $created);

            DB::transaction(function () use ($batchSize, $whs, $projs, $users, $partners, $mats, $now, $statuses, $delStatuses, &$created, $bar) {
                $headers = [];
                for ($i = 0; $i < $batchSize; $i++) {
                    $headers[] = [
                        'date'                => $now->copy()->subDays(random_int(0, 365))->toDateString(),
                        'warehouse_id'        => $whs[array_rand($whs)],
                        'project_id'          => $projs[array_rand($projs)],
                        'user_id'             => $users[array_rand($users)],
                        'status'              => $statuses[array_rand($statuses)],
                        'note'                => 'Bulk seed xuất #' . ($created + $i + 1),
                        'delivery_partner_id' => !empty($partners) && random_int(0, 1) ? $partners[array_rand($partners)] : null,
                        'delivery_status'     => $delStatuses[array_rand($delStatuses)],
                        'delivery_fee'        => random_int(0, 5_000_000),
                        'delivery_code'       => 'VD' . random_int(10000, 99999),
                        'created_at'          => $now,
                        'updated_at'          => $now,
                    ];
                }

                DB::table('inventory_exits')->insert($headers);
                $firstId = (int) DB::getPdo()->lastInsertId();

                $details = [];
                for ($i = 0; $i < $batchSize; $i++) {
                    $exitId  = $firstId + $i;
                    $lineCnt = random_int(1, 3);
                    for ($j = 0; $j < $lineCnt; $j++) {
                        $details[] = [
                            'inventory_exit_id' => $exitId,
                            'material_id'       => $mats[array_rand($mats)],
                            'quantity'          => random_int(1, 100),
                            'unit_price'        => random_int(10000, 500000),
                            'batch_code'        => 'B' . random_int(1000, 9999),
                            'location'          => 'A' . random_int(1, 20),
                            'status'            => 'approved',
                            'created_at'        => $now,
                            'updated_at'        => $now,
                        ];
                    }
                }

                foreach (array_chunk($details, 1000) as $piece) {
                    DB::table('inventory_exit_details')->insert($piece);
                }
            });

            $created += $batchSize;
            $bar->advance($batchSize);
        }

        $bar->finish();
        $this->newLine();
        $this->info("✅ Đã tạo {$total} phiếu xuất kho.");
    }
}
