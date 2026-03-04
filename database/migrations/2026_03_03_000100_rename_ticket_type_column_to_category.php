<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'ticket_type') && !Schema::hasColumn('tickets', 'category')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->renameColumn('ticket_type', 'category');
            });
        }

        if (Schema::hasTable('category') && Schema::hasColumn('category', 'ticket_type') && !Schema::hasColumn('category', 'category')) {
            Schema::table('category', function (Blueprint $table) {
                $table->renameColumn('ticket_type', 'category');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'category') && !Schema::hasColumn('tickets', 'ticket_type')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->renameColumn('category', 'ticket_type');
            });
        }

        if (Schema::hasTable('category') && Schema::hasColumn('category', 'category') && !Schema::hasColumn('category', 'ticket_type')) {
            Schema::table('category', function (Blueprint $table) {
                $table->renameColumn('category', 'ticket_type');
            });
        }
    }
};
