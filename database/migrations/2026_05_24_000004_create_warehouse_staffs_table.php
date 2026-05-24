<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_staffs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('warehouse_id');
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('id_card')->nullable()->comment('CCCD/CMND');
            $table->text('address')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female', 'other'])->default('male');
            $table->string('position')->nullable()->comment('Chức vụ: thủ kho, NV nhập kho, NV xuất kho...');
            $table->date('start_date')->nullable()->comment('Ngày bắt đầu làm việc');
            $table->decimal('base_salary', 15, 2)->default(0)->comment('Lương cơ bản/tháng');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouse_staffs');
    }
};
