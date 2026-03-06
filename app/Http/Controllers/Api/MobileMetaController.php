<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class MobileMetaController extends Controller
{
    public function events(): JsonResponse
    {
        $events = Event::query()
            ->where('is_active', true)
            ->orderByDesc('id')
            ->get(['id', 'event_code', 'name', 'location']);

        $eventIds = $events->pluck('id')->all();
        $typesByEvent = Ticket::query()
            ->leftJoin('category as ct', 'ct.id', '=', 'tickets.category_id')
            ->select('ct.events_id', DB::raw('ct.category as category'))
            ->whereIn('ct.events_id', $eventIds)
            ->whereNotNull('ct.category')
            ->orderBy('ct.category')
            ->get()
            ->groupBy('events_id')
            ->map(fn ($rows) => $rows->pluck('category')->filter()->unique()->values())
            ->toArray();

        $payload = $events->map(function ($event) use ($typesByEvent) {
            return [
                'id' => $event->id,
                'event_code' => $event->event_code,
                'name' => $event->name,
                'location' => $event->location,
                'ticket_types' => $typesByEvent[$event->id] ?? [],
            ];
        })->values();

        return response()->json([
            'ok' => true,
            'events' => $payload,
        ]);
    }

    public function groupGates(Request $request): JsonResponse
    {
        $data = $request->validate([
            'events_id' => ['required', 'integer', 'exists:events,id'],
        ]);

        $eventId = (int) $data['events_id'];

        $query = DB::table('group_gates as gg')
            ->join('gates as g', 'g.id', '=', 'gg.gates_id')
            ->join('category as c', 'c.id', '=', 'gg.category_id')
            ->where('c.events_id', $eventId);

        if (Schema::hasColumn('group_gates', 'status')) {
            $query->whereRaw('UPPER(COALESCE(gg.status, ?)) = ?', ['ACTIVE', 'ACTIVE']);
        }

        $gates = $query
            ->select('g.id', 'g.gates_name')
            ->distinct()
            ->orderBy('g.gates_name')
            ->get()
            ->map(fn ($row) => [
                'id' => (int) $row->id,
                'name' => (string) $row->gates_name,
            ])
            ->values();

        return response()->json([
            'ok' => true,
            'group_gates' => $gates,
        ]);
    }
}
