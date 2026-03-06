<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::orderByDesc('id')->paginate(10);
        return view('events.index', compact('events'));
    }

    public function create()
    {
        $users = $this->userOptions();

        return view('events.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:200'],
            'location' => ['nullable','string','max:200'],
            'start_at' => ['nullable','date'],
            'end_at'   => ['nullable','date','after_or_equal:start_at'],
            'is_active'=> ['nullable','boolean'],
            'users_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? true);

        Event::create($data);

        return redirect()->route('events.index')->with('success', 'Event berhasil dibuat.');
    }

    public function edit(Event $event)
    {
        $users = $this->userOptions();

        return view('events.edit', compact('event', 'users'));
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:200'],
            'location' => ['nullable','string','max:200'],
            'start_at' => ['nullable','date'],
            'end_at'   => ['nullable','date','after_or_equal:start_at'],
            'is_active'=> ['nullable','boolean'],
            'users_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        $data['is_active'] = (bool)($data['is_active'] ?? false);

        $event->update($data);

        return redirect()->route('events.index')->with('success', 'Event berhasil diupdate.');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')->with('success', 'Event berhasil dihapus.');
    }

    private function userOptions(): Collection
    {
        return User::query()
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
    }
}
