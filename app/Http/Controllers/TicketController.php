<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function index(Event $event)
    {
        $tickets = Ticket::where('event_id', $event->id)
            ->orderByDesc('id')
            ->paginate(15);

        return view('tickets.index', compact('event', 'tickets'));
    }

    public function create(Event $event)
    {
        return view('tickets.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $data = $request->validate([
            'code'        => ['required','string','max:80'],
            'owner_name'  => ['nullable','string','max:200'],
            'owner_email' => ['nullable','email','max:200'],
            'status'      => ['nullable','in:valid,used,void'],
        ]);

        $data['event_id'] = $event->id;
        $data['status']   = $data['status'] ?? 'valid';

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
            'code'        => ['required','string','max:80'],
            'owner_name'  => ['nullable','string','max:200'],
            'owner_email' => ['nullable','email','max:200'],
            'status'      => ['required','in:valid,used,void'],
        ]);

        $ticket->update($data);

        return redirect()->route('events.tickets.index', $event)->with('success', 'Ticket berhasil diupdate.');
    }

    public function destroy(Event $event, Ticket $ticket)
    {
        abort_unless($ticket->event_id === $event->id, 404);
        $ticket->delete();

        return redirect()->route('events.tickets.index', $event)->with('success', 'Ticket berhasil dihapus.');
    }
}
