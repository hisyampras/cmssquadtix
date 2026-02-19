@extends('layouts.app')
@php($title='Add User')

@section('content')
<div class="max-w-[900px] mx-auto bg-[var(--panel)] text-[var(--panel-text)] border border-[var(--border)] rounded-xl shadow-sm">
  {{-- Header --}}
  <div class="p-6 border-b border-[var(--border)]">
    <h2 class="text-xl md:text-2xl font-bold">Add User</h2>
    <p class="text-sm text-[var(--text-muted)]">Buat akun baru untuk ASM – PORTAL</p>
  </div>

  {{-- Body --}}
  <div class="p-6">
    @if ($errors->any())
      <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('admin.users.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
      @csrf

      {{-- Informasi Akun --}}
      <div class="md:col-span-2 mb-1">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Informasi Akun</h3>
      </div>

      <div class="col-span-1">
        <label class="block text-sm font-medium mb-1">Nama</label>
        <input
          type="text"
          name="name"
          value="{{ old('name') }}"
          required
          class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
        />
      </div>

      <div class="col-span-1">
        <label class="block text-sm font-medium mb-1">Email</label>
        <input
          type="email"
          name="email"
          value="{{ old('email') }}"
          required
          class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
        />
      </div>

      <div class="col-span-1">
        <label class="block text-sm font-medium mb-1">Role</label>
        <select
          name="role"
          required
          class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
        >
          <option value="user"  @selected(old('role','user')==='user')>user</option>
          <option value="scan_gate" @selected(old('role')==='scan_gate')>scan_gate</option>
          <option value="admin" @selected(old('role')==='admin')>admin</option>
        </select>
      </div>

      <div class="col-span-1">
        <label class="block text-sm font-medium mb-1">
          Password <span class="text-[var(--text-muted)] text-xs">(opsional)</span>
        </label>
        <input
          type="password"
          name="password"
          placeholder="Kosongkan untuk auto-generate"
          class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
        />
      </div>

      {{-- Status & Organisasi --}}
      <div class="md:col-span-2 mt-2">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Status & Organisasi</h3>
      </div>

      <div class="col-span-1">
        <label class="block text-sm font-medium mb-1">Status</label>
        <select
          name="status"
          class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
        >
          <option value="active" @selected(old('status','active')==='active')>Active</option>
          <option value="suspended" @selected(old('status')==='suspended')>Suspended</option>
        </select>
        <p class="text-xs text-[var(--text-muted)] mt-1">Default: Active.</p>
      </div>

      <div class="col-span-1">
        <label class="block text-sm font-medium mb-1">Branch</label>
        <input
          type="text"
          name="branch"
          value="{{ old('branch') }}"
          placeholder="Contoh: Jakarta, Surabaya"
          class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
        />
      </div>

      <div class="col-span-1">
        <label class="block text-sm font-medium mb-1">Department</label>
        <input
          type="text"
          name="department"
          value="{{ old('department') }}"
          placeholder="Contoh: IT, Finance, Marketing"
          class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/50"
        />
      </div>

      {{-- Keamanan & Pengaturan Password --}}
      <div class="md:col-span-2 mt-2">
        <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">Keamanan & Password</h3>
      </div>

      <div class="col-span-1 md:col-span-2 space-y-2">
        <label class="inline-flex items-center gap-2 text-sm">
          <input
            type="checkbox"
            name="send_reset"
            value="1"
            @checked(old('send_reset'))
            class="rounded border-[var(--border)]"
          >
          <span>Kirim link reset password ke email user</span>
        </label>
        <p class="text-xs text-[var(--text-muted)]">
          Jika dicentang, user akan menerima email untuk membuat password sendiri.
          Jika tidak, sistem akan pakai password yang kamu isi (atau auto-generate jika kosong).
        </p>

        <label class="inline-flex items-center gap-2 text-sm mt-2">
          <input
            type="checkbox"
            name="must_reset_password"
            value="1"
            @checked(old('must_reset_password'))
            class="rounded border-[var(--border)]"
          >
          <span>Paksa user mengganti password pada login pertama</span>
        </label>
        <p class="text-xs text-[var(--text-muted)]">
          Cocok untuk akun yang dibuatkan oleh admin dengan password sementara.
        </p>

        <label class="inline-flex items-center gap-2 text-sm mt-2">
          <input
            type="checkbox"
            name="two_factor_enabled"
            value="1"
            @checked(old('two_factor_enabled'))
            class="rounded border-[var(--border)]"
          >
          <span>Aktifkan Two-Factor Authentication (2FA) untuk user ini</span>
        </label>
        <p class="text-xs text-[var(--text-muted)]">
          Menandai bahwa user wajib menggunakan lapisan keamanan tambahan (misalnya OTP).
        </p>
      </div>

      {{-- Actions --}}
      <div class="col-span-1 md:col-span-2 flex items-center justify-end gap-3 pt-4">
        <a
          href="{{ route('admin.users.index') }}"
          class="rounded-lg border border-[var(--border)] px-4 py-2 text-sm hover:bg-[var(--sidebar-hover)]"
        >
          Batal
        </a>
        <button
          type="submit"
          class="rounded-lg bg-brand-blue text-white px-4 py-2 text-sm font-semibold hover:opacity-90"
        >
          Simpan
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
