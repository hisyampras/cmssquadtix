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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M7 3v18m10-8h4M7 11h10" />
            </svg>
          </div>
          <div>
            <h1 class="text-xl md:text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">
              Scan Gate
              <span class="ml-2 inline-flex items-center px-2.5 py-1 rounded-full border text-[11px] font-black tracking-widest uppercase bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-950/30 dark:border-emerald-900/60 dark:text-emerald-200">IN</span>
            </h1>
            <p class="text-sm text-slate-500 mt-1 dark:text-slate-400">
              Hardware scanner utama • Kamera sebagai opsi cepat
            </p>
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 sm:items-center">
          <button id="btnFullscreen"
                  type="button"
                  class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                         text-slate-800 font-bold shadow-sm transition focus:outline-none focus:ring-4 focus:ring-slate-200/70
                         dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:shadow-none dark:focus:ring-slate-700/50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
              <path d="M3 3h5a1 1 0 110 2H5v3a1 1 0 11-2 0V3zm12 0a2 2 0 012 2v5a1 1 0 11-2 0V5h-3a1 1 0 110-2h5zM3 12a1 1 0 012 0v3h3a1 1 0 110 2H5a2 2 0 01-2-2v-5zm14 0a1 1 0 10-2 0v3h-3a1 1 0 100 2h3a2 2 0 002-2v-5z"/>
            </svg>
            Fullscreen
          </button>

          <a href="{{ route('dashboard.index', ['event_id'=>$eventId]) }}"
             class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                    text-slate-800 font-bold shadow-sm transition focus:outline-none focus:ring-4 focus:ring-slate-200/70
                    dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:shadow-none dark:focus:ring-slate-700/50">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L10.414 9H17a1 1 0 110 2h-6.586l2.293 2.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Back Dashboard
          </a>
        </div>
      </div>

      <div class="h-1.5 bg-gradient-to-r from-emerald-400 via-sky-400 to-indigo-400"></div>
    </div>

    {{-- Main grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-4">

      {{-- LEFT --}}
      <div class="lg:col-span-7 space-y-4">

        {{-- Setup controls --}}
        <div class="rounded-3xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm p-5
                    dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Event</label>
              <div class="mt-2 relative">
                <select id="event_id"
                        class="w-full appearance-none px-4 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
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

            <div>
              <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Gate Name</label>
              <input id="gate_name"
                     value="{{ $gate }}"
                     class="w-full mt-2 px-4 py-3 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                            focus:outline-none focus:ring-4 focus:ring-slate-200/70 transition
                            font-semibold text-slate-900 placeholder:text-slate-400
                            dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-slate-700/50"
                     placeholder="Contoh: Gate A / VIP / Entrance 1">
            </div>
          </div>

          <div class="mt-5 rounded-2xl border border-slate-200/70 bg-slate-50/70 p-4
                      dark:border-slate-800/70 dark:bg-slate-950/30">
            <div class="flex items-center justify-between gap-2">
              <div>
                <label class="text-[11px] font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">
                  Allowed Ticket Types
                </label>
                <div id="typeSummary" class="text-xs text-slate-500 mt-1 dark:text-slate-400">
                  Semua tipe diterima.
                </div>
              </div>
              <div class="flex items-center gap-2">
                <button id="btnTypeAll" type="button"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold
                               dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100">
                  Check All
                </button>
                <button id="btnTypeNone" type="button"
                        class="px-3 py-1.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold
                               dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100">
                  Clear
                </button>
              </div>
            </div>

            <div id="typeFilters" class="mt-3 flex flex-wrap gap-2"></div>
            <div id="typeEmpty" class="hidden mt-2 text-xs text-slate-500 dark:text-slate-400">
              Belum ada ticket type untuk event ini. Semua scan akan dianggap valid tipe.
            </div>
          </div>

        </div>

        {{-- 1) Hardware Scanner --}}
        <div class="rounded-3xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm p-5
                    dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
          <div class="flex items-start justify-between gap-3">
            <div>
              <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Hardware Scanner</div>
              <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">
                Scan pakai alat scanner (USB/Bluetooth) → biasanya otomatis kirim <b>Enter</b>.
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
                   placeholder="Scan barcode/QR di sini…"
                   autofocus
                   inputmode="text"
                   autocomplete="off"
                   autocapitalize="characters">
          </div>
        </div>

        {{-- 2) Camera Scanner (Premium UI) --}}
        <div class="rounded-3xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm overflow-hidden
                    dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">

          {{-- Top control bar (minimal, premium) --}}
          <div class="p-4 md:p-5 border-b border-slate-200/70 dark:border-slate-800/70 flex items-center justify-between gap-3">
            <div class="min-w-0">
              <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Camera Scan</div>
              <div id="camHint" class="text-xs text-slate-500 mt-1 dark:text-slate-400 truncate">
                Auto-start • Arahkan kode ke frame
              </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
              {{-- Sound toggle --}}
              <button id="btnSound"
                      type="button"
                      class="inline-flex items-center gap-2 px-3 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50
                             text-slate-800 font-bold text-xs transition
                             dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100">
                <span class="h-2.5 w-2.5 rounded-full bg-emerald-500" id="soundDot"></span>
                <span id="soundText">Sound ON</span>
              </button>

              {{-- Switch camera --}}
              <button id="btnCamSwitch" type="button"
                      class="px-3 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs
                             dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100">
                Switch
              </button>

              {{-- Retry --}}
              <button id="btnCamRetry" type="button"
                      class="px-3 py-2 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs
                             dark:bg-slate-100 dark:text-slate-900 dark:hover:bg-white">
                Retry
              </button>

              {{-- Hide / Show camera --}}
              <button id="btnCamToggle" type="button"
                      class="px-3 py-2 rounded-2xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-800 font-bold text-xs
                             dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-100">
                Hide Camera
              </button>
            </div>
          </div>

          {{-- Camera stage --}}
          <div id="cameraStage" class="relative">
            <div id="qrReader"
                 class="w-full bg-black aspect-[4/3] sm:aspect-video
                        border-t border-slate-200/70 dark:border-slate-800/70"></div>

            {{-- Premium overlay --}}
            <div class="pointer-events-none absolute inset-0">
              {{-- soft vignette --}}
              <div class="absolute inset-0 bg-gradient-to-b from-black/30 via-black/10 to-black/40"></div>

              {{-- top pills --}}
              <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-2">
                <div class="flex items-center gap-2">
                  <span class="text-[11px] font-black uppercase tracking-wider px-3 py-1.5 rounded-full
                               bg-white/85 text-slate-900 border border-white/60">
                    CAMERA
                  </span>
                  <span id="camState"
                        class="text-[11px] font-black uppercase tracking-wider px-3 py-1.5 rounded-full
                               bg-black/45 text-white border border-white/15">
                    STARTING
                  </span>
                </div>

                <span id="camFacingPill"
                      class="text-[11px] font-black uppercase tracking-wider px-3 py-1.5 rounded-full
                             bg-black/45 text-white border border-white/15">
                  BACK
                </span>
              </div>

              {{-- viewfinder frame --}}
              <div class="absolute inset-0 grid place-items-center">
                <div class="relative w-[74%] max-w-[360px] aspect-square">
                  {{-- outside mask --}}
                  <div class="absolute inset-0 rounded-[28px] shadow-[0_0_0_9999px_rgba(0,0,0,0.42)]"></div>

                  {{-- frame border --}}
                  <div class="absolute inset-0 rounded-[28px] border border-white/65"></div>

                  {{-- corner accents --}}
                  <div class="absolute -top-1 -left-1 h-10 w-10 border-t-4 border-l-4 border-white/90 rounded-tl-[22px]"></div>
                  <div class="absolute -top-1 -right-1 h-10 w-10 border-t-4 border-r-4 border-white/90 rounded-tr-[22px]"></div>
                  <div class="absolute -bottom-1 -left-1 h-10 w-10 border-b-4 border-l-4 border-white/90 rounded-bl-[22px]"></div>
                  <div class="absolute -bottom-1 -right-1 h-10 w-10 border-b-4 border-r-4 border-white/90 rounded-br-[22px]"></div>

                  {{-- scanning line --}}
                  <div id="scanLine"
                       class="absolute left-5 right-5 top-6 h-[2px] rounded-full bg-emerald-300/90
                              blur-[0.2px] opacity-90"></div>
                </div>
              </div>

              {{-- bottom hint --}}
              <div class="absolute bottom-3 left-3 right-3 text-center">
                <div class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-2xl bg-black/45 text-white text-xs border border-white/15">
                  Align QR/Barcode inside frame • Auto scan
                </div>
              </div>
            </div>
          </div>

          {{-- NOTE: detail cards removed (per request) --}}
        </div>

      </div>

      {{-- RIGHT: Result + Recent --}}
      <div class="lg:col-span-5 space-y-4">

        <div class="rounded-3xl border border-slate-200/70 bg-white/80 backdrop-blur shadow-sm overflow-hidden
                    dark:border-slate-800/70 dark:bg-slate-900/60 dark:shadow-none">
          <div class="p-5 border-b border-slate-200/70 dark:border-slate-800/70 flex items-center justify-between">
            <div>
              <div class="text-sm font-extrabold text-slate-900 dark:text-slate-100">Result</div>
              <div class="text-xs text-slate-500 mt-1 dark:text-slate-400">Status scan terakhir.</div>
            </div>
            <span id="pill"
                  class="text-[11px] font-black uppercase tracking-wider px-3 py-1.5 rounded-full border bg-slate-50 border-slate-200 text-slate-700
                         dark:bg-slate-950/40 dark:border-slate-800 dark:text-slate-200">
              READY
            </span>
          </div>

          <div id="resultBox" class="p-5 transition">
            <div class="flex items-start gap-3">
              <div id="iconBox"
                   class="h-11 w-11 rounded-2xl bg-slate-100 text-slate-900 grid place-items-center
                          dark:bg-slate-950/40 dark:text-slate-100 dark:border dark:border-slate-800">
                <svg id="iconReady" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                </svg>
              </div>
              <div>
                <div id="result" class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-slate-100">READY</div>
                <div id="message" class="text-slate-600 mt-1 dark:text-slate-300">Scan ticket…</div>
                {{-- meta boleh tetap ada, tapi ringan --}}
                <div id="meta" class="text-xs text-slate-500 mt-2 dark:text-slate-400">—</div>
              </div>
            </div>
          </div>

          <div class="px-5 pb-5">
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4
                        dark:border-slate-800 dark:bg-slate-950/30">
              <div class="flex items-center justify-between">
                <div class="text-xs font-black uppercase tracking-wider text-slate-600 dark:text-slate-300">Recent (local)</div>
                <button id="btnClearRecent" type="button"
                        class="text-xs font-bold px-2 py-1 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700
                               dark:border-slate-800 dark:bg-slate-950/30 dark:hover:bg-slate-950/50 dark:text-slate-200">
                  Clear
                </button>
              </div>

              <div id="recentWrap" class="mt-2 max-h-72 overflow-auto pr-1">
                <div id="recentLocal" class="text-sm text-slate-700 leading-relaxed dark:text-slate-200">-</div>
              </div>

              <div class="mt-2 text-[11px] text-slate-500 dark:text-slate-400">
                Menampilkan maksimal 20 item terbaru.
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>

  </div>
</div>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
  const el = (id) => document.getElementById(id);
  const EVENT_TICKET_TYPES = @json($eventTicketTypesByEventId ?? []);

  // -----------------------------
  // Preferences (Sound)
  // -----------------------------
  const SOUND_KEY = 'scan_sound_enabled_v1';
  let soundEnabled = true;

  function loadSoundPref() {
    const v = localStorage.getItem(SOUND_KEY);
    soundEnabled = (v === null) ? true : (v === '1');
    renderSoundBtn();
  }
  function saveSoundPref() {
    localStorage.setItem(SOUND_KEY, soundEnabled ? '1' : '0');
    renderSoundBtn();
  }
  function renderSoundBtn() {
    const t = el('soundText');
    const d = el('soundDot');
    if (t) t.textContent = soundEnabled ? 'Sound ON' : 'Sound OFF';
    if (d) {
      d.className = 'h-2.5 w-2.5 rounded-full';
      d.classList.add(soundEnabled ? 'bg-emerald-500' : 'bg-slate-400');
    }
  }

  // -----------------------------
  // Sound (Beep) - WebAudio
  // -----------------------------
  let audioCtx = null;
  function ensureAudio() {
    if (!soundEnabled) return;
    if (!audioCtx) {
      const AC = window.AudioContext || window.webkitAudioContext;
      if (AC) audioCtx = new AC();
    }
    if (audioCtx && audioCtx.state === 'suspended') audioCtx.resume().catch(()=>{});
  }
  function beep(freq = 880, duration = 0.10, type = 'sine', gainVal = 0.06) {
    if (!soundEnabled) return;
    ensureAudio();
    if (!audioCtx) return;

    const o = audioCtx.createOscillator();
    const g = audioCtx.createGain();

    o.type = type;
    o.frequency.value = freq;

    g.gain.setValueAtTime(0.0001, audioCtx.currentTime);
    g.gain.exponentialRampToValueAtTime(gainVal, audioCtx.currentTime + 0.01);
    g.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + duration);

    o.connect(g);
    g.connect(audioCtx.destination);

    o.start();
    o.stop(audioCtx.currentTime + duration + 0.02);
  }
  function playTone(result) {
    if (!soundEnabled) return;
    const r = String(result || '').toUpperCase();
    if (r === 'VALID') { beep(980, 0.08, 'triangle', 0.07); setTimeout(()=>beep(1310, 0.08, 'triangle', 0.07), 90); }
    else if (r === 'WARNING') { beep(520, 0.12, 'sine', 0.06); }
    else if (r === 'DUPLICATE' || r === 'DUP') { beep(660, 0.12, 'sine', 0.06); }
    else if (r === 'INVALID') { beep(220, 0.18, 'sawtooth', 0.05); }
    else if (r === 'CHECKING') { beep(520, 0.06, 'sine', 0.03); }
  }

  // -----------------------------
  // Vibration (mobile)
  // -----------------------------
  function vibrate(result) {
    const r = String(result || '').toUpperCase();
    if (!navigator.vibrate) return;
    if (r === 'VALID') navigator.vibrate([40, 40, 40]);
    else if (r === 'WARNING') navigator.vibrate([60, 40, 60]);
    else if (r === 'DUPLICATE' || r === 'DUP') navigator.vibrate([80]);
    else if (r === 'INVALID') navigator.vibrate([120, 60, 120]);
  }

  // -----------------------------
  // Recent
  // -----------------------------
  const recentList = [];
  const RECENT_LIMIT = 20;

  function renderRecent() {
    const node = el('recentLocal');
    if (!node) return;
    node.innerHTML = recentList.length ? recentList.map(x => `• ${x}`).join('<br>') : '-';
  }

  function pushRecent(line) {
    recentList.unshift(line);
    if (recentList.length > RECENT_LIMIT) recentList.length = RECENT_LIMIT;
    renderRecent();
    const wrap = el('recentWrap');
    if (wrap) wrap.scrollTop = 0;
  }

  el('btnClearRecent')?.addEventListener('click', () => {
    recentList.length = 0;
    renderRecent();
  });

  // -----------------------------
  // Ticket Type Filters
  // -----------------------------
  function storageKeyForTypes() {
    const eventId = String(el('event_id')?.value || '');
    return `scan:allowed_types:${eventId}`;
  }

  function getSelectedTypes() {
    return Array.from(document.querySelectorAll('.ticket-type-checkbox:checked'))
      .map((node) => String(node.value || '').trim())
      .filter(Boolean);
  }

  function updateTypeSummary() {
    const selected = getSelectedTypes();
    const summary = el('typeSummary');
    if (!summary) return;

    if (selected.length === 0) {
      summary.textContent = 'Tidak ada yang dipilih (semua scan akan ditolak sebagai salah tipe).';
      return;
    }

    summary.textContent = `Aktif: ${selected.join(', ')}`;
  }

  function persistTypeSelection() {
    try {
      localStorage.setItem(storageKeyForTypes(), JSON.stringify(getSelectedTypes()));
    } catch (_) {}
  }

  function readPersistedTypes() {
    try {
      const raw = localStorage.getItem(storageKeyForTypes());
      const parsed = JSON.parse(raw || '[]');
      return Array.isArray(parsed) ? parsed.map(String) : [];
    } catch (_) {
      return [];
    }
  }

  function renderTypeFilters() {
    const wrap = el('typeFilters');
    const empty = el('typeEmpty');
    if (!wrap) return;

    const eventId = String(el('event_id')?.value || '');
    const types = (EVENT_TICKET_TYPES[eventId] || []).map((v) => String(v).trim()).filter(Boolean);
    const persisted = readPersistedTypes();

    wrap.innerHTML = '';
    if (types.length === 0) {
      if (empty) empty.classList.remove('hidden');
      updateTypeSummary();
      return;
    }
    if (empty) empty.classList.add('hidden');

    const selectedSet = new Set((persisted.length ? persisted : types).map((v) => v.toUpperCase()));

    types.forEach((type) => {
      const id = `type_${type.replace(/[^a-zA-Z0-9_-]/g, '_')}`;
      const checked = selectedSet.has(type.toUpperCase()) ? 'checked' : '';

      wrap.insertAdjacentHTML('beforeend', `
        <label for="${id}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-bold
                                   hover:bg-slate-50 transition dark:border-slate-800 dark:bg-slate-950/30 dark:text-slate-100 dark:hover:bg-slate-950/50">
          <input id="${id}" type="checkbox" value="${type}" class="ticket-type-checkbox h-3.5 w-3.5 rounded border-slate-300 text-slate-900 focus:ring-slate-400" ${checked}>
          <span>${type}</span>
        </label>
      `);
    });

    document.querySelectorAll('.ticket-type-checkbox').forEach((node) => {
      node.addEventListener('change', () => {
        persistTypeSelection();
        updateTypeSummary();
      });
    });

    updateTypeSummary();
  }

  // -----------------------------
  // UI Result helper
  // -----------------------------
  function setPill(text, cls) {
    const pill = el('pill');
    if (!pill) return;
    pill.textContent = text || 'READY';
    pill.className = "text-[11px] font-black uppercase tracking-wider px-3 py-1.5 rounded-full border";
    pill.classList.add(...(cls || '').split(' ').filter(Boolean));
  }

  function setIcon(type){
    const iconBox = el('iconBox');
    const svg = el('iconReady');
    if(!iconBox || !svg) return;

    svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />';
    iconBox.className = "h-11 w-11 rounded-2xl grid place-items-center border transition";

    if (type === 'VALID') {
      iconBox.classList.add('bg-emerald-50','text-emerald-700','border-emerald-200','dark:bg-emerald-950/30','dark:text-emerald-200','dark:border-emerald-900/60');
      svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />';
    } else if (type === 'WARNING') {
      iconBox.classList.add('bg-amber-50','text-amber-700','border-amber-200','dark:bg-amber-950/30','dark:text-amber-200','dark:border-amber-900/60');
      svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M6.938 4h10.124c1.54 0 2.502 1.667 1.732 3L13.732 17c-.77 1.333-2.694 1.333-3.464 0L5.206 7c-.77-1.333.192-3 1.732-3z" />';
    } else if (type === 'DUPLICATE' || type === 'DUP') {
      iconBox.classList.add('bg-amber-50','text-amber-700','border-amber-200','dark:bg-amber-950/30','dark:text-amber-200','dark:border-amber-900/60');
      svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h8M8 16h8M8 8h8" />';
    } else if (type === 'INVALID') {
      iconBox.classList.add('bg-rose-50','text-rose-700','border-rose-200','dark:bg-rose-950/30','dark:text-rose-200','dark:border-rose-900/60');
      svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />';
    } else if (type === 'INVALID_TYPE') {
      iconBox.classList.add('bg-orange-50','text-orange-700','border-orange-200','dark:bg-orange-950/30','dark:text-orange-200','dark:border-orange-900/60');
      svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01M6.938 4h10.124c1.54 0 2.502 1.667 1.732 3L13.732 17c-.77 1.333-2.694 1.333-3.464 0L5.206 7c-.77-1.333.192-3 1.732-3z" />';
    } else if (type === 'CHECKING') {
      iconBox.classList.add('bg-sky-50','text-sky-700','border-sky-200','dark:bg-sky-950/30','dark:text-sky-200','dark:border-sky-900/60');
      svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v6h6M20 20v-6h-6M5 19a9 9 0 0114-7M19 5a9 9 0 00-14 7" />';
    } else {
      iconBox.classList.add('bg-slate-100','text-slate-900','border-slate-200','dark:bg-slate-950/40','dark:text-slate-100','dark:border-slate-800');
    }
  }

  function setBox(result, message){
    const box = el('resultBox');
    const r = (result || '').toUpperCase();

    box.className = "p-5 transition";

    if (r === 'VALID') {
      box.classList.add('bg-emerald-50/70','dark:bg-emerald-950/20');
      setPill('VALID', 'bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-950/30 dark:border-emerald-900/60 dark:text-emerald-200');
    } else if (r === 'WARNING') {
      box.classList.add('bg-amber-50/70','dark:bg-amber-950/20');
      setPill('WARNING', 'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-950/30 dark:border-amber-900/60 dark:text-amber-200');
    } else if (r === 'DUPLICATE' || r === 'DUP') {
      box.classList.add('bg-amber-50/70','dark:bg-amber-950/20');
      setPill('DUP', 'bg-amber-50 border-amber-200 text-amber-700 dark:bg-amber-950/30 dark:border-amber-900/60 dark:text-amber-200');
    } else if (r === 'INVALID') {
      box.classList.add('bg-rose-50/70','dark:bg-rose-950/20');
      setPill('INVALID', 'bg-rose-50 border-rose-200 text-rose-700 dark:bg-rose-950/30 dark:border-rose-900/60 dark:text-rose-200');
    } else if (r === 'INVALID_TYPE') {
      box.classList.add('bg-orange-50/70','dark:bg-orange-950/20');
      setPill('WRONG TYPE', 'bg-orange-50 border-orange-200 text-orange-700 dark:bg-orange-950/30 dark:border-orange-900/60 dark:text-orange-200');
    } else if (r === 'CHECKING') {
      box.classList.add('bg-sky-50/70','dark:bg-sky-950/20');
      setPill('CHECK', 'bg-sky-50 border-sky-200 text-sky-700 dark:bg-sky-950/30 dark:border-sky-900/60 dark:text-sky-200');
    } else {
      setPill('READY', 'bg-slate-50 border-slate-200 text-slate-700 dark:bg-slate-950/40 dark:border-slate-800 dark:text-slate-200');
    }

    setIcon(r || 'READY');
    el('result').textContent = r || 'READY';
    el('message').textContent = message || '';
    const selectedTypes = getSelectedTypes();
    const allowedLabel = selectedTypes.length ? selectedTypes.join(', ') : '—';
    const selectedEventLabel = el('event_id')?.selectedOptions?.[0]?.textContent?.trim() || '-';
    el('meta').textContent = `Event: ${selectedEventLabel} • Gate: ${el('gate_name')?.value || '-'} • Allowed: ${allowedLabel}`;
  }

  // -----------------------------
  // Submit scan
  // -----------------------------
  async function submitScan(code){
    code = (code || '').trim();
    if(!code) return;

    const hasTypeFilterOptions = document.querySelectorAll('.ticket-type-checkbox').length > 0;
    const payload = {
      event_id: Number(el('event_id').value),
      code: code,
      gate_name: el('gate_name').value || null,
      mode: 'in',
      allowed_types: hasTypeFilterOptions ? getSelectedTypes() : null
    };

    ensureAudio();
    setBox('CHECKING', 'Checking…');
    playTone('CHECKING');

    const res = await fetch("{{ route('scan.do') }}", {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify(payload)
    });

    const data = await res.json();

    setBox(data.result, data.message);
    playTone(data.result);
    vibrate(data.result);

    pushRecent(`[${String(data.result || '').toUpperCase()}] ${code} (${new Date().toLocaleTimeString('id-ID')})`);

    el('code').value = '';
    el('code').focus();
  }

  // hardware scanner ENTER submit
  el('code')?.addEventListener('keydown', (e) => {
    if(e.key === 'Enter'){
      e.preventDefault();
      submitScan(el('code').value);
    }
  });

  // global scanner capture:
  // many hardware scanners behave like keyboard-wedge and type very fast.
  // this buffer lets scan work even when #code is not focused or scanner doesn't send Enter.
  let scanBuffer = '';
  let scanTimer = null;
  const SCAN_IDLE_MS = 90;

  function clearScanBuffer() {
    scanBuffer = '';
    if (scanTimer) {
      clearTimeout(scanTimer);
      scanTimer = null;
    }
  }

  function scheduleBufferedSubmit() {
    if (scanTimer) clearTimeout(scanTimer);
    scanTimer = setTimeout(() => {
      const candidate = (scanBuffer || '').trim();
      if (candidate.length >= 6) submitScan(candidate);
      clearScanBuffer();
    }, SCAN_IDLE_MS);
  }

  document.addEventListener('keydown', (e) => {
    // ignore shortcuts/composition
    if (e.isComposing || e.ctrlKey || e.altKey || e.metaKey) return;

    const active = document.activeElement;
    const isEditable =
      active &&
      (active.tagName === 'INPUT' ||
        active.tagName === 'TEXTAREA' ||
        active.isContentEditable);

    // don't hijack normal typing when user is typing in any editable field
    if (isEditable) return;

    if (e.key === 'Enter') {
      if (scanBuffer.trim()) {
        e.preventDefault();
        submitScan(scanBuffer.trim());
        clearScanBuffer();
      }
      return;
    }

    // printable key only
    if (e.key.length === 1) {
      scanBuffer += e.key;
      scheduleBufferedSubmit();
    }
  }, true);

  el('btnFocus')?.addEventListener('click', () => el('code')?.focus());
  el('event_id')?.addEventListener('change', () => {
    renderTypeFilters();
    setBox(el('result')?.textContent, el('message')?.textContent);
  });
  el('gate_name')?.addEventListener('input', () => setBox(el('result')?.textContent, el('message')?.textContent));

  el('btnTypeAll')?.addEventListener('click', () => {
    document.querySelectorAll('.ticket-type-checkbox').forEach((node) => { node.checked = true; });
    persistTypeSelection();
    updateTypeSummary();
    setBox(el('result')?.textContent, el('message')?.textContent);
  });

  el('btnTypeNone')?.addEventListener('click', () => {
    document.querySelectorAll('.ticket-type-checkbox').forEach((node) => { node.checked = false; });
    persistTypeSelection();
    updateTypeSummary();
    setBox(el('result')?.textContent, el('message')?.textContent);
  });

  // -----------------------------
  // Fullscreen toggle
  // -----------------------------
  function isFullscreen() {
    return !!(document.fullscreenElement || document.webkitFullscreenElement);
  }
  function updateFsBtn() {
    const b = el('btnFullscreen');
    if (!b) return;
    b.innerHTML = isFullscreen()
      ? `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M3 3h5a1 1 0 110 2H5v3a1 1 0 11-2 0V3zm12 0a2 2 0 012 2v5a1 1 0 11-2 0V5h-3a1 1 0 110-2h5zM3 12a1 1 0 012 0v3h3a1 1 0 110 2H5a2 2 0 01-2-2v-5zm14 0a1 1 0 10-2 0v3h-3a1 1 0 100 2h3a2 2 0 002-2v-5z"/></svg> Exit Fullscreen`
      : `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M3 3h5a1 1 0 110 2H5v3a1 1 0 11-2 0V3zm12 0a2 2 0 012 2v5a1 1 0 11-2 0V5h-3a1 1 0 110-2h5zM3 12a1 1 0 012 0v3h3a1 1 0 110 2H5a2 2 0 01-2-2v-5zm14 0a1 1 0 10-2 0v3h-3a1 1 0 100 2h3a2 2 0 002-2v-5z"/></svg> Fullscreen`;
  }
  async function toggleFullscreen() {
    try {
      ensureAudio();
      const root = document.documentElement;
      if (!isFullscreen()) {
        if (root.requestFullscreen) await root.requestFullscreen();
        else if (root.webkitRequestFullscreen) await root.webkitRequestFullscreen();
      } else {
        if (document.exitFullscreen) await document.exitFullscreen();
        else if (document.webkitExitFullscreen) await document.webkitExitFullscreen();
      }
    } catch (e) {
      console.error('Fullscreen error:', e);
    } finally {
      updateFsBtn();
      el('code')?.focus();
    }
  }
  el('btnFullscreen')?.addEventListener('click', toggleFullscreen);
  document.addEventListener('fullscreenchange', updateFsBtn);
  document.addEventListener('webkitfullscreenchange', updateFsBtn);

  // -----------------------------
  // Camera Scanner
  // -----------------------------
  let qrScanner = null;
  let cameraRunning = false;
  let cameraHidden = false;
  let desiredFacingMode = "environment"; // back camera
  let lastDecodeAt = 0;

  // scan line animation (simple + smooth)
  let scanAnim = null;
  function startScanLine() {
    const line = el('scanLine');
    if (!line) return;
    let y = 24;
    let dir = 1;
    if (scanAnim) cancelAnimationFrame(scanAnim);

    const step = () => {
      // only animate when camera live
      if (!cameraRunning) {
        line.style.opacity = '0.35';
      } else {
        line.style.opacity = '0.90';
      }
      y += dir * 1.6;
      if (y > 260) dir = -1;
      if (y < 24) dir = 1;
      line.style.transform = `translateY(${y}px)`;
      scanAnim = requestAnimationFrame(step);
    };
    step();
  }

  function setCamState(text){
    const s = el('camState');
    if (s) s.textContent = text;
  }
  function setCamHint(text){
    const h = el('camHint');
    if (h) h.textContent = text;
  }
  function setCamFacingUI(){
    const pill = el('camFacingPill');
    if (!pill) return;
    pill.textContent = (desiredFacingMode === "environment") ? "BACK" : "FRONT";
  }

  function isSecureContextForCamera() {
    return window.isSecureContext || location.hostname === 'localhost' || location.hostname === '127.0.0.1';
  }

  async function stopCamera(){
    try {
      if (qrScanner && cameraRunning) {
        await qrScanner.stop();
        await qrScanner.clear();
      }
    } catch (e) {
      // ignore
    } finally {
      cameraRunning = false;
      setCamState('STOPPED');
      setCamFacingUI();
    }
  }

  async function startCamera(){
    if (cameraHidden) return;

    setCamFacingUI();

    if (!window.Html5Qrcode) {
      setCamState('ERROR');
      setCamHint('Library kamera belum ke-load. Cek koneksi/CDN.');
      return;
    }

    if (!isSecureContextForCamera()) {
      setCamState('BLOCKED');
      setCamHint('Kamera browser butuh HTTPS (atau localhost).');
      return;
    }

    if (cameraRunning) return;

    try {
      if (!qrScanner) qrScanner = new Html5Qrcode("qrReader");

      setCamState('STARTING');
      cameraRunning = true;

      const config = {
        fps: 14,
        qrbox: (w, h) => {
          const size = Math.floor(Math.min(w, h) * 0.70);
          return { width: size, height: size };
        },
        aspectRatio: 1.0,
        disableFlip: false,
        formatsToSupport: [
          Html5QrcodeSupportedFormats.QR_CODE,
          Html5QrcodeSupportedFormats.CODE_128,
          Html5QrcodeSupportedFormats.EAN_13,
          Html5QrcodeSupportedFormats.EAN_8,
          Html5QrcodeSupportedFormats.CODE_39,
        ],
      };

      await qrScanner.start(
        { facingMode: desiredFacingMode },
        config,
        async (decodedText) => {
          const now = Date.now();
          if (now - lastDecodeAt < 1200) return; // cooldown
          lastDecodeAt = now;

          setCamState('LOCK');
          setCamHint('Captured • Processing…');

          await stopCamera(); // stop cepat biar tidak double scan
          submitScan(String(decodedText).trim());

          // auto resume
          setTimeout(() => startCamera().catch(()=>{}), 900);
        },
        (_err) => {}
      );

      setCamState('LIVE');
      setCamHint('Scanning…');
    } catch (e) {
      cameraRunning = false;
      setCamState('ERROR');
      setCamHint('Tidak bisa akses kamera. Cek permission / HTTPS (iPhone).');
      console.error('Camera start error:', e);
    }
  }

  el('btnCamSwitch')?.addEventListener('click', async () => {
    desiredFacingMode = (desiredFacingMode === "environment") ? "user" : "environment";
    setCamFacingUI();
    setCamHint('Switching…');
    await stopCamera();
    await startCamera();
  });

  el('btnCamRetry')?.addEventListener('click', async () => {
    setCamHint('Retry…');
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
      setCamState('HIDDEN');
      setCamHint('Camera disembunyikan.');
      return;
    }

    if (stage) stage.classList.remove('hidden');
    if (btn) btn.textContent = 'Hide Camera';
    setCamHint('Retry…');
    await startCamera();
  }

  el('btnCamToggle')?.addEventListener('click', async () => {
    await setCameraHidden(!cameraHidden);
  });

  // Sound toggle click
  el('btnSound')?.addEventListener('click', () => {
    soundEnabled = !soundEnabled;
    saveSoundPref();
    if (soundEnabled) ensureAudio();
  });

  // -----------------------------
  // Init
  // -----------------------------
  document.addEventListener('DOMContentLoaded', () => {
    updateFsBtn();
    renderTypeFilters();
    setBox('READY', 'Scan ticket…');
    renderRecent();

    loadSoundPref();
    startScanLine();

    // fokus utama hardware scanner
    el('code')?.focus();

    // camera auto-start
    startCamera();
  });
</script>
@endsection
