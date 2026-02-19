<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ScanLog;
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

        $today = now('Asia/Jakarta')->startOfDay();
        $monthStart = now('Asia/Jakarta')->startOfMonth();

        $totalTickets = DB::table('tickets')->where('event_id', $eventId)->count();

        $validToday = DB::table('scan_logs')
            ->where('event_id', $eventId)
            ->where('scan_result', 'VALID')
            ->whereNotNull('ticket_id')
            ->where('scanned_at', '>=', $today)
            ->distinct()
            ->count('ticket_id');

        $validMonth = DB::table('scan_logs')
            ->where('event_id', $eventId)
            ->where('scan_result', 'VALID')
            ->whereNotNull('ticket_id')
            ->where('scanned_at', '>=', $monthStart)
            ->distinct()
            ->count('ticket_id');

        $validAll = DB::table('scan_logs')
            ->where('event_id', $eventId)
            ->where('scan_result', 'VALID')
            ->whereNotNull('ticket_id')
            ->distinct()
            ->count('ticket_id');

        $dupAll = DB::table('scan_logs')
            ->where('event_id', $eventId)
            ->where('scan_result', 'DUPLICATE')
            ->count();

        $invalidAll = DB::table('scan_logs')
            ->where('event_id', $eventId)
            ->whereIn('scan_result', ['INVALID', 'INVALID_TYPE'])
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
            ->where('event_id', $eventId)
            ->orderByDesc('scanned_at')
            ->limit(20)
            ->get(['scanned_at','scan_result','gate_name','ticket_id']);

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
        ])->setPublic()->setMaxAge(5);
    }
}
