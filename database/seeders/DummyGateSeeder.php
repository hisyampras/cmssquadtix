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
        $categoryIds = [];
        foreach ($types as $type) {
            $categoryIds[$type] = DB::table('category')->insertGetId([
                'events_id' => $eventId,
                'category' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $ticketIds = [];
        for ($i=0; $i<500; $i++) {
            $ticketTypeName = $types[$i % count($types)];
            $ticketIds[] = DB::table('tickets')->insertGetId([
                'code' => sprintf('DUMMY-%06d', $i + 1),
                'category_id' => $categoryIds[$ticketTypeName],
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
            $statusId = $result === 'VALID' ? 2 : ($result === 'DUPLICATE' ? 3 : 5);
            $ticketTypeName = $types[$idx % count($types)];

            // scanned_at random hari ini
            $scannedAt = $now->copy()
                ->setTime(rand(8, 21), rand(0, 59), rand(0, 59));

            $gateId = DB::table('gates')->where('gates_name', $gate)->value('id');
            if (!$gateId) {
                $gateId = DB::table('gates')->insertGetId([
                    'gates_name' => $gate,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $ticketTypeId = DB::table('category')
                ->where('events_id', $eventId)
                ->where('category', $ticketTypeName)
                ->value('id');
            if (!$ticketTypeId) {
                $ticketTypeId = DB::table('category')->insertGetId([
                    'events_id' => $eventId,
                    'category' => $ticketTypeName,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('group_gates')->updateOrInsert(
                [
                    'gates_id' => $gateId,
                    'category_id' => $ticketTypeId,
                ],
                [
                    'status' => 'ACTIVE',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            DB::table('scan_logs')->insert([
                'tickets_id' => $tid,
                'status_tickets_id' => $statusId,
                'scan_result' => $result,
                'scanned_at' => $scannedAt,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // tambah 1 record duplicate untuk sebagian
            if ($result === 'DUPLICATE') {
                DB::table('scan_logs')->insert([
                    'tickets_id' => $tid,
                    'status_tickets_id' => 3,
                    'scan_result' => 'DUPLICATE',
                    'scanned_at' => $scannedAt->copy()->addMinutes(rand(1, 10)),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
