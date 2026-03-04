@extends('layouts.app')

@section('content')
@php
  $showCreateCategory = request()->boolean('show_create_category') || $errors->has('category');
@endphp
<div class="min-h-[calc(100vh-64px)] bg-gradient-to-br from-slate-50 via-white to-slate-100
            dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
  <div class="max-w-7xl mx-auto p-4 md:p-8 space-y-6">

    {{-- Header --}}
    <div class="rounded-3xl border border-slate-200/70 bg-white/75 backdrop-blur supports-[backdrop-filter]:bg-white/60
                shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] overflow-hidden
                dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
      <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-start gap-3">
          <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-700 text-white grid place-items-center shadow
                      dark:from-slate-200 dark:to-slate-100 dark:text-slate-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m6 10V7M5 12h14" />
            </svg>
          </div>
          <div>
            <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Tickets</h1>
            <p class="text-sm text-slate-500 mt-1 dark:text-slate-400">
              Event: <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $event->event_code ?? '-' }} - {{ $event->name }}</span>
            </p>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
          <a href="{{ route('events.index') }}"
             class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                    text-slate-800 font-bold shadow-sm transition focus:outline-none focus:ring-4 focus:ring-slate-200/70
                    dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:shadow-none dark:focus:ring-slate-700/50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L10.414 9H17a1 1 0 110 2h-6.586l2.293 2.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Back
          </a>
        </div>
      </div>

      <div class="h-1.5 bg-gradient-to-r from-emerald-400 via-sky-400 to-indigo-400"></div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800
                  dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-200">
        <div class="flex items-start gap-3">
          <div class="h-10 w-10 rounded-2xl bg-emerald-100 text-emerald-700 grid place-items-center
                      dark:bg-emerald-950/40 dark:text-emerald-200 dark:border dark:border-emerald-900/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
          </div>
          <div>
            <div class="font-extrabold">Success</div>
            <div class="text-sm mt-0.5 opacity-90">{{ session('success') }}</div>
          </div>
        </div>
      </div>
    @endif

    @if(session('import_errors'))
      <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900
                  dark:border-amber-900/60 dark:bg-amber-950/30 dark:text-amber-200">
        <div class="font-extrabold">Beberapa baris CSV dilewati</div>
        <ul class="mt-2 text-sm list-disc pl-5 space-y-1">
          @foreach(session('import_errors') as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- Category Entry Rules --}}
    <div class="rounded-3xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm overflow-hidden
                dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
      <div class="p-4 md:p-5 border-b border-slate-200/70 dark:border-slate-800/70 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
          <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Gate Rule per Category</div>
          <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">
            Atur berapa kali tiket boleh check-in. Kosongkan untuk <span class="font-bold">unlimited</span>.
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
          <a href="{{ route('events.tickets.index', ['event' => $event, 'show_create_category' => 1]) }}"
             class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800
                    text-white font-extrabold transition
                    dark:bg-slate-100 dark:hover:bg-white dark:text-slate-900">
              + New Category
          </a>
          <a href="{{ route('events.gates.index', $event) }}"
             class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800
                    text-white font-extrabold transition
                    dark:bg-slate-100 dark:hover:bg-white dark:text-slate-900">
              + New Gate
          </a>
          <a href="{{ route('events.group-gates.index', $event) }}"
             class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800
                    text-white font-extrabold transition
                    dark:bg-slate-100 dark:hover:bg-white dark:text-slate-900">
              + New Group Gate
          </a>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50/70 text-slate-700 dark:bg-slate-950/40 dark:text-slate-200">
            <tr>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Category</th>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Total Ticket</th>
              <th class="text-right px-4 py-3 font-black uppercase tracking-wider text-[11px]">Max Entry Rule</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800/70">
            @forelse(($ticketTypeStats ?? collect()) as $typeStat)
              @php
                $typeKey = strtoupper((string) $typeStat->category);
                $policy = ($ticketTypePolicies ?? collect())->get($typeKey);
              @endphp
              <tr class="hover:bg-slate-50/60 transition dark:hover:bg-slate-950/30">
                <td class="px-4 py-3">
                  <span class="font-mono font-bold text-slate-900 dark:text-slate-100">{{ $typeKey }}</span>
                </td>
                <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ number_format($typeStat->total_ticket) }}</td>
                <td class="px-4 py-3 text-right">
                  <form method="POST" action="{{ route('events.tickets.type-policy.upsert', $event) }}"
                        class="inline-flex items-center gap-2">
                    @csrf
                    <input type="hidden" name="category" value="{{ $typeKey }}">
                    <input type="number" name="max_entry_count" min="1" max="1000"
                           value="{{ old('category') === $typeKey ? old('max_entry_count') : optional($policy)->max_entry_count }}"
                           class="w-28 px-3 py-2 rounded-xl border border-slate-200 bg-white text-slate-900 text-sm
                                  focus:outline-none focus:ring-4 focus:ring-slate-200/70
                                  dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-100 dark:focus:ring-slate-700/50"
                           placeholder="Unlimited">
                    <button type="submit"
                            class="inline-flex items-center justify-center px-3 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-extrabold
                                   dark:bg-slate-100 dark:hover:bg-white dark:text-slate-900">
                      Save
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="px-4 py-6 text-center text-slate-500 dark:text-slate-400">
                  Belum ada category. Tambahkan ticket dulu untuk mengatur gate rule.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
      @error('max_entry_count')
        <div class="px-4 py-3 border-t border-rose-200 bg-rose-50 text-xs font-semibold text-rose-700
                    dark:border-rose-900/60 dark:bg-rose-950/30 dark:text-rose-200">
          {{ $message }}
        </div>
      @enderror
    </div>

    {{-- Table Card --}}
    <div class="rounded-3xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm overflow-hidden
                dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">

      {{-- Top bar --}}
      <div class="p-4 md:p-5 border-b border-slate-200/70 flex flex-col md:flex-row md:items-center md:justify-between gap-3
                  dark:border-slate-800/70">
        <div>
          <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Daftar Tickets</div>
          <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">
            Total: <span class="font-bold text-slate-700 dark:text-slate-200">{{ $tickets->total() }}</span>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
          <form method="GET" action="{{ route('events.tickets.index', $event) }}" class="flex items-center gap-2">
            <input type="text"
                   name="q"
                   value="{{ $q ?? '' }}"
                   placeholder="Search code..."
                   class="w-full sm:w-52 px-3 py-2.5 rounded-2xl border border-slate-200 bg-white text-sm font-semibold text-slate-900
                          focus:outline-none focus:ring-4 focus:ring-slate-200/70
                          dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-100 dark:focus:ring-slate-700/50">
            <button type="submit"
                    class="inline-flex items-center justify-center px-3 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                           text-slate-800 font-extrabold transition
                           dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100">
              Search
            </button>
          </form>
          <a href="{{ route('events.tickets.create', $event) }}"
             class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800
                    text-white font-extrabold transition
                    dark:bg-slate-100 dark:hover:bg-white dark:text-slate-900">
            + New Ticket
          </a>
          <a href="{{ route('events.tickets.bulk.form', $event) }}"
             class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                    text-slate-800 font-extrabold transition
                    dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100">
            + Bulk CSV
          </a>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50/70 text-slate-700 dark:bg-slate-950/40 dark:text-slate-200">
            <tr>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Code</th>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Category</th>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Name</th>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Other Data</th>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Check-in Status</th>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Created</th>
              <th class="text-right px-4 py-3 font-black uppercase tracking-wider text-[11px]">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800/70">
            @forelse($tickets as $t)
              <tr class="hover:bg-slate-50/60 transition dark:hover:bg-slate-950/30">
                <td class="px-4 py-4">
                  <div class="font-mono font-bold text-slate-900 dark:text-slate-100">
                    {{ $t->code }}
                  </div>
                  <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">Ticket Code</div>
                </td>

                <td class="px-4 py-4">
                  <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-extrabold
                               bg-sky-50 border-sky-200 text-sky-700 dark:bg-sky-950/30 dark:border-sky-900/60 dark:text-sky-200">
                    {{ strtoupper($t->category ?? 'REGULAR') }}
                  </span>
                </td>

                <td class="px-4 py-4">
                  <div class="font-semibold text-slate-900 dark:text-slate-100">
                    {{ $t->name ?: '-' }}
                  </div>
                </td>

                <td class="px-4 py-4">
                  <div class="max-w-xs truncate font-medium text-slate-700 dark:text-slate-200" title="{{ $t->other_data }}">
                    {{ $t->other_data ?: '-' }}
                  </div>
                </td>

                <td class="px-4 py-4">
                  @php $statusId = (int) ($t->latest_status_tickets_id ?? 1); @endphp
                  @if($statusId === 2)
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-extrabold
                                 bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-950/30 dark:border-emerald-900/60 dark:text-emerald-200">
                      <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                      CHECKIN
                    </span>
                  @elseif($statusId === 4)
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-extrabold
                                 bg-indigo-50 border-indigo-200 text-indigo-700 dark:bg-indigo-950/30 dark:border-indigo-900/60 dark:text-indigo-200">
                      <span class="h-2 w-2 rounded-full bg-indigo-500"></span>
                      RECHECKIN
                    </span>
                  @elseif($statusId === 3)
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-extrabold
                                 bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-950/30 dark:border-amber-900/60 dark:text-amber-200">
                      <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                      CHECKOUT
                    </span>
                  @elseif($statusId === 5)
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-extrabold
                                 bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-950/30 dark:border-rose-900/60 dark:text-rose-200">
                      <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                      RECHECKOUT
                    </span>
                  @else
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-extrabold
                                 bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-950/40 dark:border-slate-800 dark:text-slate-200">
                      <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                      PENDING
                    </span>
                  @endif
                </td>

                <td class="px-4 py-4">
                  <div class="font-semibold text-slate-900 dark:text-slate-100">{{ optional($t->created_at)->format('Y-m-d H:i') }}</div>
                  <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">ID: {{ $t->id }}</div>
                </td>

                <td class="px-4 py-4">
                  <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('events.tickets.edit', [$event, $t]) }}"
                       class="inline-flex items-center justify-center px-3 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                              text-slate-800 font-bold transition focus:outline-none focus:ring-4 focus:ring-slate-200/70
                              dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:focus:ring-slate-700/50">
                      Edit
                    </a>

                    <form method="POST" action="{{ route('events.tickets.destroy', [$event, $t]) }}"
                          onsubmit="return confirm('Hapus ticket?')" class="inline">
                      @csrf @method('DELETE')
                      <button type="submit"
                              class="inline-flex items-center justify-center px-3 py-2 rounded-2xl border border-rose-200 bg-rose-50 hover:bg-rose-100
                                     text-rose-700 font-extrabold transition focus:outline-none focus:ring-4 focus:ring-rose-200/60
                                     dark:border-rose-900/60 dark:bg-rose-950/30 dark:hover:bg-rose-950/45 dark:text-rose-200 dark:focus:ring-rose-900/30">
                        Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-4 py-10 text-center">
                  <div class="text-slate-500 dark:text-slate-400">Belum ada ticket.</div>
                  <div class="mt-3">
                    <a href="{{ route('events.tickets.create', $event) }}"
                       class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800
                              text-white font-extrabold transition
                              dark:bg-slate-100 dark:hover:bg-white dark:text-slate-900">
                      + New Ticket
                    </a>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      {{-- Footer / Pagination --}}
      <div class="p-4 md:p-5 border-t border-slate-200/70 dark:border-slate-800/70">
        {{ $tickets->links() }}
      </div>
    </div>

  </div>
</div>

@if($showCreateCategory)
  <div class="fixed inset-0 z-50">
    <a href="{{ route('events.tickets.index', $event) }}"
       class="absolute inset-0 bg-slate-900/15 backdrop-blur-[1px] dark:bg-black/25"
       aria-label="Close modal"></a>

    <div class="relative min-h-screen grid place-items-center p-4 md:p-8">
      <div class="w-full max-w-xl rounded-3xl border border-slate-200/70 bg-white/95 p-5 md:p-6 shadow-2xl
                  dark:border-slate-800/70 dark:bg-slate-900/90 dark:shadow-none">
        <div class="flex items-start justify-between gap-3">
          <div>
            <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Add New Category</h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
              Event: <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $event->event_code ?? '-' }} - {{ $event->name }}</span>
            </p>
          </div>
          <a href="{{ route('events.tickets.index', $event) }}"
             class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-slate-200 bg-white text-slate-700 hover:bg-slate-50
                    dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-200 dark:hover:bg-slate-950/60"
             aria-label="Close">
            x
          </a>
        </div>

        <form method="POST" action="{{ route('events.categories.store', $event) }}" class="mt-6 space-y-4">
          @csrf

          <div>
            <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Name Category</label>
            <input name="category"
                   value="{{ old('category') }}"
                   class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-semibold text-slate-900 placeholder:text-slate-400
                          transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-200/70
                          dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-100 dark:placeholder:text-slate-500 dark:hover:bg-slate-950/50 dark:focus:ring-slate-700/50"
                   placeholder="Contoh: REGULAR / VIP / VVIP"
                   required>
            @error('category')
              <p class="mt-2 text-xs font-semibold text-rose-700 dark:text-rose-200">{{ $message }}</p>
            @enderror
          </div>

          <div class="flex items-center justify-end gap-2 pt-1">
            <a href="{{ route('events.tickets.index', $event) }}"
               class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 font-bold text-slate-800 transition hover:bg-slate-50
                      focus:outline-none focus:ring-4 focus:ring-slate-200/70 dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-100 dark:hover:bg-slate-950/50 dark:focus:ring-slate-700/50">
              Close
            </a>
            <button type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-2.5 font-extrabold text-white transition hover:bg-slate-800
                           dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
              Save
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endif
@endsection
