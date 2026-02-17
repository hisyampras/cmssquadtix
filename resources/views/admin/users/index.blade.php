@extends('layouts.app')
@php($title='User Management')

@section('content')
<div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-0 py-4 space-y-4">

  {{-- Page header --}}
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
    <div>
      <h1 class="text-lg font-semibold text-slate-900 dark:text-white">
        User Management
      </h1>
      <p class="text-xs sm:text-sm text-[var(--text-muted)]">
        Kelola akses, status, dan keamanan user ASM Portal.
      </p>
    </div>

    {{-- Add user button (desktop) --}}
    <div class="hidden sm:flex">
      <a href="{{ route('admin.users.create') }}"
         class="inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-medium shadow-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Add User
      </a>
    </div>
  </div>

  {{-- Card utama: Filter + Table --}}
  <div class="border border-[var(--border)] rounded-2xl bg-[var(--panel)]/80 shadow-sm backdrop-blur-sm">
    {{-- Filter bar --}}
    <div class="border-b border-[var(--border)] px-4 sm:px-5 py-4">
      <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3">

        {{-- Filter form --}}
        <form method="GET"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2 w-full md:max-w-[860px]">

          {{-- Search --}}
          <div class="lg:col-span-2">
            <div class="relative">
              <input
                type="text"
                name="q"
                value="{{ $q ?? '' }}"
                placeholder="Cari nama / email"
                class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2.5 pl-9 text-sm
                       placeholder:text-[var(--text-muted)]
                       focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-blue/60"
              >
              <span class="absolute inset-y-0 left-3 flex items-center text-[var(--text-muted)]">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd"
                        d="M8.5 3.5a5 5 0 013.967 8.07l2.731 2.732a.75.75 0 11-1.06 1.06l-2.732-2.73A5 5 0 118.5 3.5zm0 1.5a3.5 3.5 0 100 7 3.5 3.5 0 000-7z"
                        clip-rule="evenodd" />
                </svg>
              </span>
            </div>
          </div>

          {{-- Role --}}
          <div>
            <select
              name="role"
              class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2.5 text-sm
                     focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-blue/60"
            >
              <option value="">Semua role</option>
              <option value="admin" @selected(request('role')==='admin')>Admin</option>
              <option value="scan_gate" @selected(request('role')==='scan_gate')>Scan Gate</option>
              <option value="user"  @selected(request('role')==='user')>User</option>
            </select>
          </div>

          {{-- Status --}}
          <div>
            <select
              name="status"
              class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2.5 text-sm
                     focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-blue/60"
            >
              <option value="">Semua status</option>
              <option value="active"    @selected(request('status')==='active')>Active</option>
              <option value="suspended" @selected(request('status')==='suspended')>Suspended</option>
            </select>
          </div>

          {{-- Branch --}}
          <div>
            <select
              name="branch"
              class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2.5 text-sm
                     focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-blue/60"
            >
              <option value="">Semua branch</option>
              @foreach($branches as $b)
                <option value="{{ $b }}" @selected(request('branch')===$b)>{{ $b }}</option>
              @endforeach
            </select>
          </div>

          {{-- Tombol Filter --}}
          <div class="flex items-stretch sm:justify-end">
            <button
              class="inline-flex items-center justify-center gap-2 w-full sm:w-auto
                     rounded-lg bg-brand-blue hover:bg-brand-blue/90 text-white px-4 py-2.5 text-sm font-medium shadow-sm">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none"
                   stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 4h18M5 8h14l-4 5v5l-6 2v-7L5 8z" />
              </svg>
              Filter
            </button>
          </div>
        </form>

        {{-- Add user button (mobile / tablet) --}}
        <div class="sm:hidden">
          <a href="{{ route('admin.users.create') }}"
             class="inline-flex w-full items-center justify-center gap-2 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 text-sm font-medium shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add User
          </a>
        </div>

      </div>
    </div>

    <br>

{{-- Table --}}
<div class="px-2 sm:px-4 pb-4 sm:pb-5">
  {{-- Scroll Y & X untuk seluruh table --}}
  <div class="rounded-xl border border-[var(--border)] bg-[var(--panel)] max-h-[60vh] overflow-auto">
    <table class="min-w-full text-xs sm:text-sm">
      <thead class="bg-[var(--sidebar-hover)] text-[var(--panel-text)]">
        <tr>
          <th class="text-left px-3 py-2.5 sm:px-4">User</th>
          <th class="text-left px-3 py-2.5 sm:px-4">Role</th>
          <th class="text-left px-3 py-2.5 sm:px-4">Status</th>
          <th class="text-left px-3 py-2.5 sm:px-4">Branch / Dept</th>
          <th class="text-left px-3 py-2.5 sm:px-4">2FA</th>
          <th class="text-left px-3 py-2.5 sm:px-4">Last Login</th>
          <th class="text-right px-3 py-2.5 sm:px-4">Actions</th>
        </tr>
      </thead>

      <tbody class="divide-y divide-[var(--border)]">
      @forelse($users as $u)
        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-900/40 transition-colors">
          <td class="px-3 py-3 sm:px-4 align-top">
            <div class="font-semibold text-slate-900 dark:text-white leading-tight">
              {{ $u->name }}
            </div>
            <div class="text-[10px] sm:text-xs text-[var(--text-muted)]">
              {{ $u->email }}
            </div>
          </td>

          <td class="px-3 py-3 sm:px-4 align-top text-xs sm:text-sm">
            <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-medium">
              {{ strtoupper($u->role) }}
            </span>
          </td>

          <td class="px-3 py-3 sm:px-4 align-top">
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-medium
              {{ $u->status==='active' ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' }}">
              <span class="h-1.5 w-1.5 rounded-full mr-1.5
                {{ $u->status==='active' ? 'bg-green-500' : 'bg-amber-500' }}">
              </span>
              {{ ucfirst($u->status) }}
            </span>
          </td>

          <td class="px-3 py-3 sm:px-4 align-top text-xs sm:text-sm">
            {{ $u->branch ?? '-' }} / {{ $u->department ?? '-' }}
          </td>

          <td class="px-3 py-3 sm:px-4 align-top">
            @if($u->two_factor_enabled)
              <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px]">
                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 mr-1.5"></span>
                ON
              </span>
            @else
              <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[11px]">
                OFF
              </span>
            @endif
          </td>

          <td class="px-3 py-3 sm:px-4 align-top text-[11px] sm:text-xs whitespace-nowrap">
            @if($u->last_login_at)
              <div>{{ $u->last_login_at->format('Y-m-d H:i') }}</div>
              <div class="text-[var(--text-muted)]">{{ $u->last_login_ip }}</div>
            @else
              <span class="text-[var(--text-muted)]">-</span>
            @endif
          </td>

          {{-- ==================== ACTIONS ICON ONLY ===================== --}}
          <td class="px-3 py-3 sm:px-4 align-top text-right">
            <div class="inline-flex items-center gap-2 whitespace-nowrap overflow-x-auto no-scrollbar">

              {{-- Edit --}}
              <a href="{{ route('admin.users.edit', $u) }}" class="action-btn" data-tip="Edit">
                <i class="fa-solid fa-pen-to-square text-blue-600"></i>
              </a>

              {{-- Reset Password --}}
              <form method="POST" action="{{ route('admin.users.resetpw', $u) }}"
                    onsubmit="return confirm('Kirim link reset password?')">
                @csrf
                <button class="action-btn" data-tip="Reset PW">
                  <i class="fa-solid fa-key text-amber-600"></i>
                </button>
              </form>

              {{-- Suspend / Activate --}}
              @if($u->status==='active')
                <form method="POST" action="{{ route('admin.users.suspend', $u) }}"
                      onsubmit="return confirm('Suspend user ini?')">
                  @csrf
                  <button class="action-btn" data-tip="Suspend">
                    <i class="fa-solid fa-user-slash text-orange-600"></i>
                  </button>
                </form>
              @else
                <form method="POST" action="{{ route('admin.users.unsuspend', $u) }}"
                      onsubmit="return confirm('Aktifkan user ini?')">
                  @csrf
                  <button class="action-btn" data-tip="Activate">
                    <i class="fa-solid fa-user-check text-green-600"></i>
                  </button>
                </form>
              @endif

              {{-- Impersonate --}}
              @if(Auth::id() !== $u->id)
                <form method="POST" action="{{ route('admin.users.impersonate', $u) }}"
                      onsubmit="return confirm('Impersonate user ini?')">
                  @csrf
                  <button class="action-btn" data-tip="Impersonate">
                    <i class="fa-solid fa-mask text-purple-600"></i>
                  </button>
                </form>
              @endif

              {{-- Delete --}}
              <form method="POST" action="{{ route('admin.users.destroy', $u) }}"
                    onsubmit="return confirm('Hapus user ini?')">
                @csrf @method('DELETE')
                <button class="action-btn" data-tip="Delete">
                  <i class="fa-solid fa-trash text-red-600"></i>
                </button>
              </form>

            </div>
          </td>
        </tr>

      @empty
        <tr>
          <td colspan="7" class="px-4 py-6 text-center text-[var(--text-muted)] text-sm">Tidak ada data.</td>
        </tr>
      @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-3">
    {{ $users->links() }}
  </div>
</div>

  </div>

</div>
@endsection
