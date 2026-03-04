<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $driver = DB::getDriverName();

        Schema::table('group_gates', function (Blueprint $table) {
            $table->foreignId('gates_id')
                ->nullable()
                ->constrained('gates')
                ->nullOnDelete()
                ->after('id');

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('category')
                ->nullOnDelete()
                ->after('gates_id');
        });

        // Backfill gates table from existing group_gates names.
        if ($driver === 'mysql') {
            DB::statement("
                INSERT IGNORE INTO gates (gates_name, created_at, updated_at)
                SELECT DISTINCT name_gates, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                FROM group_gates
                WHERE name_gates IS NOT NULL
            ");
        } else {
            DB::statement("
                INSERT OR IGNORE INTO gates (gates_name, created_at, updated_at)
                SELECT DISTINCT name_gates, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
                FROM group_gates
                WHERE name_gates IS NOT NULL
            ");
        }

        DB::statement("
            UPDATE group_gates
            SET gates_id = (
                SELECT g.id
                FROM gates g
                WHERE g.gates_name = group_gates.name_gates
                LIMIT 1
            )
            WHERE name_gates IS NOT NULL
        ");

        Schema::table('group_gates', function (Blueprint $table) {
            $table->dropConstrainedForeignId('events_id');
            $table->index(['gates_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::table('group_gates', function (Blueprint $table) {
            $table->foreignId('events_id')
                ->nullable()
                ->constrained('events')
                ->nullOnDelete()
                ->after('id');
        });

        Schema::table('group_gates', function (Blueprint $table) {
            $table->dropIndex('group_gates_gates_id_category_id_index');
            $table->dropConstrainedForeignId('category_id');
            $table->dropConstrainedForeignId('gates_id');
        });
    }
};
