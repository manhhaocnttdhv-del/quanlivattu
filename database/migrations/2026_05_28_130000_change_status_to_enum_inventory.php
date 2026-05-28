<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Đổi cột `status` của các bảng phiếu (inventory_entries / exits / transfers / checks)
 * từ varchar(255) → ENUM cho gọn, có ràng buộc giá trị, tốc độ filter nhanh hơn.
 *
 * Cũng đảm bảo default rõ ràng cho status của *_details.
 */
return new class extends Migration {
    public function up(): void
    {
        // Cleanup giá trị NULL / không hợp lệ trước khi đổi sang ENUM
        DB::table('inventory_entries')->whereNull('status')->update(['status' => 'pending']);
        DB::table('inventory_exits')->whereNull('status')->update(['status' => 'pending']);
        if (Schema::hasTable('inventory_transfers')) {
            DB::table('inventory_transfers')->whereNull('status')->update(['status' => 'pending']);
        }
        if (Schema::hasTable('inventory_checks')) {
            DB::table('inventory_checks')->whereNull('status')->update(['status' => 'pending']);
        }

        // Bắt buộc cài doctrine/dbal khi change(). Nếu không có, dùng raw SQL.
        // Dùng raw để khỏi phụ thuộc package.
        DB::statement("ALTER TABLE `inventory_entries` MODIFY `status` ENUM('pending','completed','cancelled') NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE `inventory_exits`   MODIFY `status` ENUM('pending','completed','cancelled') NOT NULL DEFAULT 'pending'");

        if (Schema::hasTable('inventory_transfers')) {
            DB::statement("ALTER TABLE `inventory_transfers` MODIFY `status` ENUM('pending','completed','cancelled') NOT NULL DEFAULT 'pending'");
        }
        if (Schema::hasTable('inventory_checks')) {
            DB::statement("ALTER TABLE `inventory_checks` MODIFY `status` ENUM('pending','completed','cancelled') NOT NULL DEFAULT 'pending'");
        }

        // Đảm bảo *_details có default 'pending' (đã enum sẵn, chỉ chắc default)
        if (Schema::hasColumn('inventory_entry_details', 'status')) {
            DB::statement("ALTER TABLE `inventory_entry_details` MODIFY `status` ENUM('pending','approved') NOT NULL DEFAULT 'pending'");
        }
        if (Schema::hasColumn('inventory_exit_details', 'status')) {
            DB::statement("ALTER TABLE `inventory_exit_details` MODIFY `status` ENUM('pending','approved') NOT NULL DEFAULT 'pending'");
        }
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE `inventory_entries` MODIFY `status` VARCHAR(255) NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE `inventory_exits`   MODIFY `status` VARCHAR(255) NOT NULL DEFAULT 'pending'");

        if (Schema::hasTable('inventory_transfers')) {
            DB::statement("ALTER TABLE `inventory_transfers` MODIFY `status` VARCHAR(255) NOT NULL DEFAULT 'pending'");
        }
        if (Schema::hasTable('inventory_checks')) {
            DB::statement("ALTER TABLE `inventory_checks` MODIFY `status` VARCHAR(255) NOT NULL DEFAULT 'pending'");
        }
    }
};
