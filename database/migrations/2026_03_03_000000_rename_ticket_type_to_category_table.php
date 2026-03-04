<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('ticket_type') && !Schema::hasTable('category')) {
            Schema::rename('ticket_type', 'category');
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('category') && !Schema::hasTable('ticket_type')) {
            Schema::rename('category', 'ticket_type');
        }
    }
};
