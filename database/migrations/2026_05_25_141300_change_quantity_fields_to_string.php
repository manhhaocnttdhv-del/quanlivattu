<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventory_entry_details', function (Blueprint $table) {
            $table->string('quantity')->change();
        });

        Schema::table('inventory_exit_details', function (Blueprint $table) {
            $table->string('quantity')->change();
        });

        Schema::table('inventory_transfer_details', function (Blueprint $table) {
            $table->string('quantity')->change();
        });

        Schema::table('material_warehouses', function (Blueprint $table) {
            $table->string('stock')->change();
        });

        Schema::table('inventory_check_details', function (Blueprint $table) {
            $table->string('system_stock')->change();
            $table->string('actual_stock')->change();
            $table->string('variance')->change();
        });

        Schema::table('project_materials', function (Blueprint $table) {
            $table->string('estimated_quantity')->change();
        });

        Schema::table('purchase_order_details', function (Blueprint $table) {
            $table->string('quantity')->change();
        });

        Schema::table('material_batches', function (Blueprint $table) {
            $table->string('stock')->change();
        });

        Schema::table('inventory_alerts', function (Blueprint $table) {
            $table->string('current_stock')->change();
            $table->string('min_stock_level')->change();
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->string('min_stock')->nullable()->change();
            $table->string('max_stock')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
