@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-64px)] bg-gradient-to-br from-slate-50 via-white to-slate-100
            dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
  <div class="max-w-7xl mx-auto p-4 md:p-8 space-y-6">

    {{-- Header --}}
    <div class="rounded-2xl border border-slate-200/70 bg-white/70 backdrop-blur supports-[backdrop-filter]:bg-white/60 shadow-sm
                dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
      <div class="p-5 md:p-6 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex items-start gap-3">
          <div class="h-11 w-11 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-700 text-white grid place-items-center shadow
                      dark:from-slate-200 dark:to-slate-100 dark:text-slate-900">
            {{-- simple icon --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.4 20a7.4 7.4 0 10-14.8 0" />
            </svg>
          </div>
          <div>
            <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">Dashboard Scan Gate</h1>
            <p class="text-sm text-slate-500 mt-1 dark:text-slate-400">Monitoring validasi tiket secara real-time per event & gate.</p>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
          <a href="{{ route('events.index') }}"
             class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-semibold shadow-sm transition
                    focus:outline-none focus:ring-4 focus:ring-slate-200/70
                    dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:shadow-none dark:focus:ring-slate-700/50">
            Manage Events
          </a>
          <a href="{{ route('scan.index', ['event_id'=>$eventId]) }}"
             class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold shadow-sm transition
                    focus:outline-none focus:ring-4 focus:ring-slate-200/70
                    dark:bg-slate-100 dark:hover:bg-white dark:text-slate-900 dark:shadow-none dark:focus:ring-slate-700/50">
            Open Scan Gate
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 opacity-90" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
            </svg>
          </a>
        </div>
      </div>

      {{-- Filter bar --}}
      <div class="px-5 md:px-6 pb-5 md:pb-6">
        <div class="flex flex-col lg:flex-row lg:items-end gap-3 lg:gap-4">
          <div class="flex-1">
            <label class="text-xs font-bold text-slate-600 uppercase tracking-wider dark:text-slate-300">Event</label>
            <div class="mt-2 relative">
              <select id="eventSelect"
                      class="w-full appearance-none px-4 py-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50
                             focus:outline-none focus:ring-4 focus:ring-slate-200/70 font-semibold text-slate-900 transition
                             dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:focus:ring-slate-700/50">
                @foreach($events as $e)
                  <option value="{{ $e->id }}" @selected($e->id == $eventId)>{{ $e->event_code ?? '-' }} - {{ $e->name }}</option>
                @endforeach
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-slate-500 dark:text-slate-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                </svg>
              </div>
            </div>
          </div>

          <div class="flex items-center gap-2">
            <button id="btnRefresh"
                    class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl border border-slate-200 bg-white hover:bg-slate-50
                           font-semibold text-slate-900 shadow-sm transition focus:outline-none focus:ring-4 focus:ring-slate-200/70
                           dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:shadow-none dark:focus:ring-slate-700/50">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M5 19a9 9 0 0114-7M19 5a9 9 0 00-14 7" />
              </svg>
              Refresh
            </button>

            <div class="px-4 py-3 rounded-xl bg-slate-900 text-white shadow-sm
                        dark:bg-slate-950/40 dark:text-slate-100 dark:shadow-none dark:border dark:border-slate-800">
              <div class="text-[11px] uppercase tracking-wider opacity-80">Last update</div>
              <div id="last" class="text-sm font-semibold">-</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
      <div class="rounded-2xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm p-5
                  dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Total Tickets</div>
            <div id="k_totalTickets" class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">0</div>
          </div>
          <div class="h-11 w-11 rounded-2xl bg-slate-100 text-slate-900 grid place-items-center
                      dark:bg-slate-950/40 dark:text-slate-100 dark:border dark:border-slate-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m6 10V7M5 12h14" />
            </svg>
          </div>
        </div>
        <div class="mt-4 h-2 rounded-full bg-slate-100 overflow-hidden dark:bg-slate-950/40">
          <div id="bar_total" class="h-2 rounded-full bg-slate-900 w-0 transition-all dark:bg-slate-100"></div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm p-5
                  dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-xs font-bold text-emerald-600 uppercase tracking-wider dark:text-emerald-300">Checkin</div>
            <div id="k_validToday" class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">0</div>
          </div>
          <div class="h-11 w-11 rounded-2xl bg-emerald-50 text-emerald-700 grid place-items-center
                      dark:bg-emerald-950/30 dark:text-emerald-200 dark:border dark:border-emerald-900/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 3h4a2 2 0 012 2v14a2 2 0 01-2 2h-4" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 17l5-5-5-5" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H3" />
            </svg>
          </div>
        </div>
        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Total scan tiket checkin.</p>
      </div>

      <div class="rounded-2xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm p-5
                  dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-xs font-bold text-indigo-600 uppercase tracking-wider dark:text-indigo-300">Recheckin</div>
            <div id="k_validMonth" class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">0</div>
          </div>
          <div class="h-11 w-11 rounded-2xl bg-indigo-50 text-indigo-700 grid place-items-center
                      dark:bg-indigo-950/30 dark:text-indigo-200 dark:border dark:border-indigo-900/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 20v-6h-6" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 19a8 8 0 0114-6M19 5a8 8 0 00-14 6" />
            </svg>
          </div>
        </div>
        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Total scan tiket recheckin.</p>
      </div>

      <div class="rounded-2xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm p-5
                  dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-xs font-bold text-slate-500 uppercase tracking-wider dark:text-slate-400">Pending</div>
            <div id="k_validAll" class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">0</div>
          </div>
          <div class="h-11 w-11 rounded-2xl bg-slate-100 text-slate-700 grid place-items-center
                      dark:bg-slate-950/40 dark:text-slate-100 dark:border dark:border-slate-800">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2" />
              <circle cx="12" cy="12" r="8" stroke-width="2" />
            </svg>
          </div>
        </div>
        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Total tiket berstatus pending.</p>
      </div>

      <div class="rounded-2xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm p-5
                  dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-xs font-bold text-amber-600 uppercase tracking-wider dark:text-amber-300">Checkout</div>
            <div id="k_dupAll" class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">0</div>
          </div>
          <div class="h-11 w-11 rounded-2xl bg-amber-50 text-amber-700 grid place-items-center
                      dark:bg-amber-950/30 dark:text-amber-200 dark:border dark:border-amber-900/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v14a2 2 0 002 2h4" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 7l-5 5 5 5" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h12" />
            </svg>
          </div>
        </div>
        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Total scan tiket checkout.</p>
      </div>

      <div class="rounded-2xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm p-5
                  dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
        <div class="flex items-center justify-between">
          <div>
            <div class="text-xs font-bold text-rose-600 uppercase tracking-wider dark:text-rose-300">Recheckout</div>
            <div id="k_invalidAll" class="mt-2 text-3xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">0</div>
          </div>
          <div class="h-11 w-11 rounded-2xl bg-rose-50 text-rose-700 grid place-items-center
                      dark:bg-rose-950/30 dark:text-rose-200 dark:border dark:border-rose-900/60">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3H5a2 2 0 00-2 2v14a2 2 0 002 2h4" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 7l-5 5 5 5" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h12" />
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 5v4M14 7h4" />
            </svg>
          </div>
        </div>
        <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">Total scan tiket recheckout.</p>
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm p-5
                dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
      <div class="flex items-start justify-between gap-3">
        <div>
          <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Status Trend (5 Menit)</div>
          <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">Pending, Checkin, Recheckin, Checkout, Recheckout.</div>
        </div>
        <div id="trendLegend" class="flex flex-wrap items-center justify-end gap-2 text-[11px]"></div>
      </div>
      <div class="mt-4">
        <svg id="trendSvg" viewBox="0 0 980 280" class="w-full h-[280px]"></svg>
      </div>
    </div>

    {{-- Panels --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
      <div class="rounded-2xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm
                  dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
        <div class="p-5 border-b border-slate-200/70 flex items-center justify-between dark:border-slate-800/70">
          <div>
            <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Top Gate</div>
            <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">Per gate: unique valid + total attempt.</div>
          </div>
          <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-slate-900 text-white
                       dark:bg-slate-100 dark:text-slate-900">Live</span>
        </div>
        <div class="p-5">
          <div id="byGate" class="text-sm text-slate-700 leading-relaxed dark:text-slate-200">-</div>
        </div>
      </div>

      <div class="rounded-2xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm
                  dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
        <div class="p-5 border-b border-slate-200/70 flex items-center justify-between dark:border-slate-800/70">
          <div>
            <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Recent Scans</div>
            <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">Aktivitas terbaru (auto refresh tiap 5 detik).</div>
          </div>
          <span class="text-xs font-bold px-3 py-1.5 rounded-full bg-slate-100 text-slate-700
                       dark:bg-slate-950/40 dark:text-slate-200 dark:border dark:border-slate-800">Latest</span>
        </div>
        <div class="p-5">
          <div id="recent" class="text-sm text-slate-700 leading-relaxed dark:text-slate-200">-</div>
          <div class="mt-3 flex items-center justify-between gap-2">
            <button id="recentPrev"
                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed
                           dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-200 dark:hover:bg-slate-950/50">
              Prev
            </button>
            <div id="recentPageInfo" class="text-xs text-slate-500 dark:text-slate-400">Page 1/1</div>
            <button id="recentNext"
                    class="inline-flex items-center justify-center px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed
                           dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-200 dark:hover:bg-slate-950/50">
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="text-xs text-slate-500 flex items-center justify-between dark:text-slate-400">
      <div>Tip: gunakan tombol <span class="font-semibold text-slate-700 dark:text-slate-200">Refresh</span> jika ingin update manual.</div>
      <div class="hidden md:block">UI: Tailwind • Layout modern • Soft shadows</div>
    </div>

  </div>
</div>

<script>
  const el = (id) => document.getElementById(id);

  const fmt = (n) => {
    try { return new Intl.NumberFormat('id-ID').format(Number(n ?? 0)); }
    catch { return String(n ?? 0); }
  };
  const RECENT_PAGE_SIZE = 10;
  let recentRows = [];
  let recentPage = 1;

  function renderTrend(chart) {
    const svg = el('trendSvg');
    if (!svg) return;

    const labels = Array.isArray(chart?.labels) ? chart.labels : [];
    const series = chart?.series || {};
    const defs = [
      { key: 'pending', label: 'Pending', color: '#475569' },
      { key: 'checkin', label: 'Checkin', color: '#059669' },
      { key: 'recheckin', label: 'Recheckin', color: '#4f46e5' },
      { key: 'checkout', label: 'Checkout', color: '#d97706' },
      { key: 'recheckout', label: 'Recheckout', color: '#e11d48' },
    ];

    if (labels.length === 0) {
      svg.innerHTML = '';
      return;
    }

    const padLeft = 46;
    const padRight = 16;
    const padTop = 16;
    const padBottom = 34;
    const w = 980;
    const h = 280;
    const plotW = w - padLeft - padRight;
    const plotH = h - padTop - padBottom;
    const maxY = Math.max(
      1,
      ...defs.flatMap(d => (Array.isArray(series[d.key]) ? series[d.key] : []).map(v => Number(v || 0)))
    );
    const xStep = labels.length > 1 ? (plotW / (labels.length - 1)) : 0;
    const y = (v) => padTop + (plotH - ((Number(v || 0) / maxY) * plotH));
    const x = (i) => padLeft + (xStep * i);

    const gridLines = [0, 0.25, 0.5, 0.75, 1].map(r => {
      const yy = padTop + (plotH * r);
      const val = Math.round(maxY * (1 - r));
      return `
        <line x1="${padLeft}" y1="${yy}" x2="${w - padRight}" y2="${yy}" stroke="currentColor" opacity="0.12" />
        <text x="${padLeft - 8}" y="${yy + 4}" text-anchor="end" font-size="10" fill="currentColor" opacity="0.65">${val}</text>
      `;
    }).join('');

    const xLabels = labels.map((lb, i) => {
      const show = i === 0 || i === labels.length - 1 || i % 4 === 0;
      if (!show) return '';
      return `<text x="${x(i)}" y="${h - 10}" text-anchor="middle" font-size="10" fill="currentColor" opacity="0.65">${lb}</text>`;
    }).join('');

    const lines = defs.map(def => {
      const arr = Array.isArray(series[def.key]) ? series[def.key] : [];
      const pts = arr.map((v, i) => `${x(i)},${y(v)}`).join(' ');
      if (!pts) return '';
      return `<polyline fill="none" stroke="${def.color}" stroke-width="2.5" points="${pts}" />`;
    }).join('');

    svg.innerHTML = `
      <g class="text-slate-600 dark:text-slate-300">
        ${gridLines}
        ${xLabels}
      </g>
      <g>${lines}</g>
    `;

    const legend = el('trendLegend');
    if (legend) {
      legend.innerHTML = defs.map(def => `
        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md border border-slate-200 bg-white/80 text-slate-700
                     dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-200">
          <span class="inline-block h-2.5 w-2.5 rounded-full" style="background:${def.color}"></span>
          ${def.label}
        </span>
      `).join('');
    }
  }

  function renderRecentPage() {
    const totalPages = Math.max(1, Math.ceil(recentRows.length / RECENT_PAGE_SIZE));
    recentPage = Math.min(Math.max(recentPage, 1), totalPages);

    const start = (recentPage - 1) * RECENT_PAGE_SIZE;
    const pageRows = recentRows.slice(start, start + RECENT_PAGE_SIZE);

    const recHtml = pageRows.map(r => {
      const t = (r.scanned_at ?? '').replace('T',' ').substring(0,19);
      const gate = r.gate_name ? `• ${r.gate_name}` : '';
      const statusId = Number(r.status_tickets_id ?? 0);
      const statusName = String(r.status_name ?? '').trim();
      const statusLabel = statusName !== '' ? statusName : String(r.scan_result ?? '-');

      let badgeClass = 'bg-slate-100 text-slate-700 dark:bg-slate-950/40 dark:text-slate-200 dark:border dark:border-slate-800';
      if (statusId === 2) badgeClass = 'bg-emerald-100 text-emerald-800 dark:bg-emerald-950/30 dark:text-emerald-200 dark:border dark:border-emerald-900/60';
      if (statusId === 4) badgeClass = 'bg-indigo-100 text-indigo-800 dark:bg-indigo-950/30 dark:text-indigo-200 dark:border dark:border-indigo-900/60';
      if (statusId === 3) badgeClass = 'bg-amber-100 text-amber-800 dark:bg-amber-950/30 dark:text-amber-200 dark:border dark:border-amber-900/60';
      if (statusId === 5) badgeClass = 'bg-rose-100 text-rose-800 dark:bg-rose-950/30 dark:text-rose-200 dark:border dark:border-rose-900/60';

      return `
        <div class="py-2 border-b border-slate-200/60 last:border-b-0 dark:border-slate-800/70">
          <div class="flex items-center gap-2">
            <span class="text-[11px] font-extrabold px-2 py-1 rounded-lg ${badgeClass}">${statusLabel || '-'}</span>
            <span class="text-sm font-semibold text-slate-800 dark:text-slate-100">${t || '-'}</span>
            <span class="text-xs text-slate-500 dark:text-slate-400">${gate}</span>
          </div>
        </div>
      `;
    }).join('');

    if (el('recent')) {
      el('recent').innerHTML = recHtml || '<span class="text-slate-500 dark:text-slate-400">-</span>';
    }
    if (el('recentPageInfo')) {
      el('recentPageInfo').textContent = `Page ${recentPage}/${totalPages}`;
    }
    if (el('recentPrev')) el('recentPrev').disabled = recentPage <= 1;
    if (el('recentNext')) el('recentNext').disabled = recentPage >= totalPages;
  }

  async function loadData() {
    const btn = el('btnRefresh');
    try {
      if (btn) {
        btn.disabled = true;
        btn.classList.add('opacity-70','cursor-not-allowed');
      }

      const eventSelect = el('eventSelect');
      const eventId = eventSelect ? eventSelect.value : null;

      const url = new URL("{{ route('dashboard.data') }}");
      if (eventId) url.searchParams.set('event_id', eventId);

      const res = await fetch(url, { headers: {'Accept':'application/json'} });

      if (!res.ok) {
        console.error('HTTP Error', res.status, await res.text());
        return;
      }

      const data = await res.json();
      if (!data || data.ok !== true) {
        console.error('Bad JSON payload:', data);
        return;
      }

      const kpi = data.kpi || {};
      const totalTickets = kpi.totalTickets ?? 0;
      const validToday   = kpi.validToday ?? 0;
      const validMonth   = kpi.validMonth ?? 0;
      const validAll     = kpi.validAll ?? 0;
      const dupAll       = kpi.dupAll ?? 0;
      const invalidAll   = kpi.invalidAll ?? 0;

      if (el('k_totalTickets')) el('k_totalTickets').textContent = fmt(totalTickets);
      if (el('k_validToday'))   el('k_validToday').textContent   = fmt(validToday);
      if (el('k_validMonth'))   el('k_validMonth').textContent   = fmt(validMonth);
      if (el('k_validAll'))     el('k_validAll').textContent     = fmt(validAll);
      if (el('k_dupAll'))       el('k_dupAll').textContent       = fmt(dupAll);
      if (el('k_invalidAll'))   el('k_invalidAll').textContent   = fmt(invalidAll);

      const pct = totalTickets > 0 ? Math.min(100, Math.round((validAll / totalTickets) * 100)) : 0;
      const bar = el('bar_total');
      if (bar) bar.style.width = pct + '%';

      const gateHtml = (data.byGate || []).map(g => {
        const name = g.gate_name ?? '-';
        const validUnique = fmt(g.valid_unique ?? 0);
        const totalAttempt = fmt(g.total_attempt ?? 0);
        return `
          <div class="flex items-center justify-between py-2 border-b border-slate-200/60 last:border-b-0 dark:border-slate-800/70">
            <div class="font-semibold text-slate-800 dark:text-slate-100">${name}</div>
            <div class="text-right">
              <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">${validUnique}</div>
              <div class="text-[11px] text-slate-500 dark:text-slate-400">attempt ${totalAttempt}</div>
            </div>
          </div>
        `;
      }).join('');
      if (el('byGate')) el('byGate').innerHTML = gateHtml || '<span class="text-slate-500 dark:text-slate-400">-</span>';

      recentRows = Array.isArray(data.recent) ? data.recent : [];
      const maxPages = Math.max(1, Math.ceil(recentRows.length / RECENT_PAGE_SIZE));
      if (recentPage > maxPages) recentPage = 1;
      renderRecentPage();
      renderTrend(data.chart || {});

      if (el('last')) el('last').textContent = new Date().toLocaleString('id-ID');
    } catch (e) {
      console.error('JS Error:', e);
    } finally {
      if (btn) {
        btn.disabled = false;
        btn.classList.remove('opacity-70','cursor-not-allowed');
      }
    }
  }

  const btn = el('btnRefresh');
  if (btn) btn.addEventListener('click', loadData);
  el('recentPrev')?.addEventListener('click', () => {
    recentPage -= 1;
    renderRecentPage();
  });
  el('recentNext')?.addEventListener('click', () => {
    recentPage += 1;
    renderRecentPage();
  });

  const sel = el('eventSelect');
  if (sel) {
    sel.addEventListener('change', () => {
      const u = new URL(window.location);
      u.searchParams.set('event_id', sel.value);
      history.replaceState({}, '', u);
      loadData();
    });
  }

  loadData();
  setInterval(loadData, 12000);
</script>
@endsection
