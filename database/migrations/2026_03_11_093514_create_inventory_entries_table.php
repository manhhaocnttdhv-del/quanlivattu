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
        Schema::create('inventory_entries', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->foreignId('warehouse_id')->constrained('warehouses');
            $table->foreignId('supplier_id')->constrained('suppliers');
            $table->foreignId('user_id')->constrained('users');
            $table->string('status')->default('pending'); // pending, confirmed, cancelled
            $table->timestamps();
        });

        Schema::create('inventory_entry_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_entry_id')->constrained('inventory_entries')->onDelete('cascade');
            $table->foreignId('material_id')->constrained('materials');
            $table->integer('quantity');
            $table->decimal('price', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_entries');
    }
};
