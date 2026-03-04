@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-64px)] bg-gradient-to-br from-slate-50 via-white to-slate-100
            dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
  <div class="max-w-3xl mx-auto p-4 md:p-8 space-y-6">

    <div class="rounded-3xl border border-slate-200/70 bg-white/75 backdrop-blur supports-[backdrop-filter]:bg-white/60
                shadow-[0_10px_30px_-18px_rgba(15,23,42,0.35)] overflow-hidden
                dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
      <div class="p-5 md:p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-start gap-3">
          <div class="h-12 w-12 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-700 text-white grid place-items-center shadow
                      dark:from-slate-200 dark:to-slate-100 dark:text-slate-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7h16M4 12h16M4 17h16" />
            </svg>
          </div>
          <div>
            <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Bulk Import Tickets</h1>
            <p class="text-sm text-slate-500 mt-1 dark:text-slate-400">
              Event: <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $event->event_code ?? '-' }} - {{ $event->name }}</span>
            </p>
          </div>
        </div>

        <div class="flex items-center gap-2">
          <a href="{{ route('events.tickets.template', $event) }}"
             class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                    text-slate-800 font-bold shadow-sm transition
                    dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100">
            Download Template
          </a>
          <a href="{{ route('events.tickets.index', $event) }}"
             class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                    text-slate-800 font-bold shadow-sm transition
                    dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100">
            Back
          </a>
        </div>
      </div>
      <div class="h-1.5 bg-gradient-to-r from-emerald-400 via-sky-400 to-indigo-400"></div>
    </div>

    <div class="rounded-3xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm overflow-hidden
                dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
      <div class="p-5 md:p-6 border-b border-slate-200/70 dark:border-slate-800/70">
        <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Upload CSV</div>
        <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">Format: <code>code,no_transaction,category,name,other_data</code> (header optional). Kolom selain <code>code</code> opsional. Duplicate code akan dilewati otomatis.</div>
      </div>

      <form method="POST" action="{{ route('events.tickets.bulk.store', $event) }}" enctype="multipart/form-data" class="p-5 md:p-6 space-y-4">
        @csrf

        <div>
          <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">CSV File</label>
          <input type="file" name="csv_file" accept=".csv,text/csv"
                 class="w-full mt-2 px-4 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                        focus:outline-none focus:ring-4 focus:ring-slate-200/70 transition
                        text-slate-900 dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:focus:ring-slate-700/50"
                 required>

          @error('csv_file')
            <p class="mt-2 text-xs font-semibold text-rose-700 dark:text-rose-200">{{ $message }}</p>
          @enderror
        </div>

        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs text-slate-600 dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-300">
          <div class="font-bold mb-2">Contoh isi template:</div>
          <pre class="whitespace-pre-wrap">code,no_transaction,category,name,other_data
TCKT-000001,TRX-000001,REGULAR,John Doe,Table A1
TCKT-000002,TRX-000002,VIP,Jane Doe,Seat B-12</pre>
        </div>

        <div class="pt-2 flex justify-end">
          <button type="submit"
                  class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800
                         text-white font-extrabold shadow-sm transition
                         dark:bg-slate-100 dark:hover:bg-white dark:text-slate-900">
            Import CSV
          </button>
        </div>
      </form>
    </div>

  </div>
</div>
@endsection
