@extends('layouts.app')

@php
    // Tab aktif (production / claim) dari query ?tab=
    $activeTab = request()->query('tab', 'production');

    // Segment label dari controller (jika tidak ada, fallback)
    $segments = $segments ?? [
        'all'     => 'All Segment',
        'konven'  => 'Konvensional',
        'syariah' => 'Syariah',
    ];

    $branches = $branches ?? [];
    $cobs     = $cobs ?? [];

    $currentSegment = request('segment', 'all');
    $isSyariah      = ($currentSegment === 'syariah');
@endphp

@section('content')
<div
    x-data="{
        showFilter: true,
        activeTab: '{{ $activeTab }}',

        init() {
            // Inisialisasi Tgl Awal
            flatpickr(this.$refs.startDatePicker, {
                dateFormat: 'Y-m-d',
                allowInput: true,
                defaultDate: '{{ request('start_date') }}' || null
            });

            // Inisialisasi Tgl Akhir
            flatpickr(this.$refs.endDatePicker, {
                dateFormat: 'Y-m-d',
                allowInput: true,
                defaultDate: '{{ request('end_date') }}' || null
            });
        },

        setTab(tab) {
            this.activeTab = tab;
            const form = document.getElementById('filterForm');
            if (form) {
                form.querySelector('input[name=tab]').value = tab;
                form.submit();
            }
        }
    }"
    class="space-y-6 max-w-7xl mx-auto px-3 sm:px-4 md:px-0"
>

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <div class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-wide text-[var(--text-muted)]">
                <span class="px-2 py-0.5 rounded-full bg-brand-blue/10 text-brand-blue font-semibold">
                    Monitoring
                </span>
                <span>Trial Balance - Production &amp; Claim</span>
            </div>
            <h1 class="mt-1 text-xl md:text-2xl font-semibold text-[var(--text-main)]">
                Trial Balance - Production &amp; Claim
            </h1>
            <p class="mt-1 text-xs md:text-sm text-[var(--text-muted)]">
                Drilldown data produksi &amp; klaim dengan filter dinamis.
                Export Excel / PDF sudah dirangkum by Main Class.
            </p>
        </div>

        {{-- SEGMENT + TAB --}}
        <div class="flex flex-col items-stretch md:items-end gap-2">
            {{-- Segment pills --}}
            <div class="w-full md:w-auto overflow-x-auto">
                <div class="inline-flex flex-nowrap items-center gap-1 rounded-full bg-[var(--panel)] border border-[var(--border)] px-2 py-1">
                    @foreach($segments as $key => $label)
                        <a
                            href="{{ request()->fullUrlWithQuery(['segment' => $key]) }}"
                            class="text-[10px] md:text-xs px-2.5 py-1 rounded-full border transition-colors whitespace-nowrap
                                {{ request('segment', 'all') === $key
                                    ? 'bg-brand-blue text-white border-brand-blue shadow-sm'
                                    : 'bg-transparent text-[var(--text-muted)] border-transparent hover:border-[var(--border)]'
                                }}"
                        >
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Tab Production / Claim --}}
            <div class="w-full md:w-auto overflow-x-auto">
                <div class="inline-flex flex-nowrap rounded-xl border border-[var(--border)] bg-[var(--panel)] p-1 text-xs md:text-sm font-medium">
                    <button
                        type="button"
                        @click="setTab('production')"
                        :class="activeTab === 'production'
                            ? 'bg-brand-blue text-white shadow-sm'
                            : 'text-[var(--text-muted)] hover:bg-[var(--sidebar-hover)]'"
                        class="px-3 md:px-4 py-1.5 rounded-lg transition-colors flex items-center gap-2 whitespace-nowrap"
                    >
                        <i class="fa-solid fa-coins text-[11px]"></i>
                        <span>Production</span>
                    </button>
                    <button
                        type="button"
                        @click="setTab('claim')"
                        :class="activeTab === 'claim'
                            ? 'bg-brand-blue text-white shadow-sm'
                            : 'text-[var(--text-muted)] hover:bg-[var(--sidebar-hover)]'"
                        class="px-3 md:px-4 py-1.5 rounded-lg transition-colors flex items-center gap-2 whitespace-nowrap"
                    >
                        <i class="fa-solid fa-file-shield text-[11px]"></i>
                        <span>Claim</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER + EXPORT BAR --}}
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--panel)] shadow-sm">
        {{-- Baris tunggal: filter + kalimat + tombol export di kanan --}}
        <div class="flex flex-wrap sm:flex-nowrap items-center gap-2 px-3 md:px-4 py-2.5 border-b border-[var(--border)]">
            {{-- Filter + kalimat --}}
            <div class="flex items-center gap-2 flex-1 min-w-[260px]">
                <button
                    type="button"
                    @click="showFilter = !showFilter"
                    class="inline-flex items-center gap-2 text-xs md:text-sm font-medium text-[var(--text-main)] hover:text-brand-blue"
                >
                    <i class="fa-solid fa-filter text-[11px]"></i>
                    <span>Filter Data</span>
                    <span
                        class="inline-flex items-center justify-center text-[10px] w-4 h-4 rounded-full border border-[var(--border)]"
                        x-text="showFilter ? '-' : '+'"
                    ></span>
                </button>
                <span class="hidden sm:inline text-[11px] text-[var(--text-muted)]">
                    Sesuaikan periode, cabang, COB, dan parameter lain.
                </span>
            </div>

            {{-- Export Excel / PDF --}}
            <div class="flex items-center gap-2 ml-auto">
                <button
                    form="filterForm"
                    type="submit"
                    name="export"
                    value="excel"
                    class="inline-flex items-center gap-2 rounded-lg border border-emerald-500/60 bg-emerald-50 text-emerald-700 text-xs md:text-sm font-medium px-2.5 md:px-3 py-1.5 hover:bg-emerald-100 whitespace-nowrap"
                >
                    <i class="fa-solid fa-file-excel text-[12px]"></i>
                    <span class="hidden sm:inline">Export Excel (by Main Class)</span>
                    <span class="sm:hidden">Excel</span>
                </button>
                <button
                    form="filterForm"
                    type="submit"
                    name="export"
                    value="pdf"
                    class="inline-flex items-center gap-2 rounded-lg border border-rose-500/70 bg-rose-50 text-rose-700 text-xs md:text-sm font-medium px-2.5 md:px-3 py-1.5 hover:bg-rose-100 whitespace-nowrap"
                >
                    <i class="fa-solid fa-file-pdf text-[12px]"></i>
                    <span class="hidden sm:inline">Export PDF (by Main Class)</span>
                    <span class="sm:hidden">PDF</span>
                </button>
            </div>
        </div>

        {{-- FORM FILTER --}}
        <form
            id="filterForm"
            method="GET"
            action="{{ route('production.claim') }}"
            class="px-3 md:px-4 pt-3 pb-4 space-y-3"
            x-show="showFilter"
            x-cloak
        >
            {{-- tab & segment selalu ikut terkirim --}}
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            <input type="hidden" name="segment" value="{{ request('segment', 'all') }}">

            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 md:gap-4">
                {{-- Cabang --}}
                <div class="space-y-1">
                    <label class="text-[11px] font-medium text-[var(--text-muted)] uppercase tracking-wide">
                        Cabang
                    </label>
                    <select
                        name="branch"
                        class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-2 py-2 text-xs md:text-sm focus:outline-none focus:ring-1 focus:ring-brand-blue/60 lg:px-2.5 lg:py-2.5"
                    >
                        <option value="">All Cabang</option>
                        @foreach($branches as $branchKey => $branchLabel)
                            <option
                                value="{{ $branchKey }}"
                                {{ request('branch') == $branchKey ? 'selected' : '' }}
                            >
                                {{ $branchLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- COB --}}
                <div class="space-y-1">
                    <label class="text-[11px] font-medium text-[var(--text-muted)] uppercase tracking-wide">
                        COB / Class of Business
                    </label>
                    <select
                        name="cob"
                        class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-2 py-2 text-xs md:text-sm focus:outline-none focus:ring-1 focus:ring-brand-blue/60 lg:px-2.5 lg:py-2.5"
                    >
                        <option value="">All COB</option>
                        @foreach($cobs as $cob)
                            <option
                                value="{{ $cob }}"
                                {{ request('cob') == $cob ? 'selected' : '' }}
                            >
                                {{ $cob }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Periode Type --}}
                <div class="space-y-1">
                    <label class="text-[11px] font-medium text-[var(--text-muted)] uppercase tracking-wide">
                        Periode
                    </label>
                    <select
                        name="period_type"
                        class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)] px-2 py-2 text-xs md:text-sm focus:outline-none focus:ring-1 focus:ring-brand-blue/60 lg:px-2.5 lg:py-2.5"
                    >
                        <option value="this_month" {{ request('period_type','this_month')=='this_month' ? 'selected' : '' }}>Bulan Berjalan</option>
                        <option value="ytd"        {{ request('period_type')=='ytd' ? 'selected' : '' }}>Year to Date</option>
                        <option value="custom"     {{ request('period_type')=='custom' ? 'selected' : '' }}>Custom Range</option>
                    </select>
                </div>

                {{-- Periode Tanggal (Flatpickr) --}}
                <div class="flex flex-col sm:flex-row sm:items-end gap-2 sm:gap-3">

                    {{-- Tgl Awal --}}
                    <div class="flex-1 min-w-0 space-y-1">
                        <label class="text-[11px] font-medium text-[var(--text-muted)] uppercase tracking-wide">
                            Tgl Awal
                        </label>
                        <input
                            x-ref="startDatePicker"
                            type="text"
                            name="start_date"
                            autocomplete="off"
                            value="{{ request('start_date') }}"
                            placeholder="YYYY-MM-DD"
                            class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)]
                                   px-2 py-2 text-xs md:text-xs
                                   focus:outline-none focus:ring-1 focus:ring-brand-blue/60
                                   lg:px-3 lg:py-2.5 lg:text-sm"
                        >
                    </div>

                    {{-- Tgl Akhir --}}
                    <div class="flex-1 min-w-0 space-y-1">
                        <label class="text-[11px] font-medium text-[var(--text-muted)] uppercase tracking-wide">
                            Tgl Akhir
                        </label>
                        <input
                            x-ref="endDatePicker"
                            type="text"
                            name="end_date"
                            autocomplete="off"
                            value="{{ request('end_date') }}"
                            placeholder="YYYY-MM-DD"
                            class="w-full rounded-lg border border-[var(--border)] bg-[var(--panel)]
                                   px-2 py-2 text-xs md:text-xs
                                   focus:outline-none focus:ring-1 focus:ring-brand-blue/60
                                   lg:px-3 lg:py-2.5 lg:text-sm"
                        >
                    </div>

                </div>
            </div>

            {{-- Tombol Apply & Reset --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 pt-2">
                <div class="text-[10px] md:text-[11px] text-[var(--text-muted)]">
                    * Filter akan mempengaruhi tampilan tabel &amp; nilai export.
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a
                        href="{{ route('production.claim', ['tab' => $activeTab, 'segment' => request('segment','all')]) }}"
                        class="inline-flex items-center gap-1 rounded-lg border border-[var(--border)] px-2.5 py-1.5 text-[11px] md:text-xs text-[var(--text-muted)] hover:bg-[var(--sidebar-hover)]"
                    >
                        <i class="fa-solid fa-rotate-left text-[10px]"></i>
                        Reset
                    </a>
                    <button
                        type="submit"
                        class="inline-flex items-center gap-2 rounded-lg bg-brand-blue text-white text-[11px] md:text-xs font-medium px-3 md:px-4 py-1.5 shadow-sm hover:bg-brand-blue/90"
                    >
                        <i class="fa-solid fa-magnifying-glass text-[10px]"></i>
                        Apply Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- RINGKASAN BY MAIN_CLASS --}}
    @php
        $summary = $activeTab === 'production'
            ? ($summaryMainClassProd ?? [])
            : ($summaryMainClassClaim ?? []);
    @endphp

    @if(!empty($summary))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3 md:gap-4">
            @foreach($summary as $row)
                <div class="rounded-2xl border border-[var(--border)] bg-[var(--panel)] px-3 py-3 md:px-4 md:py-4 flex flex-col gap-1 shadow-sm">
                    <div class="text-[10px] uppercase tracking-wide text-[var(--text-muted)]">
                        {{ $row['main_class'] ?? 'Main Class' }}
                    </div>
                    <div class="text-sm font-semibold text-[var(--text-main)]">
                        {{ isset($row['total_amount_idr']) ? number_format($row['total_amount_idr'], 2, ',', '.') : '0,00' }}
                        <span class="text-[10px] text-[var(--text-muted)] ml-1">IDR</span>
                    </div>
                    @isset($row['count_policy'])
                        <div class="text-[10px] text-[var(--text-muted)]">
                            {{ $row['count_policy'] }} record
                        </div>
                    @endisset
                </div>
            @endforeach
        </div>
    @endif

    {{-- TABEL DETAIL --}}
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--panel)] shadow-sm">
        <div class="flex items-center justify-between px-3 md:px-4 py-2.5 border-b border-[var(--border)]">
            <div class="flex items-center gap-2 text-xs md:text-sm font-medium text-[var(--text-main)]">
                <i class="fa-solid fa-table-list text-[12px] text-brand-blue"></i>
                <span x-text="activeTab === 'production' ? 'Detail Production' : 'Detail Claim'"></span>
            </div>
            <div class="text-[10px] md:text-[11px] text-[var(--text-muted)]">
                Scroll horizontal &amp; vertical untuk melihat semua kolom.
            </div>
        </div>

        <div class="w-full max-h-[460px] overflow-auto">
            @if($activeTab === 'production')
                {{-- ===================== TABLE PRODUCTION ===================== --}}
                <table class="min-w-full table-auto text-[11px] md:text-xs">
                    <thead class="sticky top-0 z-10 border-b border-[var(--border)]">
                        <tr class="text-[9px] sm:text-[10px] uppercase tracking-wide bg-brand-blue text-white">
                            <th class="px-3 py-2 text-left align-top min-w-[80px]">Segment</th>
                            <th class="px-3 py-2 text-left align-top min-w-[90px]">Close Dt</th>
                            <th class="px-3 py-2 text-left align-top min-w-[130px]">Cabang</th>
                            <th class="px-3 py-2 text-left align-top min-w-[160px]">Nopol</th>

                            <th class="px-3 py-2 text-left align-top min-w-[150px]">Main Class</th>
                            <th class="px-3 py-2 text-left align-top min-w-[150px]">Class of Bus</th>

                            <th class="px-3 py-2 text-left align-top min-w-[130px]">Tgl Mulai</th>
                            <th class="px-3 py-2 text-left align-top min-w-[130px]">Tgl Akhir</th>
                            <th class="px-3 py-2 text-left align-top min-w-[200px]">Nama Tertanggung</th>

                            <th class="px-3 py-2 text-left align-top min-w-[120px]">Awal Dt</th>
                            <th class="px-3 py-2 text-left align-top min-w-[120px]">Akhir Dt</th>

                            <th class="px-3 py-2 text-left align-top min-w-[90px]">Currency</th>
                            <th class="px-3 py-2 text-right align-top min-w-[90px]">Cur Rate</th>

                            <th class="px-3 py-2 text-right align-top min-w-[130px]">Sum Insured</th>
                            <th class="px-3 py-2 text-right align-top min-w-[130px]">Premi Bruto</th>

                            @if($isSyariah)
                                <th class="px-3 py-2 text-right align-top min-w-[130px]">Premi Tabarru</th>
                                <th class="px-3 py-2 text-right align-top min-w-[130px]">Premi Ujroh</th>
                            @endif

                            <th class="px-3 py-2 text-right align-top min-w-[130px]">Komisi</th>
                            <th class="px-3 py-2 text-right align-top min-w-[130px]">Diskon</th>
                            <th class="px-3 py-2 text-right align-top min-w-[130px]">Other Fee</th>
                            <th class="px-3 py-2 text-right align-top min-w-[130px]">Premi Reas</th>
                            <th class="px-3 py-2 text-right align-top min-w-[130px]">Komisi Reas</th>

                            <th class="px-3 py-2 text-right align-top min-w-[130px]">Premi OR</th>
                            <th class="px-3 py-2 text-right align-top min-w-[110px]">% Komisi</th>
                            <th class="px-3 py-2 text-right align-top min-w-[110px]">% Diskon</th>

                            <th class="px-3 py-2 text-left align-top min-w-[220px]">Alamat</th>
                            <th class="px-3 py-2 text-right align-top min-w-[110px]">Premi Jiwa</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">Bisnis</th>
                            <th class="px-3 py-2 text-left align-top min-w-[90px]">TLO</th>
                            <th class="px-3 py-2 text-left align-top min-w-[90px]">Akad</th>
                            <th class="px-3 py-2 text-left align-top min-w-[80px]">Tenor</th>

                            <th class="px-3 py-2 text-left align-top min-w-[110px]">No Nota</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">Tgl Nota</th>
                            <th class="px-3 py-2 text-right align-top min-w-[110px]">Rate Premi</th>
                            <th class="px-3 py-2 text-left align-top min-w-[80px]">COI ST</th>
                            <th class="px-3 py-2 text-right align-top min-w-[80px]">TGA Pct</th>
                            <th class="px-3 py-2 text-left align-top min-w-[120px]">Endt Rem</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">Polis Install</th>

                            <th class="px-3 py-2 text-left align-top min-w-[180px]">Insrd Pr Nm</th>
                            <th class="px-3 py-2 text-left align-top min-w-[180px]">Prm Pr Nm</th>
                            <th class="px-3 py-2 text-left align-top min-w-[180px]">Brk Pr Nm</th>
                            <th class="px-3 py-2 text-left align-top min-w-[180px]">Oth Pr Nm</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">SB Nm</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">BB Nm</th>
                            <th class="px-3 py-2 text-left align-top min-w-[150px]">Company Distr Nm</th>
                            <th class="px-3 py-2 text-left align-top min-w-[120px]">Jenis Premi</th>

                            <th class="px-3 py-2 text-left align-top min-w-[110px]">Insrd Pr ID</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">Prm Pr ID</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">Brk Pr ID</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">Oth Pr ID</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">Company Distr</th>

                            <th class="px-3 py-2 text-left align-top min-w-[130px]">NPWP</th>
                            <th class="px-3 py-2 text-left align-top min-w-[130px]">KTP</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">Tgl Lahir</th>

                            <th class="px-3 py-2 text-right align-top min-w-[130px]">Amount IDR</th>
                            <th class="px-3 py-2 text-left align-top min-w-[120px]">Status Polis</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($rowsProduction ?? []) as $row)
                            @php
                                $rate = $row->cur_rate ?? 1;
                            @endphp
                            <tr class="border-b border-[var(--border)]/60 hover:bg-[var(--sidebar-hover)]/40">
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->segment }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">
                                    {{ is_string($row->close_dt ?? null) ? $row->close_dt : optional($row->close_dt)->format('Y-m-d') }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->cabang }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->nopol }}</td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->main_class }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->class_of_bus }}</td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->tgl_mulai }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->tgl_akhir }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->nama_tertanggung }}</td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->awal_dt }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->akhir_dt }}</td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->currency }}</td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format($row->cur_rate ?? 0, 2, ',', '.') }}
                                </td>

                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format(($row->sum_insured ?? 0) * $rate, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format(($row->premi_bruto ?? 0) * $rate, 2, ',', '.') }}
                                </td>

                                @if($isSyariah)
                                    <td class="px-3 py-2 text-right whitespace-nowrap">
                                        {{ number_format(($row->premi_tabaru ?? 0) * $rate, 2, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-2 text-right whitespace-nowrap">
                                        {{ number_format(($row->premi_ujroh ?? 0) * $rate, 2, ',', '.') }}
                                    </td>
                                @endif

                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format(($row->komisi ?? 0) * $rate, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format(($row->diskon ?? 0) * $rate, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format(($row->other_fee ?? 0) * $rate, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format(($row->premi_reas ?? 0) * $rate, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format(($row->komisi_reas ?? 0) * $rate, 2, ',', '.') }}
                                </td>

                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format($row->premi_or ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format($row->persen_komisi ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format($row->persen_diskon ?? 0, 2, ',', '.') }}
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->alamat }}</td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format($row->premi_jiwa ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->bisnis }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->tlo }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->akad }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->tenor }}</td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->nonota }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->tgl_nota }}</td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format($row->rate_premi ?? 0, 6, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->coi_st }}</td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format($row->tga_pct ?? 0, 3, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->endt_rem }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->polis_install }}</td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->insrd_pr_nm }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->prm_pr_nm }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->brk_pr_nm }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->oth_pr_nm }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->sb_nm }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->bb_nm }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->company_distr_nm }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->jenis_premi }}</td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->insrd_pr_id }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->prm_pr_id }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->brk_pr_id }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->oth_pr_id }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->company_distr }}</td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->npwp }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->ktp }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->tgl_lahir }}</td>

                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format($row->amount_idr ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->status_polis }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="60" class="px-3 py-4 text-center text-[var(--text-muted)]">
                                    Tidak ada data production untuk filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                {{-- ===================== TABLE CLAIM ===================== --}}
                <table class="min-w-full table-auto text-[11px] md:text-xs">
                    <thead class="sticky top-0 z-10 border-b border-[var(--border)]">
                        <tr class="text-[9px] sm:text-[10px] uppercase tracking-wide bg-brand-blue text-white">
                            <th class="px-3 py-2 text-left align-top min-w-[80px]">Segment</th>
                            <th class="px-3 py-2 text-left align-top min-w-[90px]">Loss Dt</th>
                            <th class="px-3 py-2 text-left align-top min-w-[120px]">Tgl Trans</th>
                            <th class="px-3 py-2 text-left align-top min-w-[130px]">Cabang</th>

                            <th class="px-3 py-2 text-left align-top min-w-[150px]">Nopol</th>
                            <th class="px-3 py-2 text-left align-top min-w-[200px]">Nama Tertanggung</th>

                            <th class="px-3 py-2 text-left align-top min-w-[150px]">Main Class</th>
                            <th class="px-3 py-2 text-left align-top min-w-[150px]">Class of Bus</th>

                            <th class="px-3 py-2 text-left align-top min-w-[140px]">No Klaim</th>

                            <th class="px-3 py-2 text-right align-top min-w-[130px]">Sum Insured</th>

                            <th class="px-3 py-2 text-left align-top min-w-[90px]">Valuta Klaim</th>
                            <th class="px-3 py-2 text-right align-top min-w-[90px]">Kurs Klaim</th>

                            <th class="px-3 py-2 text-right align-top min-w-[130px]">Jumlah Klaim</th>
                            <th class="px-3 py-2 text-right align-top min-w-[130px]">Klaim Reas</th>
                            <th class="px-3 py-2 text-right align-top min-w-[130px]">Klaim Netto</th>

                            <th class="px-3 py-2 text-left align-top min-w-[120px]">Tgl Klaim</th>
                            <th class="px-3 py-2 text-left align-top min-w-[120px]">Tgl Mulai</th>
                            <th class="px-3 py-2 text-left align-top min-w-[120px]">Tgl Akhir</th>
                            <th class="px-3 py-2 text-left align-top min-w-[120px]">Tgl Bayar</th>
                            <th class="px-3 py-2 text-left align-top min-w-[120px]">Tgl Nota</th>
                            <th class="px-3 py-2 text-left align-top min-w-[120px]">Tgl Lapor</th>

                            <th class="px-3 py-2 text-left align-top min-w-[150px]">Cause of Loss</th>
                            <th class="px-3 py-2 text-left align-top min-w-[150px]">Type of Loss</th>

                            <th class="px-3 py-2 text-left align-top min-w-[180px]">Insrd Pr Nm</th>
                            <th class="px-3 py-2 text-left align-top min-w-[180px]">Prm Pr Nm</th>
                            <th class="px-3 py-2 text-left align-top min-w-[180px]">Brk Pr Nm</th>
                            <th class="px-3 py-2 text-left align-top min-w-[180px]">Oth Pr Nm</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">SB Nm</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">BB Nm</th>
                            <th class="px-3 py-2 text-left align-top min-w-[150px]">Company Distr Nm</th>
                            <th class="px-3 py-2 text-left align-top min-w-[120px]">Jenis Premi</th>

                            <th class="px-3 py-2 text-left align-top min-w-[110px]">Insrd Pr ID</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">Prm Pr ID</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">Brk Pr ID</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">Oth Pr ID</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">Company Distr</th>

                            <th class="px-3 py-2 text-left align-top min-w-[130px]">NPWP</th>
                            <th class="px-3 py-2 text-left align-top min-w-[130px]">KTP</th>
                            <th class="px-3 py-2 text-left align-top min-w-[110px]">Tgl Lahir</th>

                            <th class="px-3 py-2 text-right align-top min-w-[90px]">Cur Rate</th>
                            <th class="px-3 py-2 text-right align-top min-w-[130px]">Amount IDR</th>
                            <th class="px-3 py-2 text-left align-top min-w-[120px]">Status Claim</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($rowsClaim ?? []) as $row)
                            @php
                                $kurs = $row->kurs_klaim ?? 1;
                            @endphp
                            <tr class="border-b border-[var(--border)]/60 hover:bg-[var(--sidebar-hover)]/40">
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->segment }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->loss_dt }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->tgl_trans }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->cabang }}</td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->nopol }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->nama_tertanggung }}</td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->main_class }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->class_of_bus }}</td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->no_klaim }}</td>

                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format(($row->sum_insured ?? 0) * $kurs, 2, ',', '.') }}
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->valuta_klaim }}</td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format($row->kurs_klaim ?? 0, 2, ',', '.') }}
                                </td>

                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format(($row->jumlah_klaim ?? 0) * $kurs, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format(($row->klaim_reas ?? 0) * $kurs, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format(($row->klaim_netto ?? 0) * $kurs, 2, ',', '.') }}
                                </td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->tgl_klaim }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->tgl_mulai }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->tgl_akhir }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->tgl_bayar }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->tgl_nota }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->tgl_lapor }}</td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->cause_of_loss }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->type_of_loss }}</td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->insrd_pr_nm }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->prm_pr_nm }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->brk_pr_nm }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->oth_pr_nm }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->sb_nm }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->bb_nm }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->company_distr_nm }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->jenis_premi }}</td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->insrd_pr_id }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->prm_pr_id }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->brk_pr_id }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->oth_pr_id }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->company_distr }}</td>

                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->npwp }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->ktp }}</td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->tgl_lahir }}</td>

                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format($row->cur_rate ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    {{ number_format($row->amount_idr ?? 0, 2, ',', '.') }}
                                </td>
                                <td class="px-3 py-2 whitespace-nowrap">{{ $row->status_claim }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="60" class="px-3 py-4 text-center text-[var(--text-muted)]">
                                    Tidak ada data claim untuk filter yang dipilih.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
