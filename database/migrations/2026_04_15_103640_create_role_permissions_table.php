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
        Schema::create('role_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('role');                     // 'Admin tổng' | 'Admin kho' | 'Nhân viên kho'
            $table->string('permission_name');           // tên quyền, ví dụ: 'Xem danh sách vật tư'
            $table->string('group_name');                // nhóm quyền
            $table->string('description')->nullable();   // mô tả
            $table->boolean('is_granted')->default(false);
            $table->timestamps();

            $table->unique(['role', 'permission_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_permissions');
    }
};
