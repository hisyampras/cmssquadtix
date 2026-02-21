<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('status_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('status_name', 255);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_tickets');
    }
};
