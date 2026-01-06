<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\ScanLog;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ScanGateController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::query()->where('is_active', true)->orderByDesc('id')->get();
        $eventId = (int)($request->query('event_id') ?? ($events->first()->id ?? 0));
        $gate = $request->query('gate', 'GATE A');

        return view('scan.index', compact('events','eventId','gate'));
    }

    public function scan(Request $request)
    {
        $data = $request->validate([
            'event_id'  => ['required','integer'],
            'code'      => ['required','string','max:64'],
            'gate_name' => ['nullable','string','max:80'],
        ]);

        $eventId = (int)$data['event_id'];
        $code = strtoupper(trim($data['code']));
        $gate = $data['gate_name'] ?? null;

        $ticket = Ticket::query()
            ->where('event_id', $eventId)
            ->where('code', $code)
            ->first();

        $now = Carbon::now('Asia/Jakarta');

        if (!$ticket) {
            ScanLog::create([
                'event_id' => $eventId,
                'ticket_id' => null,
                'gate_name' => $gate,
                'scan_result' => 'INVALID',
                'scanned_at' => $now,
            ]);

            return Response::json([
                'ok' => false,
                'result' => 'INVALID',
                'message' => 'Kode tidak ditemukan / beda event.',
            ]);
        }

        $alreadyValid = ScanLog::query()
            ->where('event_id', $eventId)
            ->where('ticket_id', $ticket->id)
            ->where('scan_result', 'VALID')
            ->exists();

        if ($alreadyValid) {
            ScanLog::create([
                'event_id' => $eventId,
                'ticket_id' => $ticket->id,
                'gate_name' => $gate,
                'scan_result' => 'DUPLICATE',
                'scanned_at' => $now,
            ]);

            return Response::json([
                'ok' => true,
                'result' => 'DUPLICATE',
                'message' => 'Ticket sudah check-in.',
                'ticket' => [
                    'code' => $ticket->code,
                    'ticket_type' => $ticket->ticket_type,
                ],
            ]);
        }

        ScanLog::create([
            'event_id' => $eventId,
            'ticket_id' => $ticket->id,
            'gate_name' => $gate,
            'scan_result' => 'VALID',
            'scanned_at' => $now,
        ]);

        return Response::json([
            'ok' => true,
            'result' => 'VALID',
            'message' => 'OK, silakan masuk.',
            'ticket' => [
                'code' => $ticket->code,
                'ticket_type' => $ticket->ticket_type,
            ],
        ]);
    }

}
