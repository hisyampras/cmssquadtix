<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ScanLog;
use App\Models\Ticket;
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
        $eventId = (int)($request->query('event_id') ?? ($events->first()->id ?? 0));
        $gate = $request->query('gate', 'GATE A');

        $eventIds = $events->pluck('id')->all();
        $eventTicketTypesByEventId = Ticket::query()
            ->select('event_id', 'ticket_type')
            ->whereIn('event_id', $eventIds)
            ->whereNotNull('ticket_type')
            ->orderBy('ticket_type')
            ->get()
            ->groupBy('event_id')
            ->map(fn ($rows) => $rows->pluck('ticket_type')->filter()->unique()->values())
            ->toArray();

        return [$events, $eventId, $gate, $eventTicketTypesByEventId];
    }

    public function scan(Request $request)
    {
        $data = $request->validate([
            'event_id'  => ['required','integer'],
            'code'      => ['required','string','max:64'],
            'gate_name' => ['nullable','string','max:80'],
            'mode' => ['nullable', 'string', 'in:in,out'],
            'allowed_types' => ['nullable', 'array'],
            'allowed_types.*' => ['string', 'max:80'],
        ]);

        $eventId = (int)$data['event_id'];
        $code = strtoupper(trim($data['code']));
        $gate = $data['gate_name'] ?? null;
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
            ->where('event_id', $eventId)
            ->where('code', $code)
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
                ->where('event_id', $eventId)
                ->where('code', $code)
                ->lockForUpdate()
                ->first();

            if (!$lockedTicket) {
                return Response::json([
                    'ok' => false,
                    'result' => 'INVALID',
                    'message' => 'Kode tidak ditemukan / beda event.',
                ]);
            }

            $ticketType = strtoupper((string) ($lockedTicket->ticket_type ?? 'REGULAR'));
            if ($hasTypeFilterInput && (empty($allowedTypes) || !in_array($ticketType, $allowedTypes, true))) {
                return Response::json([
                    'ok' => false,
                    'result' => 'INVALID_TYPE',
                    'message' => empty($allowedTypes)
                        ? 'Belum ada ticket type yang dipilih pada filter gate.'
                        : 'Tipe tiket tidak sesuai gate/filter yang dipilih.',
                    'ticket' => [
                        'code' => $lockedTicket->code,
                        'ticket_type' => $lockedTicket->ticket_type,
                    ],
                ]);
            }

            $lastStatusId = ScanLog::query()
                ->where('event_id', $eventId)
                ->where('ticket_id', $lockedTicket->id)
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
                            'ticket_type' => $lockedTicket->ticket_type,
                        ],
                    ]);
                }

                $nextStatusId = $lastStatusId === self::STATUS_ID_RECHECKED_IN
                    ? self::STATUS_ID_RECHECKED_OUT
                    : self::STATUS_ID_CHECKED_OUT;

                ScanLog::create([
                    'event_id' => $eventId,
                    'ticket_id' => $lockedTicket->id,
                    'status_tickets_id' => $nextStatusId,
                    'gate_name' => $gate,
                    'scan_result' => 'VALID',
                    'scanned_at' => $now,
                ]);

                return Response::json([
                    'ok' => true,
                    'result' => 'VALID',
                    'message' => 'OK, silakan keluar.',
                    'ticket' => [
                        'code' => $lockedTicket->code,
                        'ticket_type' => $lockedTicket->ticket_type,
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
                        'ticket_type' => $lockedTicket->ticket_type,
                    ],
                ]);
            }

            $nextStatusId = $wasCheckedOut
                ? self::STATUS_ID_RECHECKED_IN
                : self::STATUS_ID_CHECKED_IN;

            $policy = TicketTypePolicy::query()
                ->where('event_id', $eventId)
                ->where('ticket_type', $ticketType)
                ->first(['max_entry_count']);

            // Default policy: 1x entry per ticket if no explicit rule is configured.
            $maxEntryCount = $policy ? $policy->max_entry_count : 1;

            $validScanCount = ScanLog::query()
                ->where('event_id', $eventId)
                ->where('ticket_id', $lockedTicket->id)
                ->whereIn('status_tickets_id', [self::STATUS_ID_CHECKED_IN, self::STATUS_ID_RECHECKED_IN])
                ->count();

            if ($maxEntryCount !== null && $validScanCount >= (int) $maxEntryCount) {
                return Response::json([
                    'ok' => true,
                    'result' => 'DUPLICATE',
                    'message' => "Batas masuk tiket sudah tercapai ({$maxEntryCount}x).",
                    'ticket' => [
                        'code' => $lockedTicket->code,
                        'ticket_type' => $lockedTicket->ticket_type,
                        'entry_count' => $validScanCount,
                        'max_entry_count' => (int) $maxEntryCount,
                    ],
                ]);
            }

            ScanLog::create([
                'event_id' => $eventId,
                'ticket_id' => $lockedTicket->id,
                'status_tickets_id' => $nextStatusId,
                'gate_name' => $gate,
                'scan_result' => 'VALID',
                'scanned_at' => $now,
            ]);

            return Response::json([
                'ok' => true,
                'result' => 'VALID',
                'message' => 'OK, silakan masuk.',
                'ticket' => [
                    'code' => $lockedTicket->code,
                    'ticket_type' => $lockedTicket->ticket_type,
                    'entry_count' => $validScanCount + 1,
                    'max_entry_count' => $maxEntryCount === null ? null : (int) $maxEntryCount,
                ],
            ]);
        }, 3);
    }

}
