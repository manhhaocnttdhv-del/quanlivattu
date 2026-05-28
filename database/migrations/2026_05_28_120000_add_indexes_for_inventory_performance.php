<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

/**
 * Thêm index cho các cột hay filter / sort trên bảng inventory_entries / inventory_exits
 * → tăng tốc trang index khi data lên đến hàng trăm nghìn dòng.
 */
return new class extends Migration {
    public function up(): void
    {
        $this->addIndex('inventory_entries', ['date'], 'idx_entries_date');
        $this->addIndex('inventory_entries', ['status'], 'idx_entries_status');
        $this->addIndex('inventory_entries', ['warehouse_id', 'date'], 'idx_entries_warehouse_date');
        $this->addIndex('inventory_entries', ['supplier_id'], 'idx_entries_supplier');

        $this->addIndex('inventory_exits', ['date'], 'idx_exits_date');
        $this->addIndex('inventory_exits', ['status'], 'idx_exits_status');
        $this->addIndex('inventory_exits', ['warehouse_id', 'date'], 'idx_exits_warehouse_date');
        $this->addIndex('inventory_exits', ['project_id'], 'idx_exits_project');
    }

    public function down(): void
    {
        $this->dropIndex('inventory_entries', 'idx_entries_date');
        $this->dropIndex('inventory_entries', 'idx_entries_status');
        $this->dropIndex('inventory_entries', 'idx_entries_warehouse_date');
        $this->dropIndex('inventory_entries', 'idx_entries_supplier');

        $this->dropIndex('inventory_exits', 'idx_exits_date');
        $this->dropIndex('inventory_exits', 'idx_exits_status');
        $this->dropIndex('inventory_exits', 'idx_exits_warehouse_date');
        $this->dropIndex('inventory_exits', 'idx_exits_project');
    }

    private function addIndex(string $table, array $cols, string $name): void
    {
        $exists = collect(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$name]))->isNotEmpty();
        if ($exists) return;
        Schema::table($table, function (Blueprint $t) use ($cols, $name) {
            $t->index($cols, $name);
        });
    }

    private function dropIndex(string $table, string $name): void
    {
        $exists = collect(DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$name]))->isNotEmpty();
        if (! $exists) return;
        Schema::table($table, function (Blueprint $t) use ($name) {
            $t->dropIndex($name);
        });
    }
};
