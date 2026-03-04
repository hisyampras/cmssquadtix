<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'event_id') && !Schema::hasColumn('tickets', 'events_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->renameColumn('event_id', 'events_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'events_id') && !Schema::hasColumn('tickets', 'event_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->renameColumn('events_id', 'event_id');
            });
        }
    }
};
