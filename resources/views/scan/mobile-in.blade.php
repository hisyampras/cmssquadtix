@extends('layouts.app')

@section('content')
<div class="min-h-[calc(100vh-64px)] bg-slate-50 dark:bg-slate-950">
  <div class="max-w-xl mx-auto px-3 py-4 sm:px-4 sm:py-6 space-y-4">

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4 dark:border-slate-800 dark:bg-slate-900">
      <div class="flex items-center justify-between gap-3">
        <div>
          <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-slate-100">
            Scan Mobile (PDT)
            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full border text-[10px] font-black tracking-widest uppercase bg-emerald-50 border-emerald-200 text-emerald-700 dark:bg-emerald-950/30 dark:border-emerald-900/60 dark:text-emerald-200">IN</span>
          </h1>
          <p class="text-xs text-slate-500 mt-1 dark:text-slate-400">Optimized for handheld scanner workflow.</p>
        </div>
        <a href="{{ route('scan.index', ['events_id' => $eventId, 'gate' => $gate]) }}"
           class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 dark:border-slate-800 dark:bg-slate-950/40 dark:text-slate-200 dark:hover:bg-slate-950">
          <i class="fa-solid fa-desktop"></i>
          Desktop
        </a>
      </div>
    </div>

    <div id="resultCard" class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4 dark:border-slate-800 dark:bg-slate-900">
      <div class="flex items-center justify-between gap-2">
        <span id="resultPill" class="inline-flex items-center px-3 py-1 rounded-full border text-xs font-black tracking-wide bg-slate-100 border-slate-200 text-slate-700 dark:bg-slate-950/50 dark:border-slate-700 dark:text-slate-200">READY</span>
        <span id="resultTime" class="text-xs text-slate-500 dark:text-slate-400">-</span>
      </div>
      <div id="resultText" class="mt-3 text-3xl font-black tracking-tight text-slate-900 dark:text-slate-100">READY</div>
      <div id="resultMessage" class="mt-1 text-sm text-slate-600 dark:text-slate-300">Waiting for scan...</div>
      <div id="resultMeta" class="mt-2 text-xs text-slate-500 dark:text-slate-400">-</div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4 dark:border-slate-800 dark:bg-slate-900">
      <div class="flex items-center justify-between gap-2">
        <label for="scan_code" class="text-[11px] font-black uppercase tracking-wide text-slate-600 dark:text-slate-300">Scan Barcode</label>
        <button id="btnManualToggle" type="button" class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-950/50 dark:text-slate-200">
          Show Manual Input
        </button>
      </div>

      <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
        Input manual disembunyikan. PDT scanner tetap aktif.
      </p>

      <input id="scan_code"
        class="fixed left-0 top-0 h-8 w-8 opacity-0 pointer-events-none"
        placeholder="Scan here"
        inputmode="text"
        spellcheck="false"
        autocomplete="off"
        autocapitalize="characters"
        @disabled($events->isEmpty())>

      <div id="manualInputControls" class="hidden">
        <div class="mt-3 grid grid-cols-2 gap-2">
          <button id="btnFocus" type="button" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-950/50 dark:text-slate-200">Focus Input</button>
          <button id="btnClear" type="button" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-950/50 dark:text-slate-200">Clear</button>
        </div>
      </div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4 dark:border-slate-800 dark:bg-slate-900">
      <div class="flex items-center justify-between gap-2">
        <div class="text-xs font-black uppercase tracking-wide text-slate-600 dark:text-slate-300">Recent scans</div>
        <button id="btnClearRecent" type="button" class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-950/50 dark:text-slate-200">Clear</button>
      </div>
      <div id="recentList" class="mt-2 text-sm text-slate-700 space-y-1 dark:text-slate-200"></div>
    </div>

    <div class="rounded-2xl border border-slate-200 bg-white shadow-sm p-4 space-y-3 dark:border-slate-800 dark:bg-slate-900">
      <div>
        <label class="text-[11px] font-black uppercase tracking-wide text-slate-600 dark:text-slate-300">Event</label>
        <select id="events_id"
          class="mt-1.5 w-full px-3 py-3 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-900 focus:outline-none focus:ring-2 focus:ring-slate-300 dark:border-slate-700 dark:bg-slate-950/40 dark:text-slate-100 dark:focus:ring-slate-700"
          @disabled($events->isEmpty())>
          @forelse($events as $e)
            <option value="{{ $e->id }}" @selected($e->id == $eventId)>{{ $e->event_code ?? '-' }} - {{ $e->name }}</option>
          @empty
            <option value="">No active event</option>
          @endforelse
        </select>
      </div>

      <div>
        <label class="text-[11px] font-black uppercase tracking-wide text-slate-600 dark:text-slate-300">Gate</label>
        <input id="gate_name" value="{{ $gate }}"
          class="mt-1.5 w-full px-3 py-3 rounded-xl border border-slate-200 bg-white text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-300 dark:border-slate-700 dark:bg-slate-950/40 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-slate-700"
          placeholder="GATE A">
      </div>

      <div class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-950/40">
        <div class="flex items-center justify-between gap-2">
          <div class="text-[11px] font-black uppercase tracking-wide text-slate-600 dark:text-slate-300">Category Filter</div>
          <div class="flex items-center gap-2">
            <button id="btnTypeAll" type="button" class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">All</button>
            <button id="btnTypeNone" type="button" class="px-2.5 py-1 rounded-lg border border-slate-200 bg-white text-[11px] font-bold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">Clear</button>
          </div>
        </div>
        <p id="typeSummary" class="text-xs text-slate-500 mt-1.5 dark:text-slate-400">-</p>
        <div id="typeFilters" class="mt-2.5 flex flex-wrap gap-2"></div>
      </div>
    </div>

  </div>
</div>

<script>
  const el = (id) => document.getElementById(id);
  const EVENT_TICKET_TYPES = @json($eventTicketTypesByEventId ?? []);
  const RECENT_LIMIT = 8;
  const recent = [];

  let audioCtx = null;
  const SOUND_KEY = 'scan_mobile_sound_enabled_v1';
  let soundEnabled = true;

  function focusScannerInput(allowKeyboard = false) {
    const input = el('scan_code');
    if (!input || input.disabled) return;
    input.focus({ preventScroll: true });
  }

  function loadSoundPref() {
    const v = localStorage.getItem(SOUND_KEY);
    soundEnabled = (v === null) ? true : v === '1';
  }

  function ensureAudio() {
    if (!soundEnabled) return;
    if (!audioCtx) {
      const AC = window.AudioContext || window.webkitAudioContext;
      if (AC) audioCtx = new AC();
    }
    if (audioCtx && audioCtx.state === 'suspended') audioCtx.resume().catch(() => {});
  }

  function beep(freq, duration, type, gainVal) {
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
    const r = String(result || '').toUpperCase();
    if (r === 'VALID') {
      beep(980, 0.08, 'triangle', 0.07);
      setTimeout(() => beep(1320, 0.08, 'triangle', 0.07), 90);
    } else if (r === 'WARNING') {
      beep(520, 0.12, 'sine', 0.06);
    } else if (r === 'DUPLICATE') {
      beep(640, 0.12, 'sine', 0.06);
    } else {
      beep(240, 0.16, 'sawtooth', 0.05);
    }
  }

  function vibrate(result) {
    if (!navigator.vibrate) return;
    const r = String(result || '').toUpperCase();
    if (r === 'VALID') navigator.vibrate([35, 35, 35]);
    else if (r === 'WARNING') navigator.vibrate([60, 40, 60]);
    else if (r === 'DUPLICATE') navigator.vibrate([70]);
    else navigator.vibrate([120, 60, 120]);
  }

  function addRecent(line) {
    recent.unshift(line);
    if (recent.length > RECENT_LIMIT) recent.length = RECENT_LIMIT;
    renderRecent();
  }

  function renderRecent() {
    const node = el('recentList');
    if (!node) return;
    node.innerHTML = recent.length
      ? recent.map((x) => `<div class="truncate">${x}</div>`).join('')
      : '<div class="text-slate-400 dark:text-slate-500">No scans yet.</div>';
  }

  function setResult(result, message, ticket = null) {
    const r = String(result || 'READY').toUpperCase();
    const card = el('resultCard');
    const pill = el('resultPill');

    card.className = 'rounded-2xl border shadow-sm p-4';
    pill.className = 'inline-flex items-center px-3 py-1 rounded-full border text-xs font-black tracking-wide';

    if (r === 'VALID') {
      card.classList.add('border-emerald-200', 'bg-emerald-50');
      pill.classList.add('bg-emerald-100', 'border-emerald-200', 'text-emerald-700');
    } else if (r === 'WARNING') {
      card.classList.add('border-amber-200', 'bg-amber-50');
      pill.classList.add('bg-amber-100', 'border-amber-200', 'text-amber-700');
    } else if (r === 'DUPLICATE') {
      card.classList.add('border-amber-200', 'bg-amber-50');
      pill.classList.add('bg-amber-100', 'border-amber-200', 'text-amber-700');
    } else if (r === 'INVALID' || r === 'INVALID_TYPE') {
      card.classList.add('border-rose-200', 'bg-rose-50');
      pill.classList.add('bg-rose-100', 'border-rose-200', 'text-rose-700');
    } else if (r === 'CHECKING') {
      card.classList.add('border-sky-200', 'bg-sky-50');
      pill.classList.add('bg-sky-100', 'border-sky-200', 'text-sky-700');
    } else {
      card.classList.add('border-slate-200', 'bg-white', 'dark:border-slate-800', 'dark:bg-slate-900');
      pill.classList.add('bg-slate-100', 'border-slate-200', 'text-slate-700', 'dark:bg-slate-950/50', 'dark:border-slate-700', 'dark:text-slate-200');
    }

    pill.textContent = r;
    el('resultText').textContent = r;
    el('resultMessage').textContent = message || '-';
    el('resultTime').textContent = new Date().toLocaleTimeString('id-ID');

    const type = ticket?.category ? `Type: ${ticket.category}` : 'Type: -';
    const code = ticket?.code ? `Code: ${ticket.code}` : 'Code: -';
    el('resultMeta').textContent = `${code} | ${type}`;
  }

  function storageKeyForTypes() {
    const eventId = String(el('events_id')?.value || '');
    return `scan_mobile:allowed_types:${eventId}`;
  }

  function getSelectedTypes() {
    return Array.from(document.querySelectorAll('.ticket-type-checkbox:checked'))
      .map((node) => String(node.value || '').trim())
      .filter(Boolean);
  }

  function persistTypes() {
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

  function updateTypeSummary() {
    const selected = getSelectedTypes();
    el('typeSummary').textContent = selected.length
      ? `Active: ${selected.join(', ')}`
      : 'No type selected (all scans will be rejected by type filter).';
  }

  function renderTypeFilters() {
    const wrap = el('typeFilters');
    if (!wrap) return;

    const eventId = String(el('events_id')?.value || '');
    const types = (EVENT_TICKET_TYPES[eventId] || []).map((v) => String(v).trim()).filter(Boolean);
    const persisted = readPersistedTypes();

    wrap.innerHTML = '';
    if (types.length === 0) {
      el('typeSummary').textContent = 'No category data for this event.';
      return;
    }

    const selectedSet = new Set((persisted.length ? persisted : types).map((v) => v.toUpperCase()));

    types.forEach((type) => {
      const id = `mtype_${type.replace(/[^a-zA-Z0-9_-]/g, '_')}`;
      const checked = selectedSet.has(type.toUpperCase()) ? 'checked' : '';
      wrap.insertAdjacentHTML('beforeend', `
        <label for="${id}" class="inline-flex items-center gap-2 px-2.5 py-1.5 rounded-lg border border-slate-200 bg-white text-xs font-semibold text-slate-700 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-200">
          <input id="${id}" type="checkbox" value="${type}" class="ticket-type-checkbox h-3.5 w-3.5 rounded border-slate-300" ${checked}>
          <span>${type}</span>
        </label>
      `);
    });

    document.querySelectorAll('.ticket-type-checkbox').forEach((node) => {
      node.addEventListener('change', () => {
        persistTypes();
        updateTypeSummary();
      });
    });

    updateTypeSummary();
  }

  async function submitScan(rawCode) {
    const code = String(rawCode || '').trim();
    if (!code) return;

    const eventId = Number(el('events_id')?.value || 0);
    if (!eventId) {
      setResult('INVALID', 'No active event selected.');
      return;
    }

    const hasTypeFilterOptions = document.querySelectorAll('.ticket-type-checkbox').length > 0;
    const payload = {
      events_id: eventId,
      code,
      gate_name: el('gate_name')?.value || null,
      mode: 'in',
      allowed_types: hasTypeFilterOptions ? getSelectedTypes() : null,
    };

    setResult('CHECKING', 'Checking ticket...');

    try {
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
      setResult(data.result, data.message, data.ticket || null);
      playTone(data.result);
      vibrate(data.result);

      const result = String(data.result || '-').toUpperCase();
      addRecent(`[${result}] ${code} (${new Date().toLocaleTimeString('id-ID')})`);
    } catch (err) {
      setResult('INVALID', 'Request failed. Check network/server.');
      addRecent(`[ERROR] ${code}`);
    } finally {
      el('scan_code').value = '';
      focusScannerInput(false);
    }
  }

  el('scan_code')?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      submitScan(el('scan_code').value);
    }
  });

  let inputIdleTimer = null;
  el('scan_code')?.addEventListener('input', () => {
    if (inputIdleTimer) clearTimeout(inputIdleTimer);
    inputIdleTimer = setTimeout(() => {
      const v = String(el('scan_code')?.value || '').trim();
      if (v.length >= 6) submitScan(v);
    }, 120);
  });

  // Support scanner keyboard wedge when input loses focus.
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
    if (e.isComposing || e.ctrlKey || e.altKey || e.metaKey) return;

    const active = document.activeElement;
    const isEditable = active && (
      active.tagName === 'INPUT' ||
      active.tagName === 'TEXTAREA' ||
      active.isContentEditable
    );

    if (isEditable) return;

    if (e.key === 'Enter') {
      if (scanBuffer.trim()) {
        e.preventDefault();
        submitScan(scanBuffer.trim());
        clearScanBuffer();
      }
      return;
    }

    if (e.key.length === 1) {
      scanBuffer += e.key;
      scheduleBufferedSubmit();
    }
  }, true);

  el('btnFocus')?.addEventListener('click', () => focusScannerInput(true));
  el('btnClear')?.addEventListener('click', () => {
    el('scan_code').value = '';
    setResult('READY', 'Waiting for scan...');
    focusScannerInput(false);
  });
  el('btnClearRecent')?.addEventListener('click', () => {
    recent.length = 0;
    renderRecent();
  });

  el('events_id')?.addEventListener('change', () => {
    renderTypeFilters();
    el('scan_code')?.focus();
  });

  el('btnTypeAll')?.addEventListener('click', () => {
    document.querySelectorAll('.ticket-type-checkbox').forEach((node) => {
      node.checked = true;
    });
    persistTypes();
    updateTypeSummary();
  });

  el('btnTypeNone')?.addEventListener('click', () => {
    document.querySelectorAll('.ticket-type-checkbox').forEach((node) => {
      node.checked = false;
    });
    persistTypes();
    updateTypeSummary();
  });

  function setManualInputVisible(visible) {
    const hiddenInput = el('scan_code');
    const controls = el('manualInputControls');
    const toggleBtn = el('btnManualToggle');

    if (!hiddenInput || !controls || !toggleBtn) return;

    if (visible) {
      controls.classList.remove('hidden');
      toggleBtn.textContent = 'Hide Manual Input';
      hiddenInput.className = 'mt-2 w-full px-4 py-4 rounded-xl border border-slate-300 bg-white text-xl font-mono font-semibold tracking-wide text-slate-900 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-300 dark:border-slate-700 dark:bg-slate-950/40 dark:text-slate-100 dark:placeholder:text-slate-500 dark:focus:ring-blue-700';
      hiddenInput.placeholder = 'Scan here';
      focusScannerInput(true);
      return;
    }

    controls.classList.add('hidden');
    toggleBtn.textContent = 'Show Manual Input';
    hiddenInput.className = 'fixed left-0 top-0 h-8 w-8 opacity-0 pointer-events-none';
    focusScannerInput(false);
  }

  el('btnManualToggle')?.addEventListener('click', () => {
    const currentlyHidden = el('manualInputControls')?.classList.contains('hidden');
    setManualInputVisible(currentlyHidden);
  });

  document.addEventListener('DOMContentLoaded', () => {
    loadSoundPref();
    renderTypeFilters();
    renderRecent();
    setResult('READY', 'Waiting for scan...');
    setManualInputVisible(false);
    focusScannerInput(false);
    setTimeout(() => focusScannerInput(false), 250);
    ensureAudio();
  });

  document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
      focusScannerInput(false);
    }
  });

  setInterval(() => {
    const isManualHidden = el('manualInputControls')?.classList.contains('hidden');
    const input = el('scan_code');
    if (isManualHidden && input && document.activeElement !== input) {
      focusScannerInput(false);
    }
  }, 1500);
</script>
@endsection
