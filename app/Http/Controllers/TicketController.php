<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketTypePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index(Request $request, Event $event)
    {
        $q = trim((string) $request->query('q', ''));

        $tickets = Ticket::where('event_id', $event->id)
            ->when($q !== '', fn ($query) => $query->where('code', 'like', '%' . strtoupper($q) . '%'))
            ->addSelect([
                'latest_status_tickets_id' => DB::table('scan_logs')
                    ->select('status_tickets_id')
                    ->whereColumn('scan_logs.ticket_id', 'tickets.id')
                    ->where('scan_logs.event_id', $event->id)
                    ->orderByDesc('scan_logs.scanned_at')
                    ->orderByDesc('scan_logs.id')
                    ->limit(1),
            ])
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $ticketTypeStats = Ticket::query()
            ->selectRaw("UPPER(COALESCE(ticket_type, 'REGULAR')) as ticket_type, COUNT(*) as total_ticket")
            ->where('event_id', $event->id)
            ->groupByRaw("UPPER(COALESCE(ticket_type, 'REGULAR'))")
            ->orderBy('ticket_type')
            ->get();

        $ticketTypePolicies = TicketTypePolicy::query()
            ->where('event_id', $event->id)
            ->get()
            ->mapWithKeys(fn ($row) => [strtoupper((string) $row->ticket_type) => $row]);

        return view('tickets.index', compact('event', 'tickets', 'ticketTypeStats', 'ticketTypePolicies', 'q'));
    }

    public function create(Event $event)
    {
        return view('tickets.create', compact('event'));
    }

    public function bulkForm(Event $event)
    {
        return view('tickets.bulk', compact('event'));
    }

    public function downloadTemplate(Event $event)
    {
        $csv = "code,ticket_type\n";
        $csv .= "TCKT-000001,REGULAR\n";
        $csv .= "TCKT-000002,VIP\n";
        $csv .= "TCKT-000003,VVIP\n";

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="ticket-template-event-' . $event->id . '.csv"',
        ]);
    }

    public function bulkStore(Request $request, Event $event)
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'], // 5MB
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        if ($handle === false) {
            return back()->withErrors(['csv_file' => 'File CSV tidak dapat dibaca.'])->withInput();
        }

        $rows = [];
        $errors = [];
        $lineNumber = 0;
        $headerChecked = false;

        while (($data = fgetcsv($handle)) !== false) {
            $lineNumber++;

            if (count($data) === 1 && trim((string) ($data[0] ?? '')) === '') {
                continue; // skip blank line
            }

            if (!$headerChecked) {
                $firstCol = strtolower(trim((string) ($data[0] ?? '')));
                if ($firstCol === 'code') {
                    $headerChecked = true;
                    continue; // header row
                }
                $headerChecked = true;
            }

            $code = trim((string) ($data[0] ?? ''));
            $ticketType = trim((string) ($data[1] ?? 'REGULAR'));

            if ($code === '') {
                $errors[] = "Baris {$lineNumber}: kolom code wajib diisi.";
                continue;
            }

            if (mb_strlen($code) > 64) {
                $errors[] = "Baris {$lineNumber}: code maksimal 64 karakter.";
                continue;
            }

            if ($ticketType === '') {
                $ticketType = 'REGULAR';
            }

            if (mb_strlen($ticketType) > 80) {
                $errors[] = "Baris {$lineNumber}: ticket_type maksimal 80 karakter.";
                continue;
            }

            $rows[] = [
                'event_id' => $event->id,
                'code' => $code,
                'ticket_type' => $ticketType,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        fclose($handle);

        if (empty($rows) && !empty($errors)) {
            return back()
                ->withErrors(['csv_file' => 'Tidak ada data valid untuk diimport.'])
                ->with('import_errors', array_slice($errors, 0, 10));
        }

        $inserted = 0;
        if (!empty($rows)) {
            // insertOrIgnore handles duplicate (event_id, code) safely.
            foreach (array_chunk($rows, 500) as $chunk) {
                $inserted += DB::table('tickets')->insertOrIgnore($chunk);
            }
        }

        $validCount = count($rows);
        $duplicateCount = max(0, $validCount - $inserted);
        $invalidCount = count($errors);

        return redirect()
            ->route('events.tickets.index', $event)
            ->with('success', "Import selesai. Inserted: {$inserted}, Duplicate: {$duplicateCount}, Invalid: {$invalidCount}.")
            ->with('import_errors', array_slice($errors, 0, 10));
    }

    public function store(Request $request, Event $event)
    {
        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:64',
                Rule::unique('tickets', 'code')->where(fn ($q) => $q->where('event_id', $event->id)),
            ],
            'ticket_type' => ['nullable', 'string', 'max:80'],
        ]);

        $data['code'] = strtoupper(trim($data['code']));
        $data['event_id'] = $event->id;
        $data['ticket_type'] = strtoupper(trim($data['ticket_type'] ?? 'REGULAR')) ?: 'REGULAR';

        Ticket::create($data);

        return redirect()->route('events.tickets.index', $event)->with('success', 'Ticket berhasil dibuat.');
    }

    public function edit(Event $event, Ticket $ticket)
    {
        abort_unless($ticket->event_id === $event->id, 404);
        return view('tickets.edit', compact('event', 'ticket'));
    }

    public function update(Request $request, Event $event, Ticket $ticket)
    {
        abort_unless($ticket->event_id === $event->id, 404);

        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:64',
                Rule::unique('tickets', 'code')
                    ->where(fn ($q) => $q->where('event_id', $event->id))
                    ->ignore($ticket->id),
            ],
            'ticket_type' => ['nullable', 'string', 'max:80'],
        ]);

        $data['code'] = strtoupper(trim($data['code']));
        $data['ticket_type'] = strtoupper(trim($data['ticket_type'] ?? 'REGULAR')) ?: 'REGULAR';
        $ticket->update($data);

        return redirect()->route('events.tickets.index', $event)->with('success', 'Ticket berhasil diupdate.');
    }

    public function destroy(Event $event, Ticket $ticket)
    {
        abort_unless($ticket->event_id === $event->id, 404);
        $ticket->delete();

        return redirect()->route('events.tickets.index', $event)->with('success', 'Ticket berhasil dihapus.');
    }

    public function upsertTypePolicy(Request $request, Event $event)
    {
        $data = $request->validate([
            'ticket_type' => ['required', 'string', 'max:80'],
            'max_entry_count' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $ticketType = strtoupper(trim($data['ticket_type']));
        $maxEntryCount = $request->filled('max_entry_count') ? (int) $data['max_entry_count'] : null;

        TicketTypePolicy::query()->updateOrCreate(
            [
                'event_id' => $event->id,
                'ticket_type' => $ticketType,
            ],
            [
                'max_entry_count' => $maxEntryCount,
            ]
        );

        $message = $maxEntryCount === null
            ? "Rule {$ticketType} diset ke unlimited entry."
            : "Rule {$ticketType} diset max {$maxEntryCount} kali masuk.";

        return redirect()->route('events.tickets.index', $event)->with('success', $message);
    }
}
