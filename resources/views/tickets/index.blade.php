@extends('layouts.app')

@section('content')
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
              Event: <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $event->name }}</span>
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

        <div class="flex items-center gap-2">
          <span class="px-3 py-2 rounded-2xl text-xs font-bold border border-slate-200 bg-slate-50 text-slate-700
                       dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-200">
            Manage
          </span>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50/70 text-slate-700 dark:bg-slate-950/40 dark:text-slate-200">
            <tr>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Code</th>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Owner</th>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Status</th>
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
                  <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $t->owner_name ?? '-' }}</div>
                  @if(!empty($t->owner_email))
                    <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">{{ $t->owner_email }}</div>
                  @endif
                </td>

                <td class="px-4 py-4">
                  @php
                    $st = strtoupper((string) $t->status);
                    $badge = 'bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-950/40 dark:border-slate-800 dark:text-slate-200';

                    if (str_contains($st, 'VALID') || str_contains($st, 'ACTIVE')) {
                      $badge = 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-950/30 dark:border-emerald-900/60 dark:text-emerald-200';
                    } elseif (str_contains($st, 'DUP')) {
                      $badge = 'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-950/30 dark:border-amber-900/60 dark:text-amber-200';
                    } elseif (str_contains($st, 'INVALID') || str_contains($st, 'INACTIVE') || str_contains($st, 'USED')) {
                      $badge = 'bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-950/30 dark:border-rose-900/60 dark:text-rose-200';
                    }
                  @endphp

                  <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border text-xs font-extrabold {{ $badge }}">
                    <span class="h-2 w-2 rounded-full
                      {{ str_contains($st,'VALID') || str_contains($st,'ACTIVE') ? 'bg-emerald-500' : (str_contains($st,'DUP') ? 'bg-amber-500' : (str_contains($st,'INVALID') || str_contains($st,'INACTIVE') || str_contains($st,'USED') ? 'bg-rose-500' : 'bg-slate-400')) }}">
                    </span>
                    {{ $t->status }}
                  </span>
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
                <td colspan="4" class="px-4 py-10 text-center">
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
@endsection
