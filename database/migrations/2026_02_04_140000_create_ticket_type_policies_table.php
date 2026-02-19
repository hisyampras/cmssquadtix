<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ticket_type_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->string('ticket_type', 80);
            $table->unsignedInteger('max_entry_count')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'ticket_type']);
            $table->index(['event_id', 'max_entry_count']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_type_policies');
    }
};
