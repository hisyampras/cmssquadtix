<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'events_id')) {
            if (DB::getDriverName() === 'mysql') {
                $foreignKeys = DB::table('information_schema.KEY_COLUMN_USAGE')
                    ->select('CONSTRAINT_NAME')
                    ->whereRaw('TABLE_SCHEMA = DATABASE()')
                    ->where('TABLE_NAME', 'tickets')
                    ->where('COLUMN_NAME', 'events_id')
                    ->whereNotNull('REFERENCED_TABLE_NAME')
                    ->pluck('CONSTRAINT_NAME');

                foreach ($foreignKeys as $fkName) {
                    $safeFkName = str_replace('`', '', (string) $fkName);
                    try {
                        DB::statement("ALTER TABLE `tickets` DROP FOREIGN KEY `{$safeFkName}`");
                    } catch (\Throwable) {
                        // ignore missing FK
                    }
                }
            } else {
                try {
                    Schema::table('tickets', function (Blueprint $table) {
                        $table->dropForeign(['events_id']);
                    });
                } catch (\Throwable) {
                    // ignore missing FK
                }
            }

            // Remove legacy indexes that still reference events_id.
            foreach ([
                'tickets_event_id_code_unique',
                'tickets_events_id_code_unique',
                'tickets_events_id_index',
                'tickets_event_id_index',
                'tickets_events_id_category_id_index',
                'tickets_event_id_category_id_index',
            ] as $indexName) {
                try {
                    Schema::table('tickets', function (Blueprint $table) use ($indexName) {
                        $table->dropIndex($indexName);
                    });
                } catch (\Throwable) {
                    // ignore missing index
                }
            }

            try {
                Schema::table('tickets', function (Blueprint $table) {
                    $table->dropColumn('events_id');
                });
            } catch (\Throwable) {
                // ignore if already removed
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tickets') && !Schema::hasColumn('tickets', 'events_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->unsignedBigInteger('events_id')->nullable()->after('id');
            });
            try {
                Schema::table('tickets', function (Blueprint $table) {
                    $table->index('events_id', 'tickets_events_id_index');
                });
            } catch (\Throwable) {
                // ignore duplicate index
            }
        }
    }
};
