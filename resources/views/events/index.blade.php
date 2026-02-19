@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-64px)] bg-gradient-to-br from-slate-50 via-white to-slate-100
            dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
  <div class="max-w-6xl mx-auto p-4 md:p-8 space-y-6">

    {{-- Header --}}
    <div class="rounded-3xl border border-slate-200/70 bg-white/75 backdrop-blur supports-[backdrop-filter]:bg-white/60
                shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] overflow-hidden
                dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
      <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-start gap-3">
          <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-700 text-white grid place-items-center shadow
                      dark:from-slate-200 dark:to-slate-100 dark:text-slate-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M6 21h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <div>
            <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Events</h1>
            <p class="text-sm text-slate-500 mt-1 dark:text-slate-400">Kelola event, status aktif, dan akses tiket.</p>
          </div>
        </div>

        <a href="{{ route('events.create') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold shadow-sm transition
                  focus:outline-none focus:ring-4 focus:ring-slate-200/70
                  dark:bg-slate-100 dark:hover:bg-white dark:text-slate-900 dark:focus:ring-slate-700/50">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" />
          </svg>
          New Event
        </a>
      </div>

      <div class="h-1.5 bg-gradient-to-r from-emerald-400 via-sky-400 to-indigo-400"></div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 shadow-sm
                  dark:border-emerald-900/60 dark:bg-emerald-950/40 dark:text-emerald-200">
        <div class="flex items-start gap-3">
          <div class="h-10 w-10 rounded-2xl bg-emerald-100 text-emerald-700 grid place-items-center
                      dark:bg-emerald-900/40 dark:text-emerald-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
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
      <div class="p-5 md:p-6 border-b border-slate-200/70 flex items-center justify-between
                  dark:border-slate-800/70">
        <div>
          <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Daftar Event</div>
          <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">Klik Tickets untuk kelola tiket, atau Edit untuk ubah detail event.</div>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50/60 dark:bg-slate-950/40">
            <tr class="text-slate-600 dark:text-slate-300">
              <th class="text-left px-5 py-4 font-black uppercase tracking-wider text-[11px]">Nama</th>
              <th class="text-left px-5 py-4 font-black uppercase tracking-wider text-[11px]">Lokasi</th>
              <th class="text-left px-5 py-4 font-black uppercase tracking-wider text-[11px]">Status</th>
              <th class="text-right px-5 py-4 font-black uppercase tracking-wider text-[11px]">Aksi</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800/70">
            @forelse($events as $e)
              <tr class="hover:bg-slate-50/50 transition dark:hover:bg-slate-950/30">
                <td class="px-5 py-4">
                  <div class="font-extrabold text-slate-900 dark:text-slate-100">{{ $e->name }}</div>
                  <div class="text-xs text-slate-500 mt-0.5 dark:text-slate-400">Code: {{ $e->event_code ?? '-' }}</div>
                </td>

                <td class="px-5 py-4 text-slate-700 dark:text-slate-200">
                  {{ $e->location ?? '-' }}
                </td>

                <td class="px-5 py-4">
                  <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-2xl text-xs font-bold border
                    {{ $e->is_active
                        ? 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-950/40 dark:border-emerald-900/60 dark:text-emerald-200'
                        : 'bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-950/40 dark:border-slate-800 dark:text-slate-200' }}">
                    <span class="h-2 w-2 rounded-full {{ $e->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                    {{ $e->is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>

                <td class="px-5 py-4">
                  <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('events.tickets.index', $e) }}"
                       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold shadow-sm transition
                              dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:shadow-none">
                      Manage Tickets
                    </a>

                    <a href="{{ route('events.edit', $e) }}"
                       class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold shadow-sm transition
                              dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-200 dark:shadow-none">
                      Edit
                    </a>

                    <form class="inline" method="POST" action="{{ route('events.destroy', $e) }}" onsubmit="return confirm('Hapus event?')">
                      @csrf @method('DELETE')
                      <button type="submit"
                              class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold shadow-sm transition
                                     dark:border-rose-900/60 dark:bg-rose-950/30 dark:hover:bg-rose-950/45 dark:text-rose-200 dark:shadow-none">
                        Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="px-5 py-12">
                  <div class="flex flex-col items-center text-center">
                    <div class="h-14 w-14 rounded-2xl bg-slate-100 text-slate-700 grid place-items-center
                                dark:bg-slate-950/40 dark:text-slate-200">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M4 11h16M6 21h12a2 2 0 002-2V7a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                      </svg>
                    </div>
                    <div class="mt-3 font-extrabold text-slate-900 dark:text-slate-100">Belum ada event</div>
                    <div class="text-sm text-slate-500 mt-1 dark:text-slate-400">Klik “New Event” untuk mulai membuat event pertama.</div>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-2">
      {{ $events->links() }}
    </div>

  </div>
</div>
@endsection
