<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedSmallInteger('month');
            $table->unsignedSmallInteger('year');
            $table->unsignedSmallInteger('standard_work_days')->default(26)->comment('Ngày công chuẩn trong tháng');
            $table->decimal('total_work_days', 5, 1)->default(0)->comment('Tổng ngày làm thực tế');
            $table->decimal('base_salary', 15, 2)->default(0)->comment('Snapshot lương cơ bản lúc tính');
            $table->decimal('actual_salary', 15, 2)->default(0)->comment('Lương = base * actual / standard');
            $table->decimal('bonus', 15, 2)->default(0)->comment('Thưởng');
            $table->decimal('deduction', 15, 2)->default(0)->comment('Khấu trừ');
            $table->decimal('final_salary', 15, 2)->default(0)->comment('Lương cuối = actual + bonus - deduction');
            $table->enum('status', ['draft', 'confirmed', 'paid'])->default('draft');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('staff_id')->references('id')->on('warehouse_staffs')->onDelete('cascade');
            $table->unique(['staff_id', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_records');
    }
};
