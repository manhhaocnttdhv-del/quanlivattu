<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tách cột price thành cost_price (giá nhập/giá vốn) và selling_price (giá bán).
     */
    public function up(): void
    {
        // 1. materials: đổi price → cost_price, thêm selling_price
        Schema::table('materials', function (Blueprint $table) {
            $table->renameColumn('price', 'cost_price');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->decimal('selling_price', 15, 2)->default(0)->after('cost_price')
                  ->comment('Giá bán / Giá xuất kho (VNĐ)');
        });

        // Mặc định selling_price = cost_price * 1.1 (lợi nhuận 10%)
        DB::statement('UPDATE materials SET selling_price = cost_price * 1.1');

        // 2. inventory_entry_details: đổi price → unit_price cho rõ ràng
        Schema::table('inventory_entry_details', function (Blueprint $table) {
            $table->renameColumn('price', 'unit_price');
        });

        // 3. inventory_exit_details: đổi price → unit_price
        Schema::table('inventory_exit_details', function (Blueprint $table) {
            $table->renameColumn('price', 'unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_exit_details', function (Blueprint $table) {
            $table->renameColumn('unit_price', 'price');
        });

        Schema::table('inventory_entry_details', function (Blueprint $table) {
            $table->renameColumn('unit_price', 'price');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('selling_price');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->renameColumn('cost_price', 'price');
        });
    }
};
