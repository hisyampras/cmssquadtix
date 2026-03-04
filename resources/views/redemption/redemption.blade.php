@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-64px)] bg-gradient-to-br from-slate-50 via-white to-slate-100
            dark:from-slate-950 dark:via-slate-950 dark:to-slate-900">
  <div class="max-w-6xl mx-auto p-4 md:p-8 space-y-6">

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
            <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">
              Redemption Scan
            </h1>
            <p class="text-sm text-slate-500 mt-1 dark:text-slate-400">
              Scan tiket lalu tampilkan data redemption di tabel bawah.
            </p>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
          <button id="btnFullscreen"
                  type="button"
                  class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                         text-slate-800 font-bold shadow-sm transition focus:outline-none focus:ring-4 focus:ring-slate-200/70
                         dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:shadow-none dark:focus:ring-slate-700/50">
            Fullscreen
          </button>

          <a href="{{ route('dashboard.index', ['events_id' => $eventId]) }}"
             class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                    text-slate-800 font-bold shadow-sm transition focus:outline-none focus:ring-4 focus:ring-slate-200/70
                    dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:shadow-none dark:focus:ring-slate-700/50">
            Back Dashboard
          </a>
        </div>
      </div>
      <div class="h-1.5 bg-gradient-to-r from-emerald-400 via-sky-400 to-indigo-400"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">
      <div class="lg:col-span-7 space-y-4">
        <div class="rounded-3xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm p-5
                    dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Event</label>
              <div class="mt-2 relative">
                <select id="events_id"
                        class="w-full appearance-none px-4 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                               focus:outline-none focus:ring-4 focus:ring-slate-200/70 font-semibold text-slate-900 transition
                               dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:focus:ring-slate-700/50">
                  @foreach($events as $e)
                    <option value="{{ $e->id }}" @selected($e->id == $eventId)>{{ $e->event_code ?? '-' }} - {{ $e->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div>
              <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Gate Name</label>
              <input id="gate_name"
                     value="{{ $gate }}"
                     class="w-full mt-2 px-4 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                            focus:outline-none focus:ring-4 focus:ring-slate-200/70 transition
                            font-semibold text-slate-900 placeholder:text-slate-400
                            dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-slate-700/50"
                     placeholder="Contoh: REDEMPTION">
            </div>
          </div>
        </div>

        <div class="rounded-3xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm p-5
                    dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Hardware Scanner</div>
              <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">
                Scan pakai alat scanner, lalu data langsung masuk ke tabel.
              </div>
            </div>
            <button id="btnFocus" type="button"
                    class="px-3 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs
                           dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100">
              Focus
            </button>
          </div>

          <div class="mt-4">
            <input id="code"
                   class="w-full px-5 py-4 rounded-2xl border border-slate-200 bg-white
                          text-lg md:text-xl font-mono tracking-wide text-slate-900
                          hover:bg-slate-50 focus:outline-none focus:ring-4 focus:ring-slate-200/70 transition
                          placeholder:text-slate-400
                          dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-slate-700/50"
                   placeholder="Scan barcode/QR di sini..."
                   autofocus
                   autocomplete="off"
                   autocapitalize="characters">
          </div>
        </div>

        <div class="rounded-3xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm overflow-hidden
                    dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
          <div class="p-4 md:p-5 border-b border-slate-200/70 dark:border-slate-800/70 flex items-center justify-between gap-3">
            <div class="min-w-0">
              <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Camera Scan</div>
              <div id="camHint" class="text-xs text-slate-500 mt-1 dark:text-slate-400 truncate">
                Auto-start, arahkan kode ke frame.
              </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
              <button id="btnCamSwitch" type="button"
                      class="px-3 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs
                             dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100">
                Switch
              </button>
              <button id="btnCamRetry" type="button"
                      class="px-3 py-2 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs
                             dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                Retry
              </button>
              <button id="btnCamToggle" type="button"
                      class="px-3 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs
                             dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100">
                Hide Camera
              </button>
            </div>
          </div>

          <div id="cameraStage" class="relative">
            <div id="qrReader"
                 class="w-full bg-black aspect-[4/3] sm:aspect-video
                        border-t border-slate-200/70 dark:border-slate-800/70"></div>
          </div>
        </div>
      </div>

      <div class="lg:col-span-5">
        <div class="rounded-3xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm p-5
                    dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
          <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Info</div>
          <div class="text-xs text-slate-500 mt-2 dark:text-slate-400">
            Tidak ada notifikasi popup/result card. Hasil scan langsung dirender dalam tabel di bawah.
          </div>
        </div>
      </div>
    </div>

    <div class="rounded-3xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm overflow-hidden
                dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
      <div class="p-4 md:p-5 border-b border-slate-200/70 dark:border-slate-800/70">
        <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Redemption Data</div>
        <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">
          Jika no_transaction sama, semua code terkait akan ditampilkan.
        </div>
        <div id="actionMessage" class="mt-2 text-xs font-semibold text-slate-600 dark:text-slate-300"></div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-slate-50/70 text-slate-700 dark:bg-slate-950/40 dark:text-slate-200">
            <tr>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Code</th>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">No Transaction</th>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Name</th>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Other Data</th>
              <th class="text-left px-4 py-3 font-black uppercase tracking-wider text-[11px]">Redeem</th>
            </tr>
          </thead>
          <tbody id="redemptionRows" class="divide-y divide-slate-200/70 dark:divide-slate-800/70">
            <tr>
              <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
                Belum ada hasil scan.
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

  </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>
<script>
  const el = (id) => document.getElementById(id);
  let currentRows = [];

  function renderRows(rows) {
    const body = el('redemptionRows');
    if (!body) return;
    currentRows = Array.isArray(rows) ? rows : [];

    if (!currentRows.length) {
      body.innerHTML = `
        <tr>
          <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
            Data tidak ditemukan.
          </td>
        </tr>
      `;
      return;
    }

    body.innerHTML = currentRows.map((row) => {
      const key = String(row.id);
      const redeemed = !!row.is_redeemed;
      return `
        <tr class="hover:bg-slate-50/60 transition dark:hover:bg-slate-950/30">
          <td class="px-4 py-3 font-mono font-bold text-slate-900 dark:text-slate-100">${row.code ?? '-'}</td>
          <td class="px-4 py-3 font-semibold text-slate-900 dark:text-slate-100">${row.no_transaction ?? '-'}</td>
          <td class="px-4 py-3 font-semibold text-slate-900 dark:text-slate-100">${row.name ?? '-'}</td>
          <td class="px-4 py-3 text-slate-700 dark:text-slate-200">${row.other_data ?? '-'}</td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-2">
              <button type="button"
                      data-ticket-id="${key}"
                      ${redeemed ? 'disabled' : ''}
                      class="btn-redeem inline-flex items-center justify-center px-3 py-1.5 rounded-xl text-xs font-extrabold border transition
                             ${redeemed
                              ? 'bg-emerald-50 border-emerald-200 text-emerald-700 cursor-not-allowed dark:bg-emerald-950/30 dark:border-emerald-900/60 dark:text-emerald-200'
                              : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50 dark:bg-slate-950/30 dark:border-slate-800 dark:text-slate-100 dark:hover:bg-slate-950/50'}">
                ${redeemed ? 'Redeemed' : 'Redeem'}
              </button>
            </div>
            <div class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Status: ${row.status_label ?? 'Pending'}</div>
          </td>
        </tr>
      `;
    }).join('');
  }

  function setActionMessage(message, isError = false) {
    const node = el('actionMessage');
    if (!node) return;
    node.textContent = message || '';
    node.className = `mt-2 text-xs font-semibold ${isError ? 'text-rose-700 dark:text-rose-200' : 'text-slate-600 dark:text-slate-300'}`;
  }

  async function submitScan(codeValue) {
    const code = String(codeValue || '').trim().toUpperCase();
    if (!code) return;

    const res = await fetch("{{ route('redemption.search') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        events_id: Number(el('events_id')?.value || 0),
        code: code
      })
    });

    const data = await res.json();
    renderRows(data.rows || []);
    setActionMessage(data.message || '', !data.ok);
    if (el('code')) {
      el('code').value = '';
      el('code').focus();
    }
  }

  el('code')?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      submitScan(el('code').value);
    }
  });

  el('btnFocus')?.addEventListener('click', () => el('code')?.focus());

  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('.btn-redeem');
    if (!btn) return;
    const id = String(btn.getAttribute('data-ticket-id') || '');
    if (!id) return;

    const res = await fetch("{{ route('redemption.action') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify({
        events_id: Number(el('events_id')?.value || 0),
        ticket_id: Number(id),
        gate_name: el('gate_name')?.value || null
      })
    });

    const data = await res.json();
    renderRows(data.rows || currentRows);
    setActionMessage(data.message || '', !data.ok);
  });

  function isFullscreen() {
    return !!(document.fullscreenElement || document.webkitFullscreenElement);
  }
  function updateFsBtn() {
    const b = el('btnFullscreen');
    if (!b) return;
    b.textContent = isFullscreen() ? 'Exit Fullscreen' : 'Fullscreen';
  }
  async function toggleFullscreen() {
    try {
      const root = document.documentElement;
      if (!isFullscreen()) {
        if (root.requestFullscreen) await root.requestFullscreen();
        else if (root.webkitRequestFullscreen) await root.webkitRequestFullscreen();
      } else {
        if (document.exitFullscreen) await document.exitFullscreen();
        else if (document.webkitExitFullscreen) await document.webkitExitFullscreen();
      }
    } catch (_) {
      // ignore
    } finally {
      updateFsBtn();
      el('code')?.focus();
    }
  }
  el('btnFullscreen')?.addEventListener('click', toggleFullscreen);
  document.addEventListener('fullscreenchange', updateFsBtn);
  document.addEventListener('webkitfullscreenchange', updateFsBtn);

  let qrScanner = null;
  let cameraRunning = false;
  let cameraHidden = false;
  let desiredFacingMode = 'environment';
  let lastDecodeAt = 0;

  function setCamHint(text) {
    if (el('camHint')) el('camHint').textContent = text;
  }
  function isSecureContextForCamera() {
    return window.isSecureContext || location.hostname === 'localhost' || location.hostname === '127.0.0.1';
  }

  async function stopCamera() {
    try {
      if (qrScanner && cameraRunning) {
        await qrScanner.stop();
        await qrScanner.clear();
      }
    } catch (_) {
      // ignore
    } finally {
      cameraRunning = false;
    }
  }

  async function startCamera() {
    if (cameraHidden) return;
    if (!window.Html5Qrcode) {
      setCamHint('Library kamera belum ter-load.');
      return;
    }
    if (!isSecureContextForCamera()) {
      setCamHint('Kamera browser butuh HTTPS atau localhost.');
      return;
    }
    if (cameraRunning) return;

    try {
      if (!qrScanner) qrScanner = new Html5Qrcode('qrReader');
      cameraRunning = true;
      setCamHint('Scanning...');

      await qrScanner.start(
        { facingMode: desiredFacingMode },
        {
          fps: 14,
          qrbox: (w, h) => {
            const size = Math.floor(Math.min(w, h) * 0.7);
            return { width: size, height: size };
          },
          aspectRatio: 1.0,
          disableFlip: false,
        },
        async (decodedText) => {
          const now = Date.now();
          if (now - lastDecodeAt < 1200) return;
          lastDecodeAt = now;
          await stopCamera();
          await submitScan(String(decodedText).trim());
          setTimeout(() => startCamera().catch(() => {}), 900);
        },
        () => {}
      );
    } catch (_) {
      cameraRunning = false;
      setCamHint('Tidak bisa akses kamera. Cek permission/HTTPS.');
    }
  }

  el('btnCamSwitch')?.addEventListener('click', async () => {
    desiredFacingMode = desiredFacingMode === 'environment' ? 'user' : 'environment';
    setCamHint('Switching...');
    await stopCamera();
    await startCamera();
  });

  el('btnCamRetry')?.addEventListener('click', async () => {
    setCamHint('Retry...');
    await stopCamera();
    await startCamera();
  });

  async function setCameraHidden(hidden) {
    cameraHidden = hidden;
    const stage = el('cameraStage');
    const btn = el('btnCamToggle');
    if (cameraHidden) {
      await stopCamera();
      if (stage) stage.classList.add('hidden');
      if (btn) btn.textContent = 'Show Camera';
      setCamHint('Camera disembunyikan.');
      return;
    }

    if (stage) stage.classList.remove('hidden');
    if (btn) btn.textContent = 'Hide Camera';
    await startCamera();
  }

  el('btnCamToggle')?.addEventListener('click', async () => {
    await setCameraHidden(!cameraHidden);
  });

  document.addEventListener('DOMContentLoaded', () => {
    updateFsBtn();
    el('code')?.focus();
    startCamera();
  });
</script>
@endsection
