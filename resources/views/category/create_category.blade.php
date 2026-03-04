@extends('layouts.app')

@section('content')
<div class="relative min-h-[calc(100vh-64px)] overflow-hidden bg-slate-100 dark:bg-slate-950">
  <div class="absolute inset-0 bg-gradient-to-br from-slate-200/70 via-slate-100 to-slate-50 dark:from-slate-950 dark:via-slate-950 dark:to-slate-900"></div>
  <div class="absolute inset-0 bg-slate-900/20 backdrop-blur-[2px] dark:bg-black/30"></div>

  <div class="relative min-h-[calc(100vh-64px)] grid place-items-center p-4 md:p-8">
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
@endsection
