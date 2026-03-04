<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('group_gates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tickets_id')->constrained('tickets')->cascadeOnDelete();
            $table->string('name_gates');
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('group_gates');
    }
};
