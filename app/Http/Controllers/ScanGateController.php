<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Gate;
use App\Models\ScanLog;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\TicketTypePolicy;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ScanGateController extends Controller
{
    private const STATUS_ID_CHECKED_IN = 2;
    private const STATUS_ID_CHECKED_OUT = 3;
    private const STATUS_ID_RECHECKED_IN = 4;
    private const STATUS_ID_RECHECKED_OUT = 5;

    public function index(Request $request)
    {
        [$events, $eventId, $gate, $eventTicketTypesByEventId] = $this->scanPageData($request);
        $mode = 'in';

        return view('scan.scan-gate-in', compact('events', 'eventId', 'gate', 'eventTicketTypesByEventId', 'mode'));
    }

    public function mobile(Request $request)
    {
        [$events, $eventId, $gate, $eventTicketTypesByEventId] = $this->scanPageData($request);
        $mode = 'in';

        return view('scan.mobile-in', compact('events', 'eventId', 'gate', 'eventTicketTypesByEventId', 'mode'));
    }

    public function out(Request $request)
    {
        [$events, $eventId, $gate, $eventTicketTypesByEventId] = $this->scanPageData($request);
        $mode = 'out';

        return view('scan.scan-gate-out', compact('events', 'eventId', 'gate', 'eventTicketTypesByEventId', 'mode'));
    }

    public function mobileOut(Request $request)
    {
        [$events, $eventId, $gate, $eventTicketTypesByEventId] = $this->scanPageData($request);
        $mode = 'out';

        return view('scan.mobile-out', compact('events', 'eventId', 'gate', 'eventTicketTypesByEventId', 'mode'));
    }

    private function scanPageData(Request $request): array
    {
        $events = Event::query()->where('is_active', true)->orderByDesc('id')->get();
        $eventId = (int)($request->query('events_id') ?? ($events->first()->id ?? 0));
        $gate = $request->query('gate', 'GATE A');

        $eventIds = $events->pluck('id')->all();
        $eventTicketTypesByEventId = Ticket::query()
            ->leftJoin('category as ct', 'ct.id', '=', 'tickets.category_id')
            ->select('ct.events_id', DB::raw('ct.category as category'))
            ->whereIn('ct.events_id', $eventIds)
            ->whereNotNull('ct.category')
            ->orderBy('ct.category')
            ->get()
            ->groupBy('events_id')
            ->map(fn ($rows) => $rows->pluck('category')->filter()->unique()->values())
            ->toArray();

        return [$events, $eventId, $gate, $eventTicketTypesByEventId];
    }

    public function scan(Request $request)
    {
        $data = $request->validate([
            'events_id'  => ['required','integer'],
            'code'      => ['required','string','max:64'],
            'gate_name' => ['nullable','string','max:80'],
            'mode' => ['nullable', 'string', 'in:in,out'],
            'allowed_types' => ['nullable', 'array'],
            'allowed_types.*' => ['string', 'max:80'],
        ]);

        $eventId = (int)$data['events_id'];
        $code = strtoupper(trim($data['code']));
        $gate = trim((string) ($data['gate_name'] ?? '')) ?: 'NO_GATE';
        $mode = strtolower((string) ($data['mode'] ?? 'in'));
        $hasTypeFilterInput = $request->exists('allowed_types') && is_array($request->input('allowed_types'));
        $allowedTypes = collect($data['allowed_types'] ?? [])
            ->filter(fn ($v) => is_string($v) && trim($v) !== '')
            ->map(fn ($v) => strtoupper(trim($v)))
            ->unique()
            ->values()
            ->all();

        $now = Carbon::now('Asia/Jakarta');
        $ticket = Ticket::query()
            ->join('category as ct', 'ct.id', '=', 'tickets.category_id')
            ->where('ct.events_id', $eventId)
            ->where('tickets.code', $code)
            ->first();

        if (!$ticket) {
            return Response::json([
                'ok' => false,
                'result' => 'INVALID',
                'message' => 'Kode tidak ditemukan / beda event.',
            ]);
        }

        return DB::transaction(function () use ($eventId, $code, $gate, $hasTypeFilterInput, $allowedTypes, $now, $mode) {
            // Lock ticket row to serialize concurrent scans for the same ticket.
            $lockedTicket = Ticket::query()
                ->leftJoin('category as ct', 'ct.id', '=', 'tickets.category_id')
                ->select('tickets.*', DB::raw("COALESCE(ct.category, 'REGULAR') as category"))
                ->where('ct.events_id', $eventId)
                ->where('tickets.code', $code)
                ->lockForUpdate()
                ->first();

            if (!$lockedTicket) {
                return Response::json([
                    'ok' => false,
                    'result' => 'INVALID',
                    'message' => 'Kode tidak ditemukan / beda event.',
                ]);
            }

            $ticketType = strtoupper((string) ($lockedTicket->category ?? 'REGULAR'));
            if ($hasTypeFilterInput && (empty($allowedTypes) || !in_array($ticketType, $allowedTypes, true))) {
                return Response::json([
                    'ok' => false,
                    'result' => 'INVALID_TYPE',
                    'message' => empty($allowedTypes)
                        ? 'Belum ada category yang dipilih pada filter gate.'
                        : 'Tipe tiket tidak sesuai gate/filter yang dipilih.',
                    'ticket' => [
                        'code' => $lockedTicket->code,
                        'category' => $lockedTicket->category,
                    ],
                ]);
            }

            $lastStatusId = ScanLog::query()
                ->where('tickets_id', $lockedTicket->id)
                ->orderByDesc('scanned_at')
                ->orderByDesc('id')
                ->value('status_tickets_id');

            $lastStatusId = $lastStatusId ? (int) $lastStatusId : null;
            $isCheckedIn = in_array($lastStatusId, [self::STATUS_ID_CHECKED_IN, self::STATUS_ID_RECHECKED_IN], true);
            $wasCheckedOut = in_array($lastStatusId, [self::STATUS_ID_CHECKED_OUT, self::STATUS_ID_RECHECKED_OUT], true);

            if ($mode === 'out') {
                if (!$isCheckedIn) {
                    return Response::json([
                        'ok' => true,
                        'result' => 'WARNING',
                        'message' => 'Harus Checkin dulu.',
                        'ticket' => [
                            'code' => $lockedTicket->code,
                            'category' => $lockedTicket->category,
                        ],
                    ]);
                }

                $nextStatusId = $lastStatusId === self::STATUS_ID_RECHECKED_IN
                    ? self::STATUS_ID_RECHECKED_OUT
                    : self::STATUS_ID_CHECKED_OUT;

                $this->resolveGroupGateId($eventId, $gate, $ticketType);

                ScanLog::create([
                    'tickets_id' => $lockedTicket->id,
                    'status_tickets_id' => $nextStatusId,
                    'scan_result' => 'VALID',
                    'scanned_at' => $now,
                ]);

                return Response::json([
                    'ok' => true,
                    'result' => 'VALID',
                    'message' => 'OK, silakan keluar.',
                    'ticket' => [
                        'code' => $lockedTicket->code,
                        'category' => $lockedTicket->category,
                    ],
                ]);
            }

            if ($isCheckedIn) {
                return Response::json([
                    'ok' => true,
                    'result' => 'WARNING',
                    'message' => 'Harus Checkout dulu.',
                    'ticket' => [
                        'code' => $lockedTicket->code,
                        'category' => $lockedTicket->category,
                    ],
                ]);
            }

            $nextStatusId = $wasCheckedOut
                ? self::STATUS_ID_RECHECKED_IN
                : self::STATUS_ID_CHECKED_IN;

            $ticketTypeId = TicketType::query()
                ->where('events_id', $eventId)
                ->whereRaw('UPPER(category) = ?', [$ticketType])
                ->value('id');

            $policy = $ticketTypeId
                ? TicketTypePolicy::query()
                    ->where('category_id', $ticketTypeId)
                    ->first(['max_entry_count'])
                : null;

            // Default policy: 1x entry per ticket if no explicit rule is configured.
            $maxEntryCount = $policy ? $policy->max_entry_count : 1;

            $validScanCount = ScanLog::query()
                ->where('tickets_id', $lockedTicket->id)
                ->whereIn('status_tickets_id', [self::STATUS_ID_CHECKED_IN, self::STATUS_ID_RECHECKED_IN])
                ->count();

            if ($maxEntryCount !== null && $validScanCount >= (int) $maxEntryCount) {
                return Response::json([
                    'ok' => true,
                    'result' => 'DUPLICATE',
                    'message' => "Batas masuk tiket sudah tercapai ({$maxEntryCount}x).",
                    'ticket' => [
                        'code' => $lockedTicket->code,
                        'category' => $lockedTicket->category,
                        'entry_count' => $validScanCount,
                        'max_entry_count' => (int) $maxEntryCount,
                    ],
                ]);
            }

            $this->resolveGroupGateId($eventId, $gate, $ticketType);

            ScanLog::create([
                'tickets_id' => $lockedTicket->id,
                'status_tickets_id' => $nextStatusId,
                'scan_result' => 'VALID',
                'scanned_at' => $now,
            ]);

            return Response::json([
                'ok' => true,
                'result' => 'VALID',
                'message' => 'OK, silakan masuk.',
                'ticket' => [
                    'code' => $lockedTicket->code,
                    'category' => $lockedTicket->category,
                    'entry_count' => $validScanCount + 1,
                    'max_entry_count' => $maxEntryCount === null ? null : (int) $maxEntryCount,
                ],
            ]);
        }, 3);
    }

    private function resolveGroupGateId(int $eventId, string $gateName, string $ticketType): int
    {
        $gateId = Gate::query()->where('gates_name', $gateName)->value('id');
        if (!$gateId) {
            $gateId = Gate::query()->insertGetId([
                'gates_name' => $gateName,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $ticketTypeId = TicketType::query()
            ->where('events_id', $eventId)
            ->whereRaw('UPPER(category) = ?', [strtoupper(trim($ticketType))])
            ->value('id');

        if (!$ticketTypeId) {
            $ticketTypeId = TicketType::query()->insertGetId([
                'events_id' => $eventId,
                'category' => strtoupper(trim($ticketType)),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $existingId = DB::table('group_gates')
            ->where('gates_id', $gateId)
            ->where('category_id', $ticketTypeId)
            ->value('id');

        if ($existingId) {
            return (int) $existingId;
        }

        return (int) DB::table('group_gates')->insertGetId([
            'gates_id' => $gateId,
            'category_id' => $ticketTypeId,
            'status' => 'ACTIVE',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

}
