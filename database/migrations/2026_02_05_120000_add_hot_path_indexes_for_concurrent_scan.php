<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scan_logs', function (Blueprint $table) {
            $table->index(['events_id', 'ticket_id', 'scan_result'], 'scan_logs_event_ticket_result_idx');
            $table->index(['events_id', 'gate_name', 'scanned_at'], 'scan_logs_event_gate_scanned_idx');
        });
    }

    public function down(): void
    {
        Schema::table('scan_logs', function (Blueprint $table) {
            $table->dropIndex('scan_logs_event_ticket_result_idx');
            $table->dropIndex('scan_logs_event_gate_scanned_idx');
        });
    }
};
