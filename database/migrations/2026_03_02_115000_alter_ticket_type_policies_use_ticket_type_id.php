<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (!Schema::hasTable('category_policies') && Schema::hasTable('ticket_type_policies')) {
            Schema::rename('ticket_type_policies', 'category_policies');
        }

        if (!Schema::hasTable('category_policies')) {
            Schema::create('category_policies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->nullable()->constrained('category')->cascadeOnDelete();
                $table->unsignedInteger('max_entry_count')->nullable();
                $table->timestamps();
                $table->unique('category_id');
                $table->index(['category_id', 'max_entry_count'], 'category_policies_category_id_max_entry_count_index');
            });
            return;
        }

        if (!Schema::hasColumn('category_policies', 'category_id')) {
            Schema::table('category_policies', function (Blueprint $table) {
                $table->foreignId('category_id')
                    ->nullable()
                    ->constrained('category')
                    ->cascadeOnDelete()
                    ->after('id');
            });
        }

        // Backfill category_id from existing category values.
        if (Schema::hasColumn('category_policies', 'category')) {
            if ($driver === 'mysql') {
                DB::statement("
                    INSERT IGNORE INTO category (category, created_at, updated_at)
                    SELECT DISTINCT category, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                    FROM category_policies
                    WHERE category IS NOT NULL
                ");
            } else {
                DB::statement("
                    INSERT OR IGNORE INTO category (category, created_at, updated_at)
                    SELECT DISTINCT category, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                    FROM category_policies
                    WHERE category IS NOT NULL
                ");
            }

            DB::statement("
                UPDATE category_policies
                SET category_id = (
                    SELECT tt.id
                    FROM category tt
                    WHERE UPPER(tt.category) = UPPER(category_policies.category)
                    LIMIT 1
                )
                WHERE category IS NOT NULL
            ");
        }

        foreach ([
            'category_policies_event_id_category_unique',
            'category_policies_event_id_max_entry_count_index',
        ] as $indexName) {
            try {
                Schema::table('category_policies', function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
            } catch (\Throwable) {
                // ignore missing index
            }
        }

        if (Schema::hasColumn('category_policies', 'events_id')) {
            try {
                Schema::table('category_policies', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('events_id');
                });
            } catch (\Throwable) {
                Schema::table('category_policies', function (Blueprint $table) {
                    $table->dropColumn('events_id');
                });
            }
        }

        if (Schema::hasColumn('category_policies', 'category')) {
            Schema::table('category_policies', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }

        try {
            Schema::table('category_policies', function (Blueprint $table) {
                $table->unique('category_id', 'category_policies_category_id_unique');
            });
        } catch (\Throwable) {
            // ignore duplicate unique index
        }

        try {
            Schema::table('category_policies', function (Blueprint $table) {
                $table->index(['category_id', 'max_entry_count'], 'category_policies_category_id_max_entry_count_index');
            });
        } catch (\Throwable) {
            // ignore duplicate index
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('category_policies')) {
            return;
        }

        Schema::table('category_policies', function (Blueprint $table) {
            if (!Schema::hasColumn('category_policies', 'events_id')) {
                $table->foreignId('events_id')
                    ->nullable()
                    ->constrained()
                    ->cascadeOnDelete()
                    ->after('id');
            }

            if (!Schema::hasColumn('category_policies', 'category')) {
                $table->string('category', 80)->nullable()->after('events_id');
            }
        });

        DB::statement("
            UPDATE category_policies
            SET category = (
                SELECT tt.category
                FROM category tt
                WHERE tt.id = category_policies.category_id
                LIMIT 1
            )
            WHERE category_id IS NOT NULL
        ");

        foreach ([
            'category_policies_category_id_unique',
            'category_policies_category_id_max_entry_count_index',
        ] as $indexName) {
            try {
                Schema::table('category_policies', function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
            } catch (\Throwable) {
                // ignore missing index
            }
        }

        if (Schema::hasColumn('category_policies', 'category_id')) {
            try {
                Schema::table('category_policies', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('category_id');
                });
            } catch (\Throwable) {
                Schema::table('category_policies', function (Blueprint $table) {
                    $table->dropColumn('category_id');
                });
            }
        }

        try {
            Schema::table('category_policies', function (Blueprint $table) {
                $table->unique(['events_id', 'category'], 'category_policies_event_id_category_unique');
            });
        } catch (\Throwable) {
            // ignore duplicate unique index
        }

        try {
            Schema::table('category_policies', function (Blueprint $table) {
                $table->index(['events_id', 'max_entry_count'], 'category_policies_event_id_max_entry_count_index');
            });
        } catch (\Throwable) {
            // ignore duplicate index
        }
    }
};
