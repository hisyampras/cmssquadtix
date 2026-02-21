<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('status_tickets')->upsert([
            ['id' => 1, 'status_name' => 'Pending'],
            ['id' => 2, 'status_name' => 'Checkin'],
            ['id' => 3, 'status_name' => 'Checkout'],
            ['id' => 4, 'status_name' => 'Recheckin'],
            ['id' => 5, 'status_name' => 'Recheckout'],
        ], ['id'], ['status_name']);
    }

    public function down(): void
    {
        DB::table('status_tickets')
            ->whereIn('id', [1, 2, 3, 4, 5])
            ->delete();
    }
};
