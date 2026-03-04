@extends('layouts.app')

@section('content')
@php
  $gateOptions = $gates->map(fn ($gate) => ['id' => (int) $gate->id, 'name' => (string) $gate->gates_name])->values();
@endphp
<div class="min-h-[calc(100vh-64px)] bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
  <div class="max-w-7xl mx-auto p-4 md:p-8 space-y-6">

    <div class="rounded-3xl border border-slate-200/70 bg-white/75 backdrop-blur supports-[backdrop-filter]:bg-white/60 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] overflow-hidden dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
      <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Group Gate Mapping</h1>
          <p class="text-sm text-slate-500 mt-1 dark:text-slate-400">
            Event: <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $event->event_code ?? '-' }} - {{ $event->name }}</span>
          </p>
        </div>
        <a href="{{ route('events.tickets.index', $event) }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold shadow-sm transition dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100">
          Back
        </a>
      </div>
      <div class="h-1.5 bg-gradient-to-r from-emerald-400 via-sky-400 to-indigo-400"></div>
    </div>

    <div class="rounded-3xl border border-slate-200/70 bg-white/80 shadow-sm overflow-hidden dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
      <div class="p-4 md:p-5 border-b border-slate-200/70 dark:border-slate-800/70 space-y-3">
        <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Choose Gate</div>

        <form method="GET" action="{{ route('events.group-gates.index', $event) }}" class="grid grid-cols-1 md:grid-cols-12 gap-2"
              x-data="{
                open: false,
                query: '',
                selectedId: {{ $selectedGateId > 0 ? $selectedGateId : 'null' }},
                gates: @js($gateOptions),
                filtered() {
                  const q = this.query.toLowerCase().trim();
                  if (!q) return this.gates;
                  return this.gates.filter(g => g.name.toLowerCase().includes(q));
                },
                selectedName() {
                  const gate = this.gates.find(g => g.id === this.selectedId);
                  return gate ? gate.name : '';
                }
              }">
          <div class="md:col-span-10 relative">
            <input type="hidden" name="gate_id" :value="selectedId ?? ''">
            <button type="button"
                    @click="open = !open"
                    class="w-full px-3 py-2.5 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-left text-slate-900 focus:outline-none focus:ring-4 focus:ring-slate-200/70 dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-100 dark:focus:ring-slate-700/50">
              <span x-text="selectedName() || 'Select gate'"></span>
            </button>

            <div x-show="open" @click.outside="open = false" x-cloak
                 class="absolute mt-2 z-30 w-full rounded-2xl border border-slate-200 bg-white shadow-xl dark:border-slate-800 dark:bg-slate-900">
              <div class="p-2 border-b border-slate-200 dark:border-slate-800">
                <input type="text"
                       x-model="query"
                       placeholder="Search gate..."
                       class="w-full px-3 py-2 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-900 focus:outline-none focus:ring-4 focus:ring-slate-200/70 dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-100 dark:focus:ring-slate-700/50">
              </div>
              <div class="max-h-64 overflow-y-auto p-1">
                <template x-for="gate in filtered()" :key="gate.id">
                  <button type="button"
                          @click="selectedId = gate.id; open = false; query = ''"
                          class="w-full text-left px-3 py-2 rounded-xl text-sm font-semibold text-slate-800 hover:bg-slate-100 dark:text-slate-100 dark:hover:bg-slate-800">
                    <span x-text="gate.name"></span>
                  </button>
                </template>
                <div x-show="filtered().length === 0" class="px-3 py-2 text-sm text-slate-500 dark:text-slate-400">
                  Gate tidak ditemukan.
                </div>
              </div>
            </div>
          </div>
          <div class="md:col-span-2">
            <button type="submit"
                    class="w-full inline-flex items-center justify-center px-3 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold transition dark:bg-slate-100 dark:hover:bg-white dark:text-slate-900">
              Apply
            </button>
          </div>
        </form>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50/70 text-slate-700 dark:bg-slate-950/40 dark:text-slate-200">
            <tr>
              <th class="text-center px-4 py-3 font-black uppercase tracking-wider text-[11px]">
                <div class="flex items-center justify-center gap-2">
                  <span>Enable</span>
                  @if($selectedGateId > 0 && $categories->count() > 0)
                    <form method="POST" action="{{ route('events.group-gates.bulk-toggle', $event) }}">
                      @csrf
                      <input type="hidden" name="gate_id" value="{{ $selectedGateId }}">
                      <input type="hidden" name="action" value="check_all">
                      <input type="hidden" name="page" value="{{ $categories->currentPage() }}">
                      <button type="submit"
                              class="px-2 py-1 rounded-lg border border-emerald-200 bg-emerald-50 text-emerald-700 text-[10px] font-extrabold hover:bg-emerald-100 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-200">
                        Check All
                      </button>
                    </form>
                    <form method="POST" action="{{ route('events.group-gates.bulk-toggle', $event) }}">
                      @csrf
                      <input type="hidden" name="gate_id" value="{{ $selectedGateId }}">
                      <input type="hidden" name="action" value="uncheck_all">
                      <input type="hidden" name="page" value="{{ $categories->currentPage() }}">
                      <button type="submit"
                              class="px-2 py-1 rounded-lg border border-rose-200 bg-rose-50 text-rose-700 text-[10px] font-extrabold hover:bg-rose-100 dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-200">
                        Uncheck All
                      </button>
                    </form>
                  @endif
                </div>
              </th>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Category</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800/70">
            @forelse($categories as $category)
              @php $isChecked = $checkedCategoryIds->contains((int) $category->id); @endphp
              <tr class="hover:bg-slate-50/60 transition dark:hover:bg-slate-950/30">
                <td class="px-4 py-3 text-center">
                  @if($selectedGateId > 0)
                    <form method="POST" action="{{ route('events.group-gates.toggle', $event) }}" class="inline-flex items-center">
                      @csrf
                      <input type="hidden" name="gate_id" value="{{ $selectedGateId }}">
                      <input type="hidden" name="category_id" value="{{ $category->id }}">
                      <input type="hidden" name="page" value="{{ $categories->currentPage() }}">
                      <input type="hidden" name="checked" value="{{ $isChecked ? 1 : 0 }}">
                      <input type="checkbox"
                             @checked($isChecked)
                             class="h-5 w-5 rounded border-slate-300 text-slate-900 focus:ring-slate-500"
                             onchange="this.form.querySelector('input[name=checked]').value = this.checked ? 1 : 0; this.form.submit();">
                    </form>
                  @else
                    <input type="checkbox" disabled class="h-5 w-5 rounded border-slate-300 text-slate-900 opacity-50 cursor-not-allowed">
                  @endif
                </td>
                <td class="px-4 py-3 font-semibold text-slate-900 dark:text-slate-100">{{ strtoupper($category->category) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="2" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">Belum ada category untuk event ini.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      <div class="p-4 md:p-5 border-t border-slate-200/70 dark:border-slate-800/70">
        {{ $categories->links() }}
      </div>
    </div>
  </div>
</div>
@endsection
