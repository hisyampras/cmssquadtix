@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-64px)] bg-gradient-to-br from-slate-50 via-white to-slate-100
            dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
  <div class="max-w-3xl mx-auto p-4 md:p-8 pb-12 md:pb-16 space-y-6">

    {{-- Header --}}
    <div class="rounded-3xl border border-slate-200/70 bg-white/75 backdrop-blur supports-[backdrop-filter]:bg-white/60
                shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] overflow-hidden
                dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
      <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-start gap-3">
          <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-700 text-white grid place-items-center shadow
                      dark:from-slate-200 dark:to-slate-100 dark:text-slate-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 4h2a2 2 0 012 2v2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2H9a2 2 0 01-2-2v-2a2 2 0 012-2h2V6a2 2 0 012-2z" />
            </svg>
          </div>
          <div>
            <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Edit Ticket</h1>
            <p class="text-sm text-slate-500 mt-1 dark:text-slate-400">
              Event: <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $event->event_code ?? '-' }} - {{ $event->name }}</span>
            </p>
          </div>
        </div>

        <a href="{{ route('events.tickets.index', $event) }}"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                  text-slate-800 font-bold shadow-sm transition focus:outline-none focus:ring-4 focus:ring-slate-200/70
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
        <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Ticket Information</div>
        <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">Ubah data ticket dan statusnya.</div>
      </div>

      <form method="POST" action="{{ route('events.tickets.update', [$event, $ticket]) }}" class="p-5 md:p-6 space-y-4">
        @csrf @method('PUT')

        <div>
          <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Code</label>
          <input name="code"
                 value="{{ old('code', $ticket->code) }}"
                 class="w-full mt-2 px-4 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                        focus:outline-none focus:ring-4 focus:ring-slate-200/70 transition
                        font-mono font-bold text-slate-900 placeholder:text-slate-400
                        dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-slate-700/50"
                 required>
          @error('code')
            <p class="mt-2 text-xs font-semibold text-rose-700 dark:text-rose-200">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Category</label>
          <input name="category"
                 list="ticketTypeOptions"
                 value="{{ old('category', optional($ticket->categoryRef)->category ?? 'REGULAR') }}"
                 class="w-full mt-2 px-4 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                        focus:outline-none focus:ring-4 focus:ring-slate-200/70 transition
                        font-semibold text-slate-900 placeholder:text-slate-400
                        dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-slate-700/50"
                 placeholder="Contoh: REGULAR / VIP / VVIP">
          <datalist id="ticketTypeOptions">
            <option value="REGULAR"></option>
            <option value="VIP"></option>
            <option value="VVIP"></option>
          </datalist>
          @error('category')
            <p class="mt-2 text-xs font-semibold text-rose-700 dark:text-rose-200">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">No Transaction</label>
          <input name="no_transaction"
                 value="{{ old('no_transaction', $ticket->no_transaction) }}"
                 class="w-full mt-2 px-4 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                        focus:outline-none focus:ring-4 focus:ring-slate-200/70 transition
                        font-semibold text-slate-900 placeholder:text-slate-400
                        dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-slate-700/50"
                 placeholder="Contoh: TRX-000123 (opsional)">
          @error('no_transaction')
            <p class="mt-2 text-xs font-semibold text-rose-700 dark:text-rose-200">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Name</label>
          <input name="name"
                 value="{{ old('name', $ticket->name) }}"
                 class="w-full mt-2 px-4 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                        focus:outline-none focus:ring-4 focus:ring-slate-200/70 transition
                        font-semibold text-slate-900 placeholder:text-slate-400
                        dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-slate-700/50"
                 placeholder="Nama pemilik tiket (opsional)">
          @error('name')
            <p class="mt-2 text-xs font-semibold text-rose-700 dark:text-rose-200">{{ $message }}</p>
          @enderror
        </div>

        <div>
          <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Other Data</label>
          <textarea name="other_data"
                    rows="3"
                    class="w-full mt-2 px-4 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                           focus:outline-none focus:ring-4 focus:ring-slate-200/70 transition
                           font-semibold text-slate-900 placeholder:text-slate-400
                           dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-slate-700/50"
                    placeholder="Data tambahan (opsional)">{{ old('other_data', $ticket->other_data) }}</textarea>
          @error('other_data')
            <p class="mt-2 text-xs font-semibold text-rose-700 dark:text-rose-200">{{ $message }}</p>
          @enderror
        </div>

        <div class="pt-2 flex flex-col sm:flex-row gap-2 sm:justify-end">
          <a href="{{ route('events.tickets.index', $event) }}"
             class="inline-flex items-center justify-center px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                    text-slate-800 font-bold transition focus:outline-none focus:ring-4 focus:ring-slate-200/70
                    dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:focus:ring-slate-700/50">
            Back
          </a>

          <button type="submit"
                  class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800
                         text-white font-extrabold shadow-sm transition focus:outline-none focus:ring-4 focus:ring-slate-200/70
                         dark:bg-slate-100 dark:hover:bg-white dark:text-slate-900 dark:shadow-none dark:focus:ring-slate-700/50">
            Update Ticket
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </button>
        </div>
      </form>
    </div>

  </div>
</div>
@endsection
