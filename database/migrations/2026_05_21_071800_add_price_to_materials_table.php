<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Thêm cột đơn giá tham khảo (giá mặc định) cho vật tư
     * và cột đơn giá xuất cho chi tiết phiếu xuất.
     */
    public function up(): void
    {
        // Thêm giá tham khảo cho bảng materials
        if (!Schema::hasColumn('materials', 'price')) {
            Schema::table('materials', function (Blueprint $table) {
                $table->decimal('price', 15, 2)->default(0)->after('description')
                      ->comment('Đơn giá tham khảo (VNĐ)');
            });
        }

        // Thêm đơn giá xuất cho chi tiết phiếu xuất
        if (!Schema::hasColumn('inventory_exit_details', 'price')) {
            Schema::table('inventory_exit_details', function (Blueprint $table) {
                $table->decimal('price', 15, 2)->default(0)->after('quantity')
                      ->comment('Đơn giá xuất (VNĐ)');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('price');
        });

        Schema::table('inventory_exit_details', function (Blueprint $table) {
            $table->dropColumn('price');
        });
    }
};
