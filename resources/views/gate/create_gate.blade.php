@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-64px)] bg-gradient-to-br from-slate-50 via-white to-slate-100 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
  <div class="max-w-7xl mx-auto p-4 md:p-8 space-y-6">

    <div class="rounded-3xl border border-slate-200/70 bg-white/75 backdrop-blur supports-[backdrop-filter]:bg-white/60 shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] overflow-hidden dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
      <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
          <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Gate Management</h1>
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

    @if(session('success'))
      <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 dark:border-emerald-900/60 dark:bg-emerald-950/30 dark:text-emerald-200">
        <div class="font-extrabold">Success</div>
        <div class="text-sm mt-0.5 opacity-90">{{ session('success') }}</div>
      </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
      <div class="lg:col-span-4 rounded-3xl border border-slate-200/70 bg-white/80 shadow-sm overflow-hidden dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
        <div class="p-4 md:p-5 border-b border-slate-200/70 dark:border-slate-800/70">
          <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">
            {{ $editGate ? 'Edit Gate' : 'New Gate' }}
          </div>
        </div>
        <form method="POST" action="{{ $editGate ? route('events.gates.update', [$event, $editGate]) : route('events.gates.store', $event) }}" class="p-4 md:p-5 space-y-4">
          @csrf
          @if($editGate)
            @method('PUT')
          @endif

          <div>
            <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Gate Name</label>
            <input type="text"
                   name="gates_name"
                   value="{{ old('gates_name', $editGate->gates_name ?? '') }}"
                   placeholder="Contoh: Gate A / VIP / Entrance 1"
                   class="mt-2 w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 font-semibold text-slate-900 placeholder:text-slate-400 transition hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-200/70 dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-100 dark:placeholder:text-slate-500 dark:hover:bg-slate-950/50 dark:focus:ring-slate-700/50"
                   required>
            @error('gates_name')
              <p class="mt-2 text-xs font-semibold text-rose-700 dark:text-rose-200">{{ $message }}</p>
            @enderror
          </div>

          <div class="flex items-center justify-end gap-2">
            @if($editGate)
              <a href="{{ route('events.gates.index', $event) }}"
                 class="inline-flex items-center justify-center rounded-2xl border border-slate-200 bg-white px-4 py-2.5 font-bold text-slate-800 transition hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-100 dark:hover:bg-slate-950/50">
                Cancel
              </a>
            @endif
            <button type="submit"
                    class="inline-flex items-center justify-center rounded-2xl bg-slate-900 px-4 py-2.5 font-extrabold text-white transition hover:bg-slate-800 dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
              {{ $editGate ? 'Update Gate' : 'Save Gate' }}
            </button>
          </div>
        </form>
      </div>

      <div class="lg:col-span-8 rounded-3xl border border-slate-200/70 bg-white/80 shadow-sm overflow-hidden dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
        <div class="p-4 md:p-5 border-b border-slate-200/70 dark:border-slate-800/70">
          <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">List Gates</div>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="bg-slate-50/70 text-slate-700 dark:bg-slate-950/40 dark:text-slate-200">
              <tr>
                <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">No</th>
                <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Gate Name</th>
                <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Created</th>
                <th class="text-right px-4 py-3 font-black uppercase tracking-wider text-[11px]">Aksi</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200/70 dark:divide-slate-800/70">
              @forelse($gates as $gate)
                <tr class="hover:bg-slate-50/60 transition dark:hover:bg-slate-950/30">
                  <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ $loop->iteration }}</td>
                  <td class="px-4 py-3 font-semibold text-slate-900 dark:text-slate-100">{{ $gate->gates_name }}</td>
                  <td class="px-4 py-3 text-slate-600 dark:text-slate-300">{{ optional($gate->created_at)->format('Y-m-d H:i') }}</td>
                  <td class="px-4 py-3">
                    <div class="flex items-center justify-end gap-2">
                      <a href="{{ route('events.gates.index', ['event' => $event, 'edit' => $gate->id]) }}"
                         class="inline-flex items-center justify-center px-3 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold transition dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100">
                        Edit
                      </a>
                      <form method="POST" action="{{ route('events.gates.destroy', [$event, $gate]) }}" onsubmit="return confirm('Hapus gate ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center justify-center px-3 py-2 rounded-2xl border border-rose-200 bg-rose-50 hover:bg-rose-100 text-rose-700 font-extrabold transition dark:border-rose-900/60 dark:bg-rose-950/30 dark:hover:bg-rose-950/45 dark:text-rose-200">
                          Delete
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">Belum ada gate.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
