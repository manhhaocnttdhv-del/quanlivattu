<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $this->add('inventory_entry_details', ['material_id', 'inventory_entry_id'], 'idx_entry_det_mat_entry');
        $this->add('inventory_exit_details',  ['material_id', 'inventory_exit_id'],  'idx_exit_det_mat_exit');

        // Bao phủ filter date + status thường dùng cho dashboard top N
        $this->add('inventory_entries', ['date', 'status'], 'idx_entries_date_status');
        $this->add('inventory_exits',   ['date', 'status'], 'idx_exits_date_status');
    }

    public function down(): void
    {
        $this->drop('inventory_entry_details', 'idx_entry_det_mat_entry');
        $this->drop('inventory_exit_details',  'idx_exit_det_mat_exit');
        $this->drop('inventory_entries',       'idx_entries_date_status');
        $this->drop('inventory_exits',         'idx_exits_date_status');
    }

    private function add(string $t, array $cols, string $name): void
    {
        $exists = collect(DB::select("SHOW INDEX FROM `$t` WHERE Key_name = ?", [$name]))->isNotEmpty();
        if ($exists) return;
        Schema::table($t, fn(Blueprint $b) => $b->index($cols, $name));
    }

    private function drop(string $t, string $name): void
    {
        $exists = collect(DB::select("SHOW INDEX FROM `$t` WHERE Key_name = ?", [$name]))->isNotEmpty();
        if (! $exists) return;
        Schema::table($t, fn(Blueprint $b) => $b->dropIndex($name));
    }
};
