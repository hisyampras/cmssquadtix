<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\TicketType;
use App\Models\TicketTypePolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class TicketController extends Controller
{
    public function index(Request $request, Event $event)
    {
        $q = trim((string) $request->query('q', ''));

        $tickets = Ticket::query()
            ->leftJoin('category as ct', 'ct.id', '=', 'tickets.category_id')
            ->select('tickets.*', DB::raw("COALESCE(ct.category, 'REGULAR') as category"))
            ->where('ct.events_id', $event->id)
            ->when($q !== '', fn ($query) => $query->where('tickets.code', 'like', '%' . strtoupper($q) . '%'))
            ->addSelect([
                'latest_status_tickets_id' => DB::table('scan_logs')
                    ->select('scan_logs.status_tickets_id')
                    ->whereColumn('scan_logs.tickets_id', 'tickets.id')
                    ->orderByDesc('scan_logs.scanned_at')
                    ->orderByDesc('scan_logs.id')
                    ->limit(1),
            ])
            ->orderByDesc('tickets.id')
            ->paginate(15)
            ->withQueryString();

        $ticketTypeStats = DB::table('category as ct')
            ->leftJoin('tickets', 'tickets.category_id', '=', 'ct.id')
            ->selectRaw('UPPER(ct.category) as category, COUNT(tickets.id) as total_ticket')
            ->where('ct.events_id', $event->id)
            ->groupByRaw('UPPER(ct.category)')
            ->orderBy('category')
            ->get();

        $ticketTypePolicies = TicketTypePolicy::query()
            ->join('category', 'category.id', '=', 'category_policies.category_id')
            ->where('category.events_id', $event->id)
            ->select('category_policies.*', 'category.category as ticket_type_name')
            ->get()
            ->mapWithKeys(fn ($row) => [strtoupper((string) $row->ticket_type_name) => $row]);

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

    public function createCategory(Request $request, Event $event)
    {
        return redirect()->route('events.tickets.index', [
            'event' => $event,
            'show_create_category' => 1,
        ]);
    }

    public function storeCategory(Request $request, Event $event)
    {
        $data = $request->validate([
            'category' => ['required', 'string', 'max:80'],
        ]);

        $category = strtoupper(trim($data['category']));
        if ($category === '') {
            return back()->withErrors(['category' => 'Name Category wajib diisi.'])->withInput();
        }

        $exists = TicketType::query()
            ->where('events_id', $event->id)
            ->whereRaw('UPPER(category) = ?', [$category])
            ->exists();

        if ($exists) {
            return back()->withErrors(['category' => 'Category sudah ada di event ini.'])->withInput();
        }

        TicketType::query()->create([
            'events_id' => $event->id,
            'category' => $category,
        ]);

        return redirect()->route('events.tickets.index', $event)->with('success', "Category {$category} berhasil dibuat.");
    }

    public function downloadTemplate(Event $event)
    {
        $csv = "code,category,name,other_data\n";
        $csv .= "TCKT-000001,REGULAR,John Doe,Table A1\n";
        $csv .= "TCKT-000002,VIP,Jane Doe,Seat B-12\n";
        $csv .= "TCKT-000003,VVIP,Alex Smith,Access all area\n";

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
        $categoryCache = [];
        $columnMap = [
            'code' => 0,
            'category' => 1,
            'name' => 2,
            'other_data' => 3,
        ];

        while (($data = fgetcsv($handle)) !== false) {
            $lineNumber++;

            if (count($data) === 1 && trim((string) ($data[0] ?? '')) === '') {
                continue; // skip blank line
            }

            if (!$headerChecked) {
                $firstCol = strtolower(trim((string) ($data[0] ?? '')));
                if ($firstCol === 'code') {
                    $headers = array_map(static fn ($value) => strtolower(trim((string) $value)), $data);
                    $columnMap['code'] = array_search('code', $headers, true) !== false ? array_search('code', $headers, true) : 0;
                    $categoryIndex = array_search('category', $headers, true);
                    $nameIndex = array_search('name', $headers, true);
                    $otherDataIndex = array_search('other_data', $headers, true);

                    $columnMap['category'] = $categoryIndex !== false ? $categoryIndex : 1;
                    $columnMap['name'] = $nameIndex !== false ? $nameIndex : 2;
                    $columnMap['other_data'] = $otherDataIndex !== false ? $otherDataIndex : 3;
                    $headerChecked = true;
                    continue; // header row
                }
                $headerChecked = true;
            }

            $code = trim((string) ($data[$columnMap['code']] ?? ''));
            $category = trim((string) ($data[$columnMap['category']] ?? 'REGULAR'));
            $name = trim((string) ($data[$columnMap['name']] ?? ''));
            $otherData = trim((string) ($data[$columnMap['other_data']] ?? ''));

            if ($code === '') {
                $errors[] = "Baris {$lineNumber}: kolom code wajib diisi.";
                continue;
            }

            if (mb_strlen($code) > 64) {
                $errors[] = "Baris {$lineNumber}: code maksimal 64 karakter.";
                continue;
            }

            if ($category === '') {
                $category = 'REGULAR';
            }

            if (mb_strlen($category) > 80) {
                $errors[] = "Baris {$lineNumber}: category maksimal 80 karakter.";
                continue;
            }

            if ($name !== '' && mb_strlen($name) > 255) {
                $errors[] = "Baris {$lineNumber}: name maksimal 255 karakter.";
                continue;
            }

            $rows[] = [
                'code' => $code,
                'name' => $name !== '' ? $name : null,
                'category_id' => $this->resolveCategoryId($event->id, $category, $categoryCache),
                'other_data' => $otherData !== '' ? $otherData : null,
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
            // insertOrIgnore handles duplicate code safely.
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
                Rule::unique('tickets', 'code'),
            ],
            'name' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:80'],
            'other_data' => ['nullable', 'string'],
        ]);

        $data['code'] = strtoupper(trim($data['code']));
        $data['name'] = trim((string) ($data['name'] ?? '')) ?: null;
        $data['category_id'] = $this->resolveCategoryId($event->id, (string) ($data['category'] ?? 'REGULAR'));
        unset($data['category']);
        $data['other_data'] = trim((string) ($data['other_data'] ?? '')) ?: null;

        Ticket::create($data);

        return redirect()->route('events.tickets.index', $event)->with('success', 'Ticket berhasil dibuat.');
    }

    public function edit(Event $event, Ticket $ticket)
    {
        abort_unless($this->ticketBelongsToEvent($ticket, $event->id), 404);
        $ticket->load('categoryRef');
        return view('tickets.edit', compact('event', 'ticket'));
    }

    public function update(Request $request, Event $event, Ticket $ticket)
    {
        abort_unless($this->ticketBelongsToEvent($ticket, $event->id), 404);

        $data = $request->validate([
            'code' => [
                'required',
                'string',
                'max:64',
                Rule::unique('tickets', 'code')
                    ->ignore($ticket->id),
            ],
            'name' => ['nullable', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:80'],
            'other_data' => ['nullable', 'string'],
        ]);

        $data['code'] = strtoupper(trim($data['code']));
        $data['name'] = trim((string) ($data['name'] ?? '')) ?: null;
        $data['category_id'] = $this->resolveCategoryId($event->id, (string) ($data['category'] ?? 'REGULAR'));
        unset($data['category']);
        $data['other_data'] = trim((string) ($data['other_data'] ?? '')) ?: null;
        $ticket->update($data);

        return redirect()->route('events.tickets.index', $event)->with('success', 'Ticket berhasil diupdate.');
    }

    public function destroy(Event $event, Ticket $ticket)
    {
        abort_unless($this->ticketBelongsToEvent($ticket, $event->id), 404);
        $ticket->delete();

        return redirect()->route('events.tickets.index', $event)->with('success', 'Ticket berhasil dihapus.');
    }

    public function upsertTypePolicy(Request $request, Event $event)
    {
        $data = $request->validate([
            'category' => ['required', 'string', 'max:80'],
            'max_entry_count' => ['nullable', 'integer', 'min:1', 'max:1000'],
        ]);

        $ticketType = strtoupper(trim($data['category']));
        $maxEntryCount = $request->filled('max_entry_count') ? (int) $data['max_entry_count'] : null;

        $ticketTypeId = TicketType::query()
            ->where('events_id', $event->id)
            ->whereRaw('UPPER(category) = ?', [$ticketType])
            ->value('id');

        if (!$ticketTypeId) {
            $ticketTypeId = TicketType::query()->insertGetId([
                'events_id' => $event->id,
                'category' => $ticketType,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        TicketTypePolicy::query()->updateOrCreate(
            [
                'category_id' => $ticketTypeId,
            ],
            array_filter([
                'event_id' => Schema::hasColumn('category_policies', 'event_id') ? $event->id : null,
                'events_id' => Schema::hasColumn('category_policies', 'events_id') ? $event->id : null,
                'ticket_type' => Schema::hasColumn('category_policies', 'ticket_type') ? $ticketType : null,
                'category' => Schema::hasColumn('category_policies', 'category') ? $ticketType : null,
                'max_entry_count' => $maxEntryCount,
            ], static fn ($value) => $value !== null)
        );

        $message = $maxEntryCount === null
            ? "Rule {$ticketType} diset ke unlimited entry."
            : "Rule {$ticketType} diset max {$maxEntryCount} kali masuk.";

        return redirect()->route('events.tickets.index', $event)->with('success', $message);
    }

    private function resolveCategoryId(int $eventId, string $category, array &$cache = []): int
    {
        $category = strtoupper(trim($category)) ?: 'REGULAR';
        $cacheKey = $eventId . '|' . $category;

        if (isset($cache[$cacheKey])) {
            return $cache[$cacheKey];
        }

        $categoryId = TicketType::query()
            ->where('events_id', $eventId)
            ->whereRaw('UPPER(category) = ?', [$category])
            ->value('id');

        if (!$categoryId) {
            $categoryId = TicketType::query()->insertGetId([
                'events_id' => $eventId,
                'category' => $category,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $cache[$cacheKey] = (int) $categoryId;
        return (int) $categoryId;
    }

    private function ticketBelongsToEvent(Ticket $ticket, int $eventId): bool
    {
        return Ticket::query()
            ->join('category as ct', 'ct.id', '=', 'tickets.category_id')
            ->where('tickets.id', $ticket->id)
            ->where('ct.events_id', $eventId)
            ->exists();
    }
}
