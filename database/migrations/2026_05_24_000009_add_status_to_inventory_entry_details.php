<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_entry_details', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved'])->default('pending')->after('location');
        });

        // Backfill: các dòng thuộc phiếu đã completed/cancelled -> approved
        DB::statement("
            UPDATE inventory_entry_details
            SET status = 'approved'
            WHERE inventory_entry_id IN (
                SELECT id FROM inventory_entries WHERE status IN ('completed', 'cancelled')
            )
        ");
    }

    public function down(): void
    {
        Schema::table('inventory_entry_details', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
