@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-64px)] bg-gradient-to-br from-slate-50 via-white to-slate-100
            dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
  <div class="max-w-4xl mx-auto p-4 md:p-8 space-y-6">

    {{-- Header --}}
    <div class="rounded-3xl border border-slate-200/70 bg-white/75 backdrop-blur supports-[backdrop-filter]:bg-white/60
                shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] overflow-hidden
                dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
      <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-start gap-3">
          <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-700 text-white grid place-items-center shadow
                      dark:from-slate-200 dark:to-slate-100 dark:text-slate-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4h2m-1 0v16m-7-7h16" />
            </svg>
          </div>
          <div>
            <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Edit Event</h1>
            <p class="text-sm text-slate-500 mt-1 dark:text-slate-400">Perbarui informasi event yang sudah dibuat.</p>
          </div>
        </div>

        <a href="{{ route('events.index') }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold shadow-sm transition
                  focus:outline-none focus:ring-4 focus:ring-slate-200/70
                  dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:shadow-none dark:focus:ring-slate-700/50">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L10.414 9H17a1 1 0 110 2h-6.586l2.293 2.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
          </svg>
          Back
        </a>
      </div>

      <div class="h-1.5 bg-gradient-to-r from-emerald-400 via-sky-400 to-indigo-400"></div>
    </div>

    {{-- Form Card --}}
    <div class="rounded-3xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm overflow-hidden
                dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
      <div class="p-5 md:p-6 border-b border-slate-200/70 dark:border-slate-800/70">
        <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Event Details</div>
        <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">Perubahan akan memengaruhi proses scan & ticketing.</div>
      </div>

      <form method="POST" action="{{ route('events.update', $event) }}" class="p-5 md:p-6 space-y-5">
        @csrf
        @method('PUT')

        {{-- Name --}}
        <div>
          <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Name</label>
          <input name="name"
                 value="{{ old('name', $event->name) }}"
                 class="w-full mt-2 px-4 py-3 rounded-2xl border border-slate-200 bg-white
                        hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-200/70 transition
                        font-semibold text-slate-900 placeholder:text-slate-400
                        dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-slate-700/50"
                 required>

          @error('name')
            <p class="text-xs text-rose-700 mt-2 bg-rose-50 border border-rose-200 rounded-xl px-3 py-2
                      dark:text-rose-200 dark:bg-rose-950/30 dark:border-rose-900/60">
              {{ $message }}
            </p>
          @enderror
        </div>

        {{-- Location --}}
        <div>
          <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Location</label>
          <input name="location"
                 value="{{ old('location', $event->location) }}"
                 class="w-full mt-2 px-4 py-3 rounded-2xl border border-slate-200 bg-white
                        hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-200/70 transition
                        font-semibold text-slate-900 placeholder:text-slate-400
                        dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-slate-700/50"
                 placeholder="Contoh: Ballroom Hotel / Gedung / Kota">
        </div>

        {{-- Active --}}
        <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50 p-4
                    dark:border-slate-800 dark:bg-slate-950/30">
          <div>
            <div class="font-extrabold text-slate-900 dark:text-slate-100">Active</div>
            <div class="text-sm text-slate-500 dark:text-slate-400">Event aktif dapat digunakan untuk scan dan distribusi tiket.</div>
          </div>

          <label class="inline-flex items-center gap-3 cursor-pointer select-none">
            <input type="checkbox" name="is_active" value="1"
                   class="h-5 w-5 rounded border-slate-300 text-slate-900 focus:ring-slate-200
                          dark:border-slate-700 dark:bg-slate-950/30 dark:text-slate-100 dark:focus:ring-slate-700/50"
                   {{ old('is_active', $event->is_active) ? 'checked' : '' }}>
            <span class="text-sm font-bold text-slate-700 dark:text-slate-200">Set Active</span>
          </label>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between pt-2">
          <a href="{{ route('events.index') }}"
             class="inline-flex items-center justify-center px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold shadow-sm transition
                    focus:outline-none focus:ring-4 focus:ring-slate-200/70
                    dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:shadow-none dark:focus:ring-slate-700/50">
            Cancel
          </a>

          <button type="submit"
                  class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-extrabold shadow-sm transition
                         focus:outline-none focus:ring-4 focus:ring-slate-200/70
                         dark:bg-slate-100 dark:hover:bg-white dark:text-slate-900 dark:shadow-none dark:focus:ring-slate-700/50">
            Update Event
          </button>
        </div>
      </form>
    </div>

  </div>
</div>
@endsection
