<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('scan_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->foreignId('ticket_id')->nullable()->constrained('tickets')->nullOnDelete();

            $table->string('gate_name', 80)->nullable();
            $table->enum('scan_result', ['VALID', 'DUPLICATE', 'INVALID'])->default('INVALID');
            $table->dateTime('scanned_at');
            $table->timestamps();

            $table->index(['event_id', 'scanned_at']);
            $table->index(['event_id', 'scan_result']);
            $table->index(['ticket_id', 'scan_result']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_logs');
    }
};
