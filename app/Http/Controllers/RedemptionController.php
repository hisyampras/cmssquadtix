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
use Illuminate\Support\Facades\Schema;

class RedemptionController extends Controller
{
    private const STATUS_ID_CHECKED_IN = 2;
    private const STATUS_ID_CHECKED_OUT = 3;
    private const STATUS_ID_RECHECKED_IN = 4;
    private const STATUS_ID_RECHECKED_OUT = 5;

    public function index(Request $request)
    {
        $events = Event::query()->where('is_active', true)->orderByDesc('id')->get();
        $eventId = (int) ($request->query('events_id') ?? ($events->first()->id ?? 0));
        $gate = trim((string) $request->query('gate', 'REDEMPTION'));

        return view('redemption.redemption', compact('events', 'eventId', 'gate'));
    }

    public function search(Request $request)
    {
        $data = $request->validate([
            'events_id' => ['required', 'integer'],
            'code' => ['required', 'string', 'max:64'],
        ]);

        $eventId = (int) $data['events_id'];
        $code = strtoupper(trim($data['code']));

        $baseQuery = Ticket::query()
            ->join('category as ct', 'ct.id', '=', 'tickets.category_id')
            ->where('ct.events_id', $eventId);

        $seed = (clone $baseQuery)
            ->select('tickets.id', 'tickets.code', 'tickets.no_transaction')
            ->where('tickets.code', $code)
            ->first();

        if (!$seed) {
            return Response::json([
                'ok' => false,
                'rows' => [],
                'message' => 'Kode tidak ditemukan untuk event ini.',
            ]);
        }

        return Response::json([
            'ok' => true,
            'rows' => $this->fetchRowsBySeed($eventId, (int) $seed->id),
        ]);
    }

    public function action(Request $request)
    {
        $data = $request->validate([
            'events_id' => ['required', 'integer'],
            'ticket_id' => ['required', 'integer'],
            'gate_name' => ['nullable', 'string', 'max:80'],
        ]);

        $eventId = (int) $data['events_id'];
        $ticketId = (int) $data['ticket_id'];
        $gate = trim((string) ($data['gate_name'] ?? '')) ?: 'REDEMPTION';
        $now = Carbon::now('Asia/Jakarta');

        return DB::transaction(function () use ($eventId, $ticketId, $gate, $now) {
            $lockedTicket = Ticket::query()
                ->join('category as ct', 'ct.id', '=', 'tickets.category_id')
                ->select('tickets.*', DB::raw("COALESCE(ct.category, 'REGULAR') as category"), 'ct.events_id')
                ->where('ct.events_id', $eventId)
                ->where('tickets.id', $ticketId)
                ->lockForUpdate()
                ->first();

            if (!$lockedTicket) {
                return Response::json([
                    'ok' => false,
                    'message' => 'Ticket tidak ditemukan untuk event ini.',
                ], 404);
            }

            $ticketType = strtoupper((string) ($lockedTicket->category ?? 'REGULAR'));
            $lastStatusId = ScanLog::query()
                ->where('tickets_id', $lockedTicket->id)
                ->orderByDesc('scanned_at')
                ->orderByDesc('id')
                ->value('status_tickets_id');

            $lastStatusId = $lastStatusId ? (int) $lastStatusId : null;
            $isCheckedIn = in_array($lastStatusId, [self::STATUS_ID_CHECKED_IN, self::STATUS_ID_RECHECKED_IN], true);
            $wasCheckedOut = in_array($lastStatusId, [self::STATUS_ID_CHECKED_OUT, self::STATUS_ID_RECHECKED_OUT], true);

            if ($isCheckedIn) {
                return Response::json([
                    'ok' => false,
                    'message' => 'Harus checkout dulu sebelum redeem lagi.',
                    'rows' => $this->fetchRowsBySeed($eventId, (int) $lockedTicket->id),
                ], 422);
            }

            $nextStatusId = $wasCheckedOut
                ? self::STATUS_ID_RECHECKED_IN
                : self::STATUS_ID_CHECKED_IN;

            $ticketTypeId = TicketType::query()
                ->where('events_id', $eventId)
                ->whereRaw('UPPER(category) = ?', [$ticketType])
                ->value('id');

            $policy = $ticketTypeId
                ? TicketTypePolicy::query()->where('category_id', $ticketTypeId)->first(['max_entry_count'])
                : null;

            $maxEntryCount = $policy ? $policy->max_entry_count : 1;
            $validScanCount = ScanLog::query()
                ->where('tickets_id', $lockedTicket->id)
                ->whereIn('status_tickets_id', [self::STATUS_ID_CHECKED_IN, self::STATUS_ID_RECHECKED_IN])
                ->count();

            if ($maxEntryCount !== null && $validScanCount >= (int) $maxEntryCount) {
                return Response::json([
                    'ok' => false,
                    'message' => "Batas redeem/checkin tiket sudah tercapai ({$maxEntryCount}x). Harus checkout dulu.",
                    'rows' => $this->fetchRowsBySeed($eventId, (int) $lockedTicket->id),
                ], 422);
            }

            $this->resolveGroupGateId($eventId, $gate, $ticketType);
            ScanLog::create($this->buildScanLogPayload($eventId, [
                'tickets_id' => $lockedTicket->id,
                'status_tickets_id' => $nextStatusId,
                'scan_result' => 'VALID',
                'scanned_at' => $now,
            ]));

            return Response::json([
                'ok' => true,
                'message' => $nextStatusId === self::STATUS_ID_RECHECKED_IN ? 'Redeem ulang berhasil (Recheckin).' : 'Redeem berhasil (Checkin).',
                'rows' => $this->fetchRowsBySeed($eventId, (int) $lockedTicket->id),
            ]);
        }, 3);
    }

    private function fetchRowsBySeed(int $eventId, int $seedTicketId)
    {
        $baseQuery = Ticket::query()
            ->join('category as ct', 'ct.id', '=', 'tickets.category_id')
            ->where('ct.events_id', $eventId);

        $seed = (clone $baseQuery)
            ->select('tickets.id', 'tickets.code', 'tickets.no_transaction')
            ->where('tickets.id', $seedTicketId)
            ->first();

        if (!$seed) {
            return collect();
        }

        $rows = (clone $baseQuery)
            ->leftJoin('category_policies as cp', 'cp.category_id', '=', 'tickets.category_id')
            ->select(
                'tickets.id',
                'tickets.code',
                'tickets.no_transaction',
                'tickets.name',
                'tickets.other_data',
                'cp.id as category_policy_id',
                'cp.max_entry_count as policy_max_entry_count'
            )
            ->addSelect([
                'latest_status_tickets_id' => DB::table('scan_logs')
                    ->select('scan_logs.status_tickets_id')
                    ->whereColumn('scan_logs.tickets_id', 'tickets.id')
                    ->orderByDesc('scan_logs.scanned_at')
                    ->orderByDesc('scan_logs.id')
                    ->limit(1),
                'valid_entry_count' => DB::table('scan_logs')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('scan_logs.tickets_id', 'tickets.id')
                    ->whereIn('scan_logs.status_tickets_id', [self::STATUS_ID_CHECKED_IN, self::STATUS_ID_RECHECKED_IN]),
            ])
            ->where(function ($query) use ($seed) {
                $query->where('tickets.code', $seed->code);

                if (!empty($seed->no_transaction)) {
                    $query->orWhere('tickets.no_transaction', $seed->no_transaction);
                }
            })
            ->orderBy('tickets.code')
            ->orderBy('tickets.id')
            ->get();

        return $rows->map(function ($row) {
            $statusId = (int) ($row->latest_status_tickets_id ?? 1);
            $validEntryCount = (int) ($row->valid_entry_count ?? 0);
            $hasPolicy = !empty($row->category_policy_id);
            $maxEntryCount = $hasPolicy
                ? ($row->policy_max_entry_count !== null ? (int) $row->policy_max_entry_count : null)
                : 1;
            $isRedeemed = ($maxEntryCount === 1 && $validEntryCount >= 1);

            $row->status_label = match ($statusId) {
                self::STATUS_ID_CHECKED_IN => 'Checkin',
                self::STATUS_ID_CHECKED_OUT => 'Checkout',
                self::STATUS_ID_RECHECKED_IN => 'Recheckin',
                self::STATUS_ID_RECHECKED_OUT => 'Recheckout',
                default => 'Pending',
            };
            if ($isRedeemed) {
                $row->status_label = 'Redeemed';
            }
            $row->can_checkout = in_array($statusId, [self::STATUS_ID_CHECKED_IN, self::STATUS_ID_RECHECKED_IN], true);
            $row->is_redeemed = $isRedeemed;
            return $row;
        });
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

    private function buildScanLogPayload(int $eventId, array $payload): array
    {
        if (Schema::hasColumn('scan_logs', 'event_id')) {
            $payload['event_id'] = $eventId;
        }
        if (Schema::hasColumn('scan_logs', 'events_id')) {
            $payload['events_id'] = $eventId;
        }

        return $payload;
    }
}
