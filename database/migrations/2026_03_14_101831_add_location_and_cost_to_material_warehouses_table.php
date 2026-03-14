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
        Schema::table('material_warehouses', function (Blueprint $table) {
            $table->string('location')->nullable()->after('stock');
            $table->decimal('average_cost', 15, 2)->default(0)->after('location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_warehouses', function (Blueprint $table) {
            $table->dropColumn(['location', 'average_cost']);
        });
    }
};
