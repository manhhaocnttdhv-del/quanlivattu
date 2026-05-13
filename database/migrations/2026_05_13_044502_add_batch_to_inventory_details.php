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
            $table->string('batch_code')->nullable()->after('quantity');
            $table->date('expiry_date')->nullable()->after('batch_code');
        });

        Schema::table('inventory_exit_details', function (Blueprint $table) {
            $table->string('batch_code')->nullable()->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_exit_details', function (Blueprint $table) {
            $table->dropColumn(['batch_code']);
        });

        Schema::table('inventory_entry_details', function (Blueprint $table) {
            $table->dropColumn(['batch_code', 'expiry_date']);
        });
    }
};
