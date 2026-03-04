<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('group_gates', function (Blueprint $table) {
            $table->dropColumn('name_gates');
        });
    }

    public function down(): void
    {
        Schema::table('group_gates', function (Blueprint $table) {
            $table->string('name_gates')->nullable()->after('category_id');
        });

        // Backfill name_gates from related gates table.
        DB::statement("
            UPDATE group_gates
            SET name_gates = (
                SELECT gates.gates_name
                FROM gates
                WHERE gates.id = group_gates.gates_id
                LIMIT 1
            )
            WHERE gates_id IS NOT NULL
        ");
    }
};
