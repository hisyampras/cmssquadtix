<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Gate;
use App\Models\GroupGate;
use App\Models\TicketType;
use Illuminate\Http\Request;

class GroupGateController extends Controller
{
    public function index(Request $request, Event $event)
    {
        $gates = Gate::query()
            ->orderBy('gates_name')
            ->orderBy('id')
            ->get();

        $selectedGateId = (int) $request->query('gate_id', 0);
        if ($selectedGateId <= 0) {
            $selectedGateId = (int) ($gates->first()->id ?? 0);
        }

        $categories = TicketType::query()
            ->where('events_id', $event->id)
            ->orderBy('category')
            ->paginate(15)
            ->withQueryString();

        $checkedCategoryIds = collect();
        if ($selectedGateId > 0) {
            $checkedCategoryIds = GroupGate::query()
                ->where('gates_id', $selectedGateId)
                ->whereIn('category_id', $categories->getCollection()->pluck('id'))
                ->pluck('category_id')
                ->map(fn ($id) => (int) $id);
        }

        return view('gate.group_gate', compact(
            'event',
            'gates',
            'selectedGateId',
            'categories',
            'checkedCategoryIds',
        ));
    }

    public function toggle(Request $request, Event $event)
    {
        $data = $request->validate([
            'gate_id' => ['required', 'integer', 'exists:gates,id'],
            'category_id' => ['required', 'integer', 'exists:category,id'],
            'checked' => ['required', 'boolean'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $gateId = (int) $data['gate_id'];
        $categoryId = (int) $data['category_id'];
        $checked = (bool) $data['checked'];
        $page = (int) ($data['page'] ?? 1);

        $categoryBelongsToEvent = TicketType::query()
            ->where('id', $categoryId)
            ->where('events_id', $event->id)
            ->exists();

        if (!$categoryBelongsToEvent) {
            return redirect()
                ->route('events.group-gates.index', ['event' => $event, 'gate_id' => $gateId, 'page' => $page])
                ->with('success', 'Category tidak valid untuk event ini.');
        }

        if ($checked) {
            GroupGate::query()->firstOrCreate(
                [
                    'gates_id' => $gateId,
                    'category_id' => $categoryId,
                ],
                [
                    'status' => 'ACTIVE',
                ]
            );
        } else {
            GroupGate::query()
                ->where('gates_id', $gateId)
                ->where('category_id', $categoryId)
                ->delete();
        }

        return redirect()->route('events.group-gates.index', [
            'event' => $event,
            'gate_id' => $gateId,
            'page' => $page,
        ]);
    }

    public function bulkToggle(Request $request, Event $event)
    {
        $data = $request->validate([
            'gate_id' => ['required', 'integer', 'exists:gates,id'],
            'action' => ['required', 'in:check_all,uncheck_all'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $gateId = (int) $data['gate_id'];
        $action = (string) $data['action'];
        $page = (int) ($data['page'] ?? 1);

        $categoryIds = TicketType::query()
            ->where('events_id', $event->id)
            ->pluck('id');

        if ($action === 'check_all') {
            foreach ($categoryIds as $categoryId) {
                GroupGate::query()->firstOrCreate(
                    [
                        'gates_id' => $gateId,
                        'category_id' => (int) $categoryId,
                    ],
                    [
                        'status' => 'ACTIVE',
                    ]
                );
            }
        } else {
            GroupGate::query()
                ->where('gates_id', $gateId)
                ->whereIn('category_id', $categoryIds)
                ->delete();
        }

        return redirect()->route('events.group-gates.index', [
            'event' => $event,
            'gate_id' => $gateId,
            'page' => $page,
        ]);
    }
}
