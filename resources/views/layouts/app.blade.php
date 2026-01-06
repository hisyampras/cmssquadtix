{{-- resources/views/layouts/app.blade.php --}}
<!doctype html>
<html lang="id" class="h-full">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>{{ $title ?? 'SquadTix' }}</title>
  <link rel="icon" type="image/png" href="{{ asset('images/squadtix-logo.png') }}" />

  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
    crossorigin="anonymous"
    referrerpolicy="no-referrer" />

  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <script>
    (() => {
      try {
        const pref = localStorage.getItem('theme');
        const shouldDark = pref ? pref === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
        if (shouldDark) document.documentElement.classList.add('dark');
      } catch (_) {}
    })();
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            brand: {
              blue: '#004AAD',
              cyan: '#00A0D2',
              dark: '#0F172A'
            }
          },
          fontFamily: {
            sans: ['Inter', 'ui-sans-serif', 'system-ui']
          },
        }
      }
    }
  </script>

  <style>
    :root {
      --bg: #f3f4f6;
      --text-main: #0f172a;
      --text-muted: #475569;
      --panel: #ffffff;
      --panel-text: #0f172a;
      --border: #e2e8f0;
      --sidebar-bg: rgba(255, 255, 255, 0.94);
      --sidebar-text: #1e293b;
      --sidebar-hover: rgba(15, 23, 42, 0.06);
      --backdrop: rgba(15, 23, 42, 0.45);
    }

    .dark {
      --bg: #020617;
      --text-main: #e2e8f0;
      --text-muted: #94a3b8;
      --panel: #020617;
      --panel-text: #f1f5f9;
      --border: #1f2937;
      --sidebar-bg: rgba(15, 23, 42, 0.96);
      --sidebar-text: #e5e7eb;
      --sidebar-hover: rgba(30, 64, 175, 0.45);
      --backdrop: rgba(2, 6, 23, 0.65);
    }

    body {
      background: linear-gradient(135deg, #e5f0ff 0%, #f9fafb 50%, #e0ebff 100%);
      background-repeat: no-repeat;
      background-size: cover;
      background-attachment: fixed;

      color: var(--text-main);
      font-family: Inter, ui-sans-serif, system-ui;
      min-height: 100vh;
      margin: 0;
      overflow-x: hidden;
    }

    .dark body {
      background: linear-gradient(135deg, #020617 0%, #020617 40%, #020617 100%);
      background-repeat: no-repeat;
      background-size: cover;
      background-attachment: fixed;
    }

    .transition-theme {
      transition: background-color .25s ease, color .25s ease, border-color .25s ease;
    }

    @keyframes spin-slow {
      from { transform: rotate(0) }
      to { transform: rotate(360deg) }
    }

    .animate-spin-slow {
      animation: spin-slow 8s linear infinite;
    }

    .nav-item {
      position: relative;
    }

    .nav-item.active::before {
      content: "";
      position: absolute;
      left: 0;
      top: .4rem;
      bottom: .4rem;
      width: 3px;
      background: #004AAD;
      border-radius: 4px;
    }

    .nav-item:hover::before {
      content: "";
      position: absolute;
      left: 0;
      top: .6rem;
      bottom: .6rem;
      width: 2px;
      background: rgba(0, 74, 173, .35);
      border-radius: 4px;
    }

    @media (max-width: 360px) {
      #aside { width: 16rem; }
    }

    [x-cloak] { display: none !important; }

    .sidebar-glass {
      backdrop-filter: blur(18px);
      -webkit-backdrop-filter: blur(18px);
    }
  </style>
</head>

@php
  $impersonating = session()->has('impersonator_id');
  $navItemBase = 'nav-item flex items-center gap-2 pl-3 pr-3 py-2 rounded-md transition-colors font-medium whitespace-nowrap overflow-hidden text-ellipsis hover:bg-[var(--sidebar-hover)]';
@endphp

@if($impersonating)
  <div class="bg-amber-500/90 text-white px-4 md:px-6 py-2 flex justify-between items-center text-xs md:text-sm font-medium">
    <div>🕵️ Anda sedang impersonate sebagai <strong>{{ Auth::user()->name }}</strong></div>
    <form method="POST" action="{{ route('admin.impersonate.stop') }}">
      @csrf
      <button type="submit" class="bg-white/20 hover:bg-white/30 text-white font-semibold px-2.5 md:px-3 py-1.5 rounded-lg transition">
        🔙 Kembali ke Akun Admin
      </button>
    </form>
  </div>
@endif

<body
  class="h-full antialiased"
  x-data="{
    sidebarMini: localStorage.getItem('asm:sidebarMini') === '1',
    toggleSidebarMini() {
      this.sidebarMini = !this.sidebarMini;
      try { localStorage.setItem('asm:sidebarMini', this.sidebarMini ? '1' : '0'); } catch (_) {}
    }
  }">

  <div class="w-full bg-transparent">
    <div class="flex w-full min-h-screen lg:gap-4 lg:px-4 lg:py-4">

      <!-- BACKDROP MOBILE -->
      <div id="asideBackdrop"
        class="fixed inset-0 bg-[var(--backdrop)] z-40 opacity-0 pointer-events-none transition-opacity lg:hidden"></div>

      <!-- SIDEBAR -->
      <aside id="aside"
        class="
          border-r border-[var(--border)] bg-[var(--sidebar-bg)] text-[var(--sidebar-text)]
          flex flex-col transition-transform duration-200 z-50 w-72
          fixed inset-y-0 left-0 transform -translate-x-full lg:translate-x-0
          lg:sticky lg:top-0 lg:h-screen
          lg:flex-shrink-0
          lg:sidebar-glass lg:rounded-2xl
          lg:shadow-[0_22px_60px_rgba(15,23,42,0.45)]
          lg:border lg:border-[var(--border)]
        "
        :class="sidebarMini ? 'lg:w-20' : 'lg:w-72'">

        <!-- LOGO -->
        <div class="h-16 flex items-center justify-center border-b border-[var(--border)]">
          <img
            src="{{ asset('images/squadtix-logo.png') }}"
            alt="Logo SquadTix"
            class="h-9 w-auto object-contain" />
        </div>

        <!-- NAV WRAPPER -->
        <nav class="flex-1 flex flex-col min-h-0 px-3 py-4 space-y-4">

          <!-- TOP: DASHBOARD + SEARCH -->
          <div class="space-y-3">

            {{-- DASHBOARD BUTTON --}}
            <div data-nav-section>
              <a href="{{ route('dashboard.index') }}"
                data-nav-search-item
                data-label="dashboard overview beranda home scan"
                class="flex items-center gap-3 px-3 py-2.5 rounded-xl border text-sm transition-colors
                {{ request()->routeIs('dashboard.*')
                    ? 'bg-brand-blue/10 text-brand-blue border-brand-blue/60 shadow-sm'
                    : 'bg-[var(--panel)] text-[var(--sidebar-text)] border-[var(--border)] hover:bg-[var(--sidebar-hover)]' }}">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded-2xl
                     {{ request()->routeIs('dashboard.*') ? 'bg-brand-blue text-white' : 'bg-[var(--sidebar-hover)] text-brand-blue' }}">
                  <i class="fa-solid fa-gauge-high text-sm"></i>
                </span>
                <div class="flex flex-col leading-tight" x-show="!sidebarMini" x-transition>
                  <span class="text-[11px] uppercase tracking-wide text-[var(--text-muted)]">Overview</span>
                  <span class="text-sm font-semibold">Dashboard</span>
                </div>
              </a>
            </div>

            <!-- SEARCH SIDEBAR -->
            <div x-show="!sidebarMini" x-transition>
              <div class="relative">
                <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none text-[var(--text-muted)]">
                  <i class="fa-solid fa-magnifying-glass text-xs"></i>
                </span>
                <input
                  id="sidebarSearch"
                  type="text"
                  autocomplete="off"
                  placeholder="Cari menu..."
                  class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)]
                         pl-8 pr-3 py-1.5 text-xs text-[var(--sidebar-text)]
                         placeholder:text-[var(--text-muted)]
                         focus:outline-none focus:ring-1 focus:ring-brand-blue/60" />
              </div>
            </div>
          </div>

          <!-- MIDDLE: MENU -->
          <div class="flex-1 min-h-0 overflow-y-auto pr-1 space-y-5">

            {{-- SCAN SYSTEM MENU --}}
            <section class="rounded-xl border border-[var(--border)] overflow-hidden transition-theme" data-nav-section>
              <button type="button"
                class="flex items-center justify-between px-3 h-11 bg-[var(--panel)] w-full"
                data-collapse-btn data-key="nav:scan" aria-expanded="true">
                <span class="text-xs font-semibold uppercase tracking-wide text-[var(--text-muted)]"
                  x-show="!sidebarMini" x-transition>
                  Scan System
                </span>
                <span class="text-xs font-semibold text-[var(--text-muted)]" x-show="sidebarMini" x-cloak>
                  <i class="fa-solid fa-qrcode"></i>
                </span>
                <svg class="h-4 w-4 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                </svg>
              </button>

              <div class="p-2 space-y-1 bg-[var(--sidebar-bg)] border-t border-[var(--border)]"
                data-collapse-panel data-key="nav:scan">

                <a href="{{ route('scan.index') }}"
                  data-nav-search-item
                  data-label="scan gate scanner checkin"
                  class="{{ $navItemBase }} {{ request()->routeIs('scan.*') ? 'active bg-[var(--sidebar-hover)] text-brand-blue font-semibold' : '' }}">
                  <span class="w-6 flex justify-center text-[var(--text-muted)]">
                    <i class="fa-solid fa-qrcode text-sm"></i>
                  </span>
                  <span class="text-sm" x-show="!sidebarMini" x-transition>Scan Gate</span>
                </a>

                <a href="{{ route('events.index') }}"
                  data-nav-search-item
                  data-label="events event acara master"
                  class="{{ $navItemBase }} {{ request()->routeIs('events.*') ? 'active bg-[var(--sidebar-hover)] text-brand-blue font-semibold' : '' }}">
                  <span class="w-6 flex justify-center text-[var(--text-muted)]">
                    <i class="fa-regular fa-calendar-days text-sm"></i>
                  </span>
                  <span class="text-sm" x-show="!sidebarMini" x-transition>Events</span>
                </a>
              </div>
            </section>

          </div> {{-- /MIDDLE scroll area --}}

          {{-- BOTTOM: ADMIN --}}
          @auth
          @if (Auth::user()->isAdmin())
            <div class="pt-3 mt-3">
              <section class="rounded-xl border border-[var(--border)] overflow-hidden transition-theme" data-nav-section>
                <button type="button"
                  class="flex items-center justify-between px-3 h-11 bg-[var(--panel)] w-full"
                  data-collapse-btn data-key="nav:admin" aria-expanded="true">
                  <span class="text-xs font-semibold uppercase tracking-wide text-[var(--text-muted)]"
                    x-show="!sidebarMini" x-transition>
                    Administration
                  </span>
                  <span class="text-xs font-semibold text-[var(--text-muted)]" x-show="sidebarMini" x-cloak>
                    <i class="fa-solid fa-gear"></i>
                  </span>
                  <svg class="h-4 w-4 transition-transform" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.24 4.24a.75.75 0 01-1.06 0L5.21 8.29a.75.75 0 01.02-1.08z" clip-rule="evenodd" />
                  </svg>
                </button>

                <div class="p-2 space-y-1 bg-[var(--sidebar-bg)] border-t border-[var(--border)]"
                  data-collapse-panel data-key="nav:admin">
                  <a href="{{ route('admin.users.index') }}"
                    data-nav-search-item
                    data-label="user management admin pengguna"
                    class="{{ $navItemBase }} {{ request()->routeIs('admin.users.*') ? 'active bg-[var(--sidebar-hover)] text-brand-blue font-semibold' : '' }}">
                    <span class="w-6 flex justify-center text-[var(--text-muted)]">
                      <i class="fa-solid fa-user-gear text-sm"></i>
                    </span>
                    <span class="text-sm" x-show="!sidebarMini" x-transition>User Management</span>
                  </a>
                </div>
              </section>
            </div>
          @endif
          @endauth

        </nav>
      </aside>

      <!-- MAIN -->
      <main class="flex-1 flex flex-col min-w-0 lg:pr-0">

        <!-- TOPBAR -->
        <header class="h-16 flex items-center justify-between px-3 sm:px-4 md:px-6 border-b border-[var(--border)] bg-[var(--panel)]/95 backdrop-blur">
          <!-- LEFT -->
          <div class="flex items-center gap-2">
            <button id="btnHamburger"
              class="lg:hidden inline-flex items-center justify-center rounded-lg border border-[var(--border)] bg-[var(--panel)] p-2"
              aria-label="Toggle Sidebar">
              <svg class="h-5 w-5" viewBox="0 0 24 24" stroke="currentColor" fill="none">
                <path stroke-width="2" d="M4 6h16M4 12h16M4 18h12" />
              </svg>
            </button>

            <button
              type="button"
              class="hidden lg:inline-flex items-center justify-center rounded-lg border border-[var(--border)] bg-[var(--panel)] p-2 hover:bg-[var(--sidebar-hover)]"
              @click="toggleSidebarMini()"
              title="Toggle sidebar">
              <i class="fa-solid fa-table-columns text-sm text-[var(--text-muted)]"></i>
            </button>
          </div>

          <!-- RIGHT -->
          <div class="ml-auto flex items-center gap-2 md:gap-3">

            <!-- Theme Toggle -->
            <button id="themeToggle"
              class="inline-flex items-center justify-center rounded-lg border border-[var(--border)] bg-[var(--panel)] p-2 hover:opacity-85"
              title="Ganti mode tema">
              <svg class="h-5 w-5 hidden dark:inline" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="12" cy="12" r="4" stroke-width="2" />
                <path stroke-width="2" d="M12 3v2M12 19v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M1 12h2M21 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4" />
              </svg>
              <svg class="h-5 w-5 dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-width="2" d="M21 12.8A9 9 0 1111.2 3a7 7 0 009.8 9.8z" />
              </svg>
            </button>

            <!-- Profile Button (dropdown overlay global) -->
            <div class="relative">
              <button id="profileBtn"
                class="flex items-center gap-2 sm:gap-3 px-2 py-1.5 sm:px-2.5 rounded-lg hover:bg-[var(--sidebar-hover)]"
                aria-haspopup="menu" aria-expanded="false">
                <div class="hidden sm:flex flex-col items-end leading-tight">
                  <span class="text-xs sm:text-sm font-semibold text-[var(--panel-text)]">{{ Auth::user()->name ?? 'Admin' }}</span>
                  <span class="text-[10px] sm:text-xs text-[var(--panel-text)]/60">Online</span>
                </div>
                <div class="relative h-8 w-8 sm:h-9 sm:w-9">
                  <div class="absolute inset-0 rounded-full animate-spin-slow bg-gradient-to-tr from-brand-blue via-brand-cyan to-brand-dark"></div>
                  <div class="absolute inset-[2px] rounded-full bg-[var(--panel)] flex items-center justify-center text-xs sm:text-sm font-bold text-brand-blue">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                  </div>
                </div>
              </button>
            </div>

          </div>
        </header>

        <!-- CONTENT -->
        <div class="flex-1 px-3 sm:px-4 md:px-6 lg:px-6 py-4 md:py-6 pb-16 bg-transparent text-[var(--text-main)] transition-theme">
          <div class="max-w-6xl xl:max-w-7xl mx-auto w-full">
            {{ $slot ?? '' }}
            @yield('content')
          </div>
        </div>

        <!-- FOOTER -->
        <footer class="border-t border-[var(--border)] bg-[var(--panel)]/95 backdrop-blur sticky bottom-0 z-30">
          <div class="max-w-6xl xl:max-w-7xl mx-auto w-full px-3 sm:px-4 md:px-6 lg:px-6 py-3
                      text-[11px] sm:text-xs text-[var(--text-muted)] flex items-center justify-center">
            <span class="text-center">
              © {{ date('Y') }} SquadTix. All rights reserved.
            </span>
          </div>
        </footer>
      </main>
    </div>
  </div>

  <!-- GLOBAL PROFILE OVERLAY (FIX: dropdown tidak numpuk & selalu di atas) -->
  <div id="profileOverlay" class="hidden fixed inset-0 z-[9999]">
    <div id="profileBackdrop" class="absolute inset-0 bg-black/35 backdrop-blur-[2px]"></div>

    <div id="profilePanel"
      class="absolute right-4 top-20 w-60 sm:w-64 bg-[var(--panel)] border border-[var(--border)] rounded-xl shadow-xl z-[10000] overflow-hidden">

      <div class="px-4 py-3 border-b border-[var(--border)] bg-slate-50/70 dark:bg-slate-900/40">
        <div class="flex items-center gap-3">
          <div class="relative h-9 w-9">
            <div class="absolute inset-0 rounded-full bg-gradient-to-tr from-brand-blue via-brand-cyan to-brand-dark animate-spin-slow"></div>
            <div class="absolute inset-[2px] rounded-full bg-[var(--panel)] flex items-center justify-center text-xs font-bold text-brand-blue">
              {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
            </div>
          </div>

          <div class="flex-1 min-w-0">
            <div class="flex items-center gap-1">
              <p class="text-sm font-semibold text-[var(--panel-text)] truncate">
                {{ Auth::user()->name ?? 'User' }}
              </p>
              @if(Auth::user()?->isAdmin())
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-100 text-[10px]">
                  Admin
                </span>
              @endif
            </div>
            <p class="text-[11px] text-[var(--text-muted)] truncate">
              {{ Auth::user()->email ?? '-' }}
            </p>
            <div class="mt-1 flex items-center gap-1.5">
              <span class="inline-flex h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
              <span class="text-[10px] text-emerald-600 dark:text-emerald-400">Online</span>
            </div>
          </div>
        </div>
      </div>

      <div class="py-1">
        <a href="{{ route('profile.edit') }}"
          class="flex items-center gap-2 px-4 py-2 text-xs sm:text-sm text-[var(--panel-text)] hover:bg-[var(--sidebar-hover)]">
          <i class="fa-regular fa-user text-[13px] text-[var(--text-muted)]"></i>
          <span>View / Edit Profile</span>
        </a>
      </div>

      @if(!empty(Auth::user()?->last_login_at))
        <div class="px-4 py-2 border-t border-[var(--border)] bg-slate-50/60 dark:bg-slate-900/40">
          <p class="text-[10px] text-[var(--text-muted)]">
            Last login:
            <span class="font-medium text-[var(--panel-text)]">
              {{ optional(Auth::user()->last_login_at)->timezone('Asia/Jakarta')->format('d M Y H:i') }}
            </span>
          </p>
        </div>
      @endif

      <div class="border-t border-[var(--border)]">
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit"
            class="w-full flex items-center justify-between px-4 py-2.5 text-xs sm:text-sm text-rose-600 hover:bg-rose-50/70 dark:hover:bg-rose-900/30">
            <span class="inline-flex items-center gap-2">
              <i class="fa-solid fa-arrow-right-from-bracket text-[13px]"></i>
              <span>Logout</span>
            </span>
            <span class="text-[10px] text-rose-500/80">Keluar dari SquadTIx</span>
          </button>
        </form>
      </div>
    </div>
  </div>

  <!-- SCRIPT: THEME, SIDEBAR, COLLAPSE + SEARCH, PROFILE OVERLAY -->
  <script>
    // Theme toggle
    document.getElementById('themeToggle')?.addEventListener('click', () => {
      const html = document.documentElement;
      const dark = html.classList.toggle('dark');
      try { localStorage.setItem('theme', dark ? 'dark' : 'light'); } catch (_) {}
    });

    // Sidebar mobile toggle
    const aside = document.getElementById('aside');
    const backdrop = document.getElementById('asideBackdrop');
    const btnHamburger = document.getElementById('btnHamburger');

    function openAside() {
      aside.classList.remove('-translate-x-full');
      backdrop.style.opacity = '1';
      backdrop.style.pointerEvents = 'auto';
    }
    function closeAside() {
      aside.classList.add('-translate-x-full');
      backdrop.style.opacity = '0';
      backdrop.style.pointerEvents = 'none';
    }
    btnHamburger?.addEventListener('click', () => {
      aside.classList.contains('-translate-x-full') ? openAside() : closeAside();
    });
    backdrop?.addEventListener('click', closeAside);

    // Collapsible panels persisted state
    const STATE_PREFIX = 'asm:collapse:';
    function setChevron(btn, expanded) {
      const icon = btn.querySelector('svg');
      if (icon) icon.style.transform = expanded ? 'rotate(180deg)' : 'rotate(0deg)';
    }
    document.querySelectorAll('[data-collapse-btn]').forEach(btn => {
      const key = STATE_PREFIX + btn.dataset.key;
      const stored = localStorage.getItem(key);
      const expanded = stored === null ? true : stored === '1';
      const panel = document.querySelector(`[data-collapse-panel][data-key="${btn.dataset.key}"]`);
      btn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
      setChevron(btn, expanded);
      if (panel) panel.style.display = expanded ? 'block' : 'none';

      btn.addEventListener('click', () => {
        const now = btn.getAttribute('aria-expanded') !== 'true';
        btn.setAttribute('aria-expanded', now ? 'true' : 'false');
        setChevron(btn, now);
        if (panel) panel.style.display = now ? 'block' : 'none';
        try { localStorage.setItem(key, now ? '1' : '0'); } catch (_) {}
      });
    });

    // Sidebar search filter
    (function() {
      const input = document.getElementById('sidebarSearch');
      if (!input) return;

      const items = Array.from(document.querySelectorAll('[data-nav-search-item]'));
      const sections = Array.from(document.querySelectorAll('[data-nav-section]'));

      function normalize(text) {
        return (text || '')
          .toString()
          .toLowerCase()
          .normalize('NFD')
          .replace(/[\u0300-\u036f]/g, '');
      }

      function applyFilter() {
        const q = normalize(input.value.trim());

        if (q) {
          document.querySelectorAll('[data-collapse-btn]').forEach(btn => {
            const panel = document.querySelector(`[data-collapse-panel][data-key="${btn.dataset.key}"]`);
            btn.setAttribute('aria-expanded', 'true');
            setChevron(btn, true);
            if (panel) panel.style.display = 'block';
            try { localStorage.setItem(STATE_PREFIX + btn.dataset.key, '1'); } catch (_) {}
          });
        }

        items.forEach(el => {
          if (!q) { el.classList.remove('hidden'); return; }
          const label = normalize(el.dataset.label || el.textContent || '');
          const match = label.includes(q);
          el.classList.toggle('hidden', !match);
        });

        sections.forEach(section => {
          if (!q) { section.classList.remove('hidden'); return; }
          const visibleItem = section.querySelector('[data-nav-search-item]:not(.hidden)');
          section.classList.toggle('hidden', !visibleItem);
        });
      }

      input.addEventListener('input', applyFilter);
    })();

    // PROFILE OVERLAY (fix: selalu di atas halaman, tidak numpuk)
    (function () {
      const btn = document.getElementById('profileBtn');
      const overlay = document.getElementById('profileOverlay');
      const panel = document.getElementById('profilePanel');
      const pBackdrop = document.getElementById('profileBackdrop');

      if (!btn || !overlay || !panel || !pBackdrop) return;

      function openProfile() {
        overlay.classList.remove('hidden');
        btn.setAttribute('aria-expanded', 'true');

        // posisi panel mengikuti tombol profile
        const r = btn.getBoundingClientRect();
        const gap = 10;

        // pastikan panel punya ukuran (overlay sudah tampil)
        const panelW = panel.offsetWidth || 260;
        const panelH = panel.offsetHeight || 220;

        const top = Math.min(window.innerHeight - panelH - gap, r.bottom + gap);
        const leftIdeal = r.right - panelW; // align kanan tombol
        const left = Math.max(gap, Math.min(window.innerWidth - panelW - gap, leftIdeal));

        panel.style.top = `${Math.max(gap, top)}px`;
        panel.style.left = `${left}px`;
      }

      function closeProfile() {
        overlay.classList.add('hidden');
        btn.setAttribute('aria-expanded', 'false');
      }

      function toggleProfile() {
        if (overlay.classList.contains('hidden')) openProfile();
        else closeProfile();
      }

      btn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        toggleProfile();
      });

      pBackdrop.addEventListener('click', closeProfile);

      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !overlay.classList.contains('hidden')) closeProfile();
      });

      window.addEventListener('resize', () => {
        if (!overlay.classList.contains('hidden')) openProfile();
      }, { passive: true });

      window.addEventListener('scroll', () => {
        if (!overlay.classList.contains('hidden')) openProfile();
      }, { passive: true });
    })();
  </script>

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

  @stack('scripts')
</body>

</html>
