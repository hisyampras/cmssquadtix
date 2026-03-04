<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('category') && !Schema::hasColumn('category', 'events_id')) {
            Schema::table('category', function (Blueprint $table) {
                $table->unsignedBigInteger('events_id')->nullable()->after('id');
                $table->index('events_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('category') && Schema::hasColumn('category', 'events_id')) {
            Schema::table('category', function (Blueprint $table) {
                $table->dropIndex(['events_id']);
                $table->dropColumn('events_id');
            });
        }
    }
};
