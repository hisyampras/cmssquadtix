<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('events') && !Schema::hasColumn('events', 'users_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->foreignId('users_id')->nullable()->after('event_code')->constrained('users')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('events') && Schema::hasColumn('events', 'users_id')) {
            Schema::table('events', function (Blueprint $table) {
                $table->dropConstrainedForeignId('users_id');
            });
        }
    }
};
