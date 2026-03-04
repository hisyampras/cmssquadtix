<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('ticket_type_policies') && !Schema::hasTable('category_policies')) {
            Schema::rename('ticket_type_policies', 'category_policies');
        }

        if (Schema::hasTable('category_policies') && Schema::hasColumn('category_policies', 'ticket_type_id') && !Schema::hasColumn('category_policies', 'category_id')) {
            Schema::table('category_policies', function (Blueprint $table) {
                $table->renameColumn('ticket_type_id', 'category_id');
            });
        }

        if (Schema::hasTable('group_gates') && Schema::hasColumn('group_gates', 'ticket_type_id') && !Schema::hasColumn('group_gates', 'category_id')) {
            Schema::table('group_gates', function (Blueprint $table) {
                $table->renameColumn('ticket_type_id', 'category_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('group_gates') && Schema::hasColumn('group_gates', 'category_id') && !Schema::hasColumn('group_gates', 'ticket_type_id')) {
            Schema::table('group_gates', function (Blueprint $table) {
                $table->renameColumn('category_id', 'ticket_type_id');
            });
        }

        if (Schema::hasTable('category_policies') && Schema::hasColumn('category_policies', 'category_id') && !Schema::hasColumn('category_policies', 'ticket_type_id')) {
            Schema::table('category_policies', function (Blueprint $table) {
                $table->renameColumn('category_id', 'ticket_type_id');
            });
        }

        if (Schema::hasTable('category_policies') && !Schema::hasTable('ticket_type_policies')) {
            Schema::rename('category_policies', 'ticket_type_policies');
        }
    }
};
