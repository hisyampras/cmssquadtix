<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (Schema::hasTable('tickets') && !Schema::hasColumn('tickets', 'category_id')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->unsignedBigInteger('category_id')->nullable()->after('code');
            });
        }

        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'category') && Schema::hasColumn('tickets', 'events_id')) {
            if ($driver === 'mysql') {
                DB::statement("
                    INSERT IGNORE INTO category (events_id, category, created_at, updated_at)
                    SELECT DISTINCT events_id, category, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                    FROM tickets
                    WHERE category IS NOT NULL
                ");
            } else {
                DB::statement("
                    INSERT OR IGNORE INTO category (events_id, category, created_at, updated_at)
                    SELECT DISTINCT events_id, category, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                    FROM tickets
                    WHERE category IS NOT NULL
                ");
            }

            DB::statement("
                UPDATE tickets
                SET category_id = (
                    SELECT c.id
                    FROM category c
                    WHERE c.events_id = tickets.events_id
                      AND UPPER(c.category) = UPPER(tickets.category)
                    LIMIT 1
                )
                WHERE category IS NOT NULL
            ");

            // SQLite cannot drop a column while any index still references it.
            // Clean up both current and legacy index names that may still exist.
            foreach ([
                'tickets_events_id_category_index',
                'tickets_event_id_category_index',
                'tickets_event_id_ticket_type_index',
                'tickets_events_id_ticket_type_index',
                'tickets_ticket_type_index',
            ] as $indexName) {
                try {
                    Schema::table('tickets', function (Blueprint $table) use ($indexName) {
                        $table->dropIndex($indexName);
                    });
                } catch (\Throwable) {
                    // ignore missing index
                }
            }
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }

        try {
            Schema::table('tickets', function (Blueprint $table) {
                $table->index('category_id', 'tickets_category_id_index');
            });
        } catch (\Throwable) {
            // ignore duplicate index
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('tickets') && !Schema::hasColumn('tickets', 'category')) {
            Schema::table('tickets', function (Blueprint $table) {
                $table->string('category', 80)->default('REGULAR')->after('code');
            });
        }

        if (Schema::hasTable('tickets') && Schema::hasColumn('tickets', 'category_id')) {
            DB::statement("
                UPDATE tickets
                SET category = (
                    SELECT c.category
                    FROM category c
                    WHERE c.id = tickets.category_id
                    LIMIT 1
                )
                WHERE category_id IS NOT NULL
            ");

            try {
                Schema::table('tickets', function (Blueprint $table) {
                    $table->dropIndex('tickets_category_id_index');
                });
            } catch (\Throwable) {
                // ignore missing index
            }
            Schema::table('tickets', function (Blueprint $table) {
                $table->dropColumn('category_id');
            });
            if (Schema::hasColumn('tickets', 'events_id')) {
                try {
                    Schema::table('tickets', function (Blueprint $table) {
                        $table->index(['events_id', 'category'], 'tickets_events_id_category_index');
                    });
                } catch (\Throwable) {
                    // ignore duplicate index
                }
            }
        }
    }
};
