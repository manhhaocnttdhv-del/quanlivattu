<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed default settings
        DB::table('app_settings')->insert([
            ['key' => 'default_min_stock_level', 'value' => '10', 'description' => 'Mức tồn kho tối thiểu mặc định (áp dụng khi vật tư chưa cài riêng)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'standard_work_days', 'value' => '26', 'description' => 'Số ngày công chuẩn trong tháng để tính lương', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};
