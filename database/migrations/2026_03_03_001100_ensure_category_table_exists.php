<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('category') && Schema::hasTable('ticket_type')) {
            Schema::rename('ticket_type', 'category');
        }

        if (!Schema::hasTable('category')) {
            Schema::create('category', function (Blueprint $table) {
                $table->id();
                $table->foreignId('events_id')->nullable()->constrained('events')->nullOnDelete();
                $table->string('category');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Keep table to avoid accidental data loss on rollback.
    }
};

