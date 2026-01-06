@extends('layouts.app')
@php($title = 'Edit User')

@section('content')
<div class="max-w-[900px] mx-auto bg-[var(--panel)] text-[var(--panel-text)] border border-[var(--border)] rounded-xl shadow-sm">
  {{-- Header --}}
  <div class="p-6 border-b border-[var(--border)]">
    <h2 class="text-xl md:text-2xl font-bold">Edit User</h2>
    <p class="text-sm text-[var(--text-muted)]">Perbarui data pengguna di bawah ini.</p>
  </div>

  {{-- Body --}}
  <div class="p-6">
    @if ($errors->any())
      <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('admin.users.update', $user) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      @csrf
      @method('PUT')

      {{-- Informasi Akun --}}
      <div class="md:col-span-2 mb-1">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Informasi Akun</h3>
      </div>

      <div class="col-span-1">
        <label class="block text-sm font-medium mb-1">Nama</label>
        <input
          type="text"
          name="name"
          value="{{ old('name', $user->name) }}"
          required
          class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2 text-sm
                 text-[var(--text-main)] focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
        />
      </div>

      <div class="col-span-1">
        <label class="block text-sm font-medium mb-1">Email</label>
        <input
          type="email"
          name="email"
          value="{{ old('email', $user->email) }}"
          required
          class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2 text-sm
                 text-[var(--text-main)] focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
        />
      </div>

      <div class="col-span-1">
        <label class="block text-sm font-medium mb-1">Role</label>
        <select
          name="role"
          class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2 text-sm
                 text-[var(--text-main)] focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
        >
          <option value="user"  @selected(old('role', $user->role)==='user')>User</option>
          <option value="admin" @selected(old('role', $user->role)==='admin')>Admin</option>
        </select>
      </div>

      {{-- Status & Organisasi --}}
      <div class="md:col-span-2 mt-2">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Status & Organisasi</h3>
      </div>

      <div class="col-span-1">
        <label class="block text-sm font-medium mb-1">Status</label>
        <select
          name="status"
          class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2 text-sm
                 text-[var(--text-main)] focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
        >
          <option value="active"    @selected(old('status', $user->status ?? 'active')==='active')>Active</option>
          <option value="suspended" @selected(old('status', $user->status ?? 'active')==='suspended')>Suspended</option>
        </select>
      </div>

      <div class="col-span-1">
        <label class="block text-sm font-medium mb-1">Branch</label>
        <input
          type="text"
          name="branch"
          value="{{ old('branch', $user->branch) }}"
          placeholder="Contoh: Jakarta, Surabaya"
          class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2 text-sm
                 text-[var(--text-main)] focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
        />
      </div>

      <div class="col-span-1">
        <label class="block text-sm font-medium mb-1">Department</label>
        <input
          type="text"
          name="department"
          value="{{ old('department', $user->department) }}"
          placeholder="Contoh: IT, Finance, Marketing"
          class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2 text-sm
                 text-[var(--text-main)] focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
        />
      </div>

      {{-- Keamanan & Password --}}
      <div class="md:col-span-2 mt-2">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Keamanan & Password</h3>
      </div>

      <div class="col-span-1 md:col-span-2 space-y-2">
        <label class="inline-flex items-center gap-2 text-sm">
          <input
            id="must_reset_password"
            type="checkbox"
            name="must_reset_password"
            value="1"
            @checked(old('must_reset_password', $user->must_reset_password ?? false))
            class="rounded border-[var(--border)]"
          >
          <span>Paksa user ganti password saat login berikutnya</span>
        </label>

        <label class="inline-flex items-center gap-2 text-sm mt-2">
          <input
            id="two_factor_enabled"
            type="checkbox"
            name="two_factor_enabled"
            value="1"
            @checked(old('two_factor_enabled', $user->two_factor_enabled ?? false))
            class="rounded border-[var(--border)]"
          >
          <span>Aktifkan Two-Factor Authentication (2FA) untuk user ini</span>
        </label>
        <p class="text-xs text-[var(--text-muted)]">
          Menandai bahwa user wajib menggunakan lapisan keamanan tambahan (misalnya OTP).
        </p>
      </div>

      <div class="col-span-1 md:col-span-2">
        <label class="block text-sm font-medium mb-1">
          Set Password Baru <span class="text-[var(--text-muted)] text-xs">(opsional)</span>
        </label>
        <input
          type="password"
          name="password"
          placeholder="Kosongkan jika tidak diubah"
          class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2 text-sm
                 text-[var(--text-main)] focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
        />
      </div>

      {{-- Actions --}}
      <div class="col-span-1 md:col-span-2 flex items-center justify-end gap-3 pt-4">
        <a
          href="{{ route('admin.users.index') }}"
          class="inline-flex items-center rounded-lg border border-[var(--border)] px-4 py-2 text-sm hover:bg-[var(--sidebar-hover)]"
        >
          Batal
        </a>

        {{-- (optional) kirim reset link bisa diaktifkan lagi kalau perlu --}}
        {{--
        <form method="POST" action="{{ route('admin.users.sendResetLink', $user) }}"
              onsubmit="return confirm('Kirim link reset password ke {{ $user->email }}?')">
          @csrf
          <button type="submit"
                  class="inline-flex items-center rounded-lg border border-[var(--border)] px-4 py-2 text-sm hover:bg-[var(--sidebar-hover)]">
            📩 Kirim Reset Link
          </button>
        </form>
        --}}

        <button
          type="submit"
          class="inline-flex items-center rounded-lg bg-brand-blue px-4 py-2 text-sm font-semibold text-white hover:opacity-95"
        >
          Simpan Perubahan
        </button>
      </div>
    </form>
  </div>
</div>

{{-- Banner impersonate --}}
@php($impersonating = session()->has('impersonator_id'))
@if($impersonating)
  <div class="mt-6 bg-amber-500/90 text-white px-6 py-2 flex justify-between items-center text-sm font-medium rounded-lg">
    <div>🕵️ Anda sedang impersonate sebagai <strong>{{ Auth::user()->name }}</strong></div>
    <form method="POST" action="{{ route('admin.impersonate.stop') }}">
      @csrf
      <button type="submit" class="bg-white/20 hover:bg-white/30 text-white font-semibold px-3 py-1.5 rounded-lg">
        🔙 Kembali ke Akun Admin
      </button>
    </form>
  </div>
@endif
@endsection
