<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('scan_logs', function (Blueprint $table) {
            $table->foreignId('status_tickets_id')
                ->nullable()
                ->constrained('status_tickets')
                ->nullOnDelete()
                ->after('ticket_id');
        });
    }

    public function down(): void
    {
        Schema::table('scan_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('status_tickets_id');
        });
    }
};
