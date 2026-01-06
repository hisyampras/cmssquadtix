<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DummyGateSeeder extends Seeder
{
    public function run(): void
    {
        $tz = 'Asia/Jakarta';
        $now = Carbon::now($tz);

        // 1 event
        $eventId = DB::table('events')->insertGetId([
            'name' => 'Squadtix Demo Event 2026',
            'start_at' => $now->copy()->subDays(1),
            'end_at' => $now->copy()->addDays(1),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // tickets
        $types = ['REGULAR', 'VIP', 'VVIP'];
        $ticketIds = [];
        for ($i=0; $i<500; $i++) {
            $ticketIds[] = DB::table('tickets')->insertGetId([
                'event_id' => $eventId,
                'ticket_type' => $types[$i % count($types)],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // scans
        $gates = ['GATE A', 'GATE B', 'GATE C'];
        foreach ($ticketIds as $idx => $tid) {
            // sebagian belum scan (simulate remaining)
            if ($idx % 5 === 0) continue;

            $gate = $gates[$idx % count($gates)];

            // sebagian duplicate/invalid
            $result = 'VALID';
            if ($idx % 17 === 0) $result = 'DUPLICATE';
            if ($idx % 29 === 0) $result = 'INVALID';

            // scanned_at random hari ini
            $scannedAt = $now->copy()
                ->setTime(rand(8, 21), rand(0, 59), rand(0, 59));

            DB::table('scan_logs')->insert([
                'event_id' => $eventId,
                'ticket_id' => $tid,
                'gate_name' => $gate,
                'scan_result' => $result,
                'scanned_at' => $scannedAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // tambah 1 record duplicate untuk sebagian
            if ($result === 'DUPLICATE') {
                DB::table('scan_logs')->insert([
                    'event_id' => $eventId,
                    'ticket_id' => $tid,
                    'gate_name' => $gate,
                    'scan_result' => 'DUPLICATE',
                    'scanned_at' => $scannedAt->copy()->addMinutes(rand(1, 10)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
