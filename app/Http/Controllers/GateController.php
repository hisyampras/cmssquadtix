<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Gate;
use Illuminate\Http\Request;

class GateController extends Controller
{
    public function index(Request $request, Event $event)
    {
        $gates = Gate::query()
            ->orderBy('gates_name')
            ->orderBy('id')
            ->get();

        $editGate = null;
        $editId = (int) $request->query('edit', 0);
        if ($editId > 0) {
            $editGate = Gate::query()->find($editId);
        }

        return view('gate.create_gate', compact('event', 'gates', 'editGate'));
    }

    public function store(Request $request, Event $event)
    {
        $data = $request->validate([
            'gates_name' => ['required', 'string', 'max:120'],
        ]);

        $gateName = trim((string) $data['gates_name']);
        if ($gateName === '') {
            return back()->withErrors(['gates_name' => 'Gate name wajib diisi.'])->withInput();
        }

        $exists = Gate::query()
            ->whereRaw('UPPER(gates_name) = ?', [strtoupper($gateName)])
            ->exists();

        if ($exists) {
            return back()->withErrors(['gates_name' => 'Gate sudah ada.'])->withInput();
        }

        Gate::query()->create([
            'gates_name' => $gateName,
        ]);

        return redirect()
            ->route('events.gates.index', $event)
            ->with('success', "Gate {$gateName} berhasil dibuat.");
    }

    public function update(Request $request, Event $event, Gate $gate)
    {
        $data = $request->validate([
            'gates_name' => ['required', 'string', 'max:120'],
        ]);

        $gateName = trim((string) $data['gates_name']);
        if ($gateName === '') {
            return back()->withErrors(['gates_name' => 'Gate name wajib diisi.'])->withInput();
        }

        $exists = Gate::query()
            ->where('id', '!=', $gate->id)
            ->whereRaw('UPPER(gates_name) = ?', [strtoupper($gateName)])
            ->exists();

        if ($exists) {
            return back()->withErrors(['gates_name' => 'Gate sudah ada.'])->withInput();
        }

        $gate->update([
            'gates_name' => $gateName,
        ]);

        return redirect()
            ->route('events.gates.index', $event)
            ->with('success', "Gate {$gateName} berhasil diupdate.");
    }

    public function destroy(Event $event, Gate $gate)
    {
        $gateName = $gate->gates_name;
        $gate->delete();

        return redirect()
            ->route('events.gates.index', $event)
            ->with('success', "Gate {$gateName} berhasil dihapus.");
    }
}

