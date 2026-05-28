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
        Schema::table('inventory_entries', function (Blueprint $table) {
            $table->foreignId('delivery_partner_id')->nullable()->constrained('delivery_partners')->onDelete('set null');
            $table->string('delivery_status')->default('pending'); // pending, in_transit, delivered, failed
            $table->decimal('delivery_fee', 15, 2)->default(0);
            $table->string('delivery_code')->nullable();
        });

        Schema::table('inventory_exits', function (Blueprint $table) {
            $table->foreignId('delivery_partner_id')->nullable()->constrained('delivery_partners')->onDelete('set null');
            $table->string('delivery_status')->default('pending');
            $table->decimal('delivery_fee', 15, 2)->default(0);
            $table->string('delivery_code')->nullable();
        });

        Schema::table('inventory_transfers', function (Blueprint $table) {
            $table->foreignId('delivery_partner_id')->nullable()->constrained('delivery_partners')->onDelete('set null');
            $table->string('delivery_status')->default('pending');
            $table->decimal('delivery_fee', 15, 2)->default(0);
            $table->string('delivery_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_entries', function (Blueprint $table) {
            $table->dropForeign(['delivery_partner_id']);
            $table->dropColumn(['delivery_partner_id', 'delivery_status', 'delivery_fee', 'delivery_code']);
        });

        Schema::table('inventory_exits', function (Blueprint $table) {
            $table->dropForeign(['delivery_partner_id']);
            $table->dropColumn(['delivery_partner_id', 'delivery_status', 'delivery_fee', 'delivery_code']);
        });

        Schema::table('inventory_transfers', function (Blueprint $table) {
            $table->dropForeign(['delivery_partner_id']);
            $table->dropColumn(['delivery_partner_id', 'delivery_status', 'delivery_fee', 'delivery_code']);
        });
    }
};
