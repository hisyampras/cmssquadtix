<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_policies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('events_id')->constrained()->cascadeOnDelete();
            $table->string('category', 80);
            $table->unsignedInteger('max_entry_count')->nullable();
            $table->timestamps();

            $table->unique(['events_id', 'category']);
            $table->index(['events_id', 'max_entry_count']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_policies');
    }
};
