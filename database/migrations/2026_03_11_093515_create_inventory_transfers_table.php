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
        Schema::create('inventory_transfers', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('from_warehouse_id')->constrained('warehouses');
            $table->foreignId('to_warehouse_id')->constrained('warehouses');
            $table->foreignId('user_id')->constrained('users');
            $table->string('status')->default('pending'); // pending, sent, received, cancelled
            $table->timestamps();
        });

        Schema::create('inventory_transfer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_transfer_id')->constrained('inventory_transfers')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials');
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_transfers');
    }
};
