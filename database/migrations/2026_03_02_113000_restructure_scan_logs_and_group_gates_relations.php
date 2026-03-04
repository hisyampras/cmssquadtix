<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('group_gates') || !Schema::hasTable('scan_logs')) {
            return;
        }

        if (!Schema::hasColumn('group_gates', 'events_id')) {
            Schema::table('group_gates', function (Blueprint $table) {
                $table->foreignId('events_id')
                    ->nullable()
                    ->constrained('events')
                    ->cascadeOnDelete()
                    ->after('id');
            });
        }

        if (!Schema::hasColumn('scan_logs', 'tickets_id')) {
            Schema::table('scan_logs', function (Blueprint $table) {
                $table->foreignId('tickets_id')
                    ->nullable()
                    ->constrained('tickets')
                    ->nullOnDelete()
                    ->after('id');
            });
        }

        // Backfill data before dropping old relation columns.
        if (Schema::hasColumn('group_gates', 'tickets_id')) {
            if (Schema::hasColumn('tickets', 'events_id')) {
                DB::statement("
                    UPDATE group_gates
                    SET events_id = (
                        SELECT tickets.events_id
                        FROM tickets
                        WHERE tickets.id = group_gates.tickets_id
                    )
                    WHERE tickets_id IS NOT NULL
                ");
            } elseif (Schema::hasColumn('tickets', 'category_id') && Schema::hasTable('category') && Schema::hasColumn('category', 'events_id')) {
                if (DB::getDriverName() === 'mysql') {
                    DB::statement("
                        UPDATE group_gates gg
                        JOIN tickets t ON t.id = gg.tickets_id
                        LEFT JOIN category c ON c.id = t.category_id
                        SET gg.events_id = c.events_id
                        WHERE gg.tickets_id IS NOT NULL
                    ");
                } else {
                    DB::statement("
                        UPDATE group_gates
                        SET events_id = (
                            SELECT c.events_id
                            FROM tickets t
                            LEFT JOIN category c ON c.id = t.category_id
                            WHERE t.id = group_gates.tickets_id
                            LIMIT 1
                        )
                        WHERE tickets_id IS NOT NULL
                    ");
                }
            }
        }

        if (Schema::hasColumn('scan_logs', 'group_gates_id')) {
            DB::statement("
                UPDATE scan_logs
                SET tickets_id = (
                    SELECT group_gates.tickets_id
                    FROM group_gates
                    WHERE group_gates.id = scan_logs.group_gates_id
                )
                WHERE group_gates_id IS NOT NULL
            ");
        }

        if (Schema::hasColumn('scan_logs', 'group_gates_id')) {
            try {
                Schema::table('scan_logs', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('group_gates_id');
                });
            } catch (\Throwable) {
                Schema::table('scan_logs', function (Blueprint $table) {
                    $table->dropColumn('group_gates_id');
                });
            }
        }

        if (Schema::hasColumn('group_gates', 'tickets_id')) {
            try {
                Schema::table('group_gates', function (Blueprint $table) {
                    $table->dropConstrainedForeignId('tickets_id');
                });
            } catch (\Throwable) {
                Schema::table('group_gates', function (Blueprint $table) {
                    $table->dropColumn('tickets_id');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::table('group_gates', function (Blueprint $table) {
            $table->foreignId('tickets_id')
                ->nullable()
                ->constrained('tickets')
                ->nullOnDelete()
                ->after('id');
        });

        Schema::table('scan_logs', function (Blueprint $table) {
            $table->foreignId('group_gates_id')
                ->nullable()
                ->constrained('group_gates')
                ->nullOnDelete()
                ->after('status_tickets_id');
        });

        Schema::table('scan_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tickets_id');
        });

        Schema::table('group_gates', function (Blueprint $table) {
            $table->dropConstrainedforeignId('events_id');
        });
    }
};
