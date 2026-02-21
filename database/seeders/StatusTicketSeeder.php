<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatusTicketSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('status_tickets')->upsert([
            ['id' => 1, 'status_name' => 'Pending'],
            ['id' => 2, 'status_name' => 'Checkin'],
            ['id' => 3, 'status_name' => 'Checkout'],
            ['id' => 4, 'status_name' => 'Recheckin'],
            ['id' => 5, 'status_name' => 'Recheckout'],
        ], ['id'], ['status_name']);
    }
}
