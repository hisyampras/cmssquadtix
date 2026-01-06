<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('code', 64);              // isi QR/Barcode
            $table->string('ticket_type', 80)->default('REGULAR');
            $table->timestamps();

            $table->unique(['event_id', 'code']);   // code unik per event
            $table->index(['event_id', 'ticket_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
