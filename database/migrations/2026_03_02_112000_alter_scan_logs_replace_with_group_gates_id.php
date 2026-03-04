<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('scan_logs')) {
            return;
        }

        foreach (['events_id', 'ticket_id'] as $column) {
            if (!Schema::hasColumn('scan_logs', $column)) {
                continue;
            }

            try {
                Schema::table('scan_logs', function (Blueprint $table) use ($column) {
                    $table->dropForeign([$column]);
                });
            } catch (\Throwable) {
                try {
                    Schema::table('scan_logs', function (Blueprint $table) use ($column) {
                        $table->dropConstrainedForeignId($column);
                    });
                } catch (\Throwable) {
                    // ignore if FK already dropped or name mismatch
                }
            }
        }

        foreach ([
            'scan_logs_event_gate_scanned_idx',
            'scan_logs_event_ticket_result_idx',
            'scan_logs_event_id_scanned_at_index',
            'scan_logs_event_id_scan_result_index',
            'scan_logs_ticket_id_scan_result_index',
        ] as $indexName) {
            try {
                Schema::table('scan_logs', function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
            } catch (\Throwable) {
                // ignore missing index
            }
        }

        Schema::table('scan_logs', function (Blueprint $table) {
            if (Schema::hasColumn('scan_logs', 'events_id')) {
                $table->dropColumn('events_id');
            }
            if (Schema::hasColumn('scan_logs', 'ticket_id')) {
                $table->dropColumn('ticket_id');
            }
            if (Schema::hasColumn('scan_logs', 'gate_name')) {
                $table->dropColumn('gate_name');
            }
        });

        if (!Schema::hasColumn('scan_logs', 'group_gates_id')) {
            Schema::table('scan_logs', function (Blueprint $table) {
                $table->foreignId('group_gates_id')
                    ->nullable()
                    ->constrained('group_gates')
                    ->nullOnDelete()
                    ->after('status_tickets_id');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('scan_logs')) {
            return;
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

        if (!Schema::hasColumn('scan_logs', 'events_id')) {
            Schema::table('scan_logs', function (Blueprint $table) {
                $table->foreignId('events_id')
                    ->constrained('events')
                    ->cascadeOnDelete()
                    ->after('id');
            });
        }

        if (!Schema::hasColumn('scan_logs', 'ticket_id')) {
            Schema::table('scan_logs', function (Blueprint $table) {
                $table->foreignId('ticket_id')
                    ->nullable()
                    ->constrained('tickets')
                    ->nullOnDelete()
                    ->after('events_id');
            });
        }

        if (!Schema::hasColumn('scan_logs', 'gate_name')) {
            Schema::table('scan_logs', function (Blueprint $table) {
                $table->string('gate_name', 80)
                    ->nullable()
                    ->after('status_tickets_id');
            });
        }

        foreach ([
            ['events_id', 'scanned_at', 'scan_logs_events_id_scanned_at_index'],
            ['events_id', 'scan_result', 'scan_logs_events_id_scan_result_index'],
            ['ticket_id', 'scan_result', 'scan_logs_ticket_id_scan_result_index'],
        ] as [$colA, $colB, $name]) {
            try {
                Schema::table('scan_logs', function (Blueprint $table) use ($colA, $colB, $name) {
                    $table->index([$colA, $colB], $name);
                });
            } catch (\Throwable) {
                // ignore duplicate index
            }
        }

        try {
            Schema::table('scan_logs', function (Blueprint $table) {
                $table->index(['events_id', 'ticket_id', 'scan_result'], 'scan_logs_event_ticket_result_idx');
            });
        } catch (\Throwable) {
            // ignore duplicate index
        }

        try {
            Schema::table('scan_logs', function (Blueprint $table) {
                $table->index(['events_id', 'gate_name', 'scanned_at'], 'scan_logs_event_gate_scanned_idx');
            });
        } catch (\Throwable) {
            // ignore duplicate index
        }
    }
};
