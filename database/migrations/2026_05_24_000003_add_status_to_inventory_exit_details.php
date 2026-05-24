<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_exit_details', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved'])->default('pending')->after('location');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_exit_details', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
