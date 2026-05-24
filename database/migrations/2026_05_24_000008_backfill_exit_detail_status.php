<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Cập nhật các dòng exit_detail cũ (phiếu đã completed/cancelled) 
     * sang status=approved để không bị ảnh hưởng bởi feature mới
     */
    public function up(): void
    {
        // Các dòng thuộc phiếu đã completed -> approved
        DB::statement("
            UPDATE inventory_exit_details
            SET status = 'approved'
            WHERE inventory_exit_id IN (
                SELECT id FROM inventory_exits WHERE status IN ('completed', 'cancelled')
            )
        ");
    }

    public function down(): void
    {
        // Rollback: đặt tất cả về pending (chỉ dùng khi rollback thủ công)
        DB::table('inventory_exit_details')->update(['status' => 'pending']);
    }
};
