@extends('layouts.app')
@php($title='User Profile')

@section('content')
<div class="max-w-3xl mx-auto bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-xl shadow-sm">
  <!-- Header -->
  <div class="p-8 border-b border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row items-center sm:items-start gap-6">
    <!-- Avatar animasi -->
    <div class="relative w-28 h-28 shrink-0">
      <div class="absolute inset-0 rounded-full animate-spin-slow bg-gradient-to-tr from-brand-blue via-brand-cyan to-brand-dark"></div>
      <div class="absolute inset-[4px] rounded-full bg-white dark:bg-slate-900 flex items-center justify-center text-3xl font-bold text-brand-blue">
        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
      </div>
    </div>

    <div class="flex-1">
      <h2 class="text-xl font-semibold">{{ Auth::user()->name ?? 'User' }}</h2>
      <p class="text-slate-500 dark:text-slate-400 text-sm">{{ Auth::user()->email ?? '-' }}</p>
      <div class="mt-2 text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wide">Profile Settings</div>
    </div>
  </div>

  <!-- Body: pakai partial Breeze standar -->
  <div class="p-6 md:p-8 space-y-10">
    @include('profile.partials.update-profile-information-form')

    <div class="border-t border-slate-200 dark:border-slate-700 pt-8">
      @include('profile.partials.update-password-form')
    </div>

    <div class="border-t border-slate-200 dark:border-slate-700 pt-8">
      @include('profile.partials.delete-user-form')
    </div>
  </div>
</div>
@endsection
