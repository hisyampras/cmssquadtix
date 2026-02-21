<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ScanLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::query()->orderByDesc('id')->limit(50)->get();
        $eventId = (int)($request->query('event_id') ?? ($events->first()->id ?? 0));

        return view('dashboard.index', [
            'events' => $events,
            'eventId' => $eventId,
        ]);
    }

    public function data(Request $request)
    {
        $eventId = (int)$request->query('event_id');
        if (!$eventId) {
            return Response::json(['ok'=>false,'message'=>'event_id required'], 422);
        }

        $totalTickets = DB::table('tickets')->where('event_id', $eventId)->count();

        $validToday = DB::table('scan_logs')
            ->where('event_id', $eventId)
            ->where('status_tickets_id', 2)
            ->whereNotNull('ticket_id')
            ->count();

        $validMonth = DB::table('scan_logs')
            ->where('event_id', $eventId)
            ->where('status_tickets_id', 4)
            ->whereNotNull('ticket_id')
            ->count();

        $validAll = DB::table('tickets')
            ->where('event_id', $eventId)
            ->whereNotExists(function ($q) use ($eventId) {
                $q->selectRaw('1')
                    ->from('scan_logs')
                    ->whereColumn('scan_logs.ticket_id', 'tickets.id')
                    ->where('scan_logs.event_id', $eventId);
            })
            ->count();

        $dupAll = DB::table('scan_logs')
            ->where('event_id', $eventId)
            ->where('status_tickets_id', 3)
            ->count();

        $invalidAll = DB::table('scan_logs')
            ->where('event_id', $eventId)
            ->where('status_tickets_id', 5)
            ->count();

        $byGate = DB::table('scan_logs')
            ->selectRaw("
                COALESCE(gate_name, 'NO_GATE') as gate_name,
                COUNT(*) as total_attempt,
                COUNT(DISTINCT CASE WHEN scan_result = 'VALID' THEN ticket_id END) as valid_unique
            ")
            ->where('event_id', $eventId)
            ->groupBy('gate_name')
            ->orderByDesc('valid_unique')
            ->orderByDesc('total_attempt')
            ->limit(10)
            ->get();

        $recent = ScanLog::query()
            ->leftJoin('status_tickets', 'status_tickets.id', '=', 'scan_logs.status_tickets_id')
            ->where('scan_logs.event_id', $eventId)
            ->orderByDesc('scanned_at')
            ->limit(20)
            ->get([
                'scan_logs.scanned_at',
                'scan_logs.scan_result',
                'scan_logs.gate_name',
                'scan_logs.ticket_id',
                'scan_logs.status_tickets_id',
                'status_tickets.status_name',
            ]);

        $chart = $this->buildStatusTrend($eventId, $totalTickets);

        return Response::json([
            'ok' => true,
            'kpi' => [
                'totalTickets' => $totalTickets,
                'validToday'   => $validToday,
                'validMonth'   => $validMonth,
                'validAll'     => $validAll,
                'dupAll'       => $dupAll,
                'invalidAll'   => $invalidAll,
            ],
            'byGate' => $byGate,
            'recent' => $recent,
            'chart' => $chart,
        ])->setPublic()->setMaxAge(5);
    }

    private function buildStatusTrend(int $eventId, int $totalTickets): array
    {
        $tz = 'Asia/Jakarta';
        $points = 24; // last 2 hours, 5-minute buckets
        $end = now($tz)->second(0);
        $end = $end->copy()->minute((int) (floor($end->minute / 5) * 5));
        $start = $end->copy()->subMinutes(($points - 1) * 5);

        $labels = [];
        $bucketKeys = [];
        for ($i = 0; $i < $points; $i++) {
            $bucket = $start->copy()->addMinutes($i * 5);
            $bucketKey = $bucket->format('Y-m-d H:i:00');
            $bucketKeys[] = $bucketKey;
            $labels[] = $bucket->format('H:i');
        }

        $series = [
            'pending' => array_fill(0, $points, 0),
            'checkin' => array_fill(0, $points, 0),
            'recheckin' => array_fill(0, $points, 0),
            'checkout' => array_fill(0, $points, 0),
            'recheckout' => array_fill(0, $points, 0),
        ];

        $bucketIndex = array_flip($bucketKeys);

        $statusRows = DB::table('scan_logs')
            ->selectRaw('FROM_UNIXTIME(FLOOR(UNIX_TIMESTAMP(scanned_at) / 300) * 300) as bucket_time, status_tickets_id, COUNT(*) as total')
            ->where('event_id', $eventId)
            ->whereBetween('scanned_at', [$start, $end->copy()->addMinutes(5)])
            ->whereIn('status_tickets_id', [2, 3, 4, 5])
            ->groupBy('bucket_time', 'status_tickets_id')
            ->get();

        foreach ($statusRows as $row) {
            $bucketTime = (string) ($row->bucket_time ?? '');
            if (!isset($bucketIndex[$bucketTime])) {
                continue;
            }

            $idx = (int) $bucketIndex[$bucketTime];
            $statusId = (int) $row->status_tickets_id;
            $total = (int) $row->total;

            if ($statusId === 2) {
                $series['checkin'][$idx] = $total;
            } elseif ($statusId === 4) {
                $series['recheckin'][$idx] = $total;
            } elseif ($statusId === 3) {
                $series['checkout'][$idx] = $total;
            } elseif ($statusId === 5) {
                $series['recheckout'][$idx] = $total;
            }
        }

        $firstScanRows = DB::table('scan_logs')
            ->selectRaw('ticket_id, MIN(scanned_at) as first_scanned_at')
            ->where('event_id', $eventId)
            ->whereNotNull('ticket_id')
            ->groupBy('ticket_id')
            ->get();

        $firstByBucket = [];
        $scannedBeforeStart = 0;

        foreach ($firstScanRows as $row) {
            $first = $row->first_scanned_at;
            if (!$first) {
                continue;
            }

            $firstAt = Carbon::parse((string) $first, $tz);
            if ($firstAt->lt($start)) {
                $scannedBeforeStart++;
                continue;
            }

            if ($firstAt->gt($end->copy()->addMinutes(5))) {
                continue;
            }

            $bucket = $firstAt->copy()
                ->second(0)
                ->minute((int) (floor($firstAt->minute / 5) * 5))
                ->format('Y-m-d H:i:00');

            $firstByBucket[$bucket] = (int) (($firstByBucket[$bucket] ?? 0) + 1);
        }

        $runningScanned = $scannedBeforeStart;
        foreach ($bucketKeys as $i => $bucketKey) {
            $runningScanned += (int) ($firstByBucket[$bucketKey] ?? 0);
            $series['pending'][$i] = max(0, $totalTickets - $runningScanned);
        }

        return [
            'labels' => $labels,
            'series' => $series,
        ];
    }
}
