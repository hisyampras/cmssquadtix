<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('tickets') || Schema::hasColumn('tickets', 'no_transaction')) {
            return;
        }

        Schema::table('tickets', function (Blueprint $table) {
            $table->string('no_transaction', 100)->nullable()->after('code');
            $table->index('no_transaction');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('tickets') || !Schema::hasColumn('tickets', 'no_transaction')) {
            return;
        }

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex('tickets_no_transaction_index');
            $table->dropColumn('no_transaction');
        });
    }
};
