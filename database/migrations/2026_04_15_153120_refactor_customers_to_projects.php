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
        if (Schema::hasTable('customers')) {
            Schema::rename('customers', 'projects');
        }

        if (Schema::hasColumn('inventory_exits', 'customer_id')) {
            Schema::table('inventory_exits', function (Blueprint $table) {
                // To avoid issues with foreign keys in sqlite/mysql, we might need to drop foreign first if it exists,
                // but since Laravel 8+ renameColumn does handle it or we can just rename.
                // It is safer to drop foreign key first if it exists.
                // $table->dropForeign(['customer_id']); 
                $table->renameColumn('customer_id', 'project_id');
                // $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('inventory_exits', 'project_id')) {
            Schema::table('inventory_exits', function (Blueprint $table) {
                // $table->dropForeign(['project_id']);
                $table->renameColumn('project_id', 'customer_id');
                // $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('projects')) {
            Schema::rename('projects', 'customers');
        }
    }
};
