@extends('layouts.app')

@php($title = 'Contact Support & IT Helpdesk')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-xl sm:text-2xl font-semibold text-slate-900 dark:text-white">
            Contact Support & IT Helpdesk
        </h1>
        <p class="mt-1 text-sm text-[var(--text-muted)]">
            Halaman ini berisi kontak resmi IT serta prosedur yang harus diikuti apabila terjadi kendala dalam penggunaan ASM Portal,
            termasuk pengecekan data TB untuk menu Production & Claim.
        </p>
    </div>

    {{-- Card: Informasi Kontak --}}
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--panel)]/90 p-5 shadow-sm space-y-4">
        <div class="flex items-start gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-brand-blue/10 text-brand-blue text-lg">
                <i class="fa-solid fa-headset"></i>
            </span>

            <div>
                <h2 class="text-base font-semibold text-[var(--panel-text)]">Kontak IT Application Support</h2>
                <p class="text-sm text-[var(--text-muted)]">
                    Silakan hubungi tim IT apabila Anda menemukan kendala dalam penggunaan sistem.
                </p>

                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                    <div class="flex flex-col gap-1">
                        <span class="font-medium text-[var(--panel-text)]">Email:</span>
                        <span class="text-[var(--text-muted)]">it@stacoinsurance.com</span>
                    </div>

                    <div class="flex flex-col gap-1">
                        <span class="font-medium text-[var(--panel-text)]">Telepon / Ext:</span>
                        <span class="text-[var(--text-muted)]">021 - xxxx xxxx (Ext. 123 / 124)</span>
                    </div>

                    <div class="flex flex-col gap-1">
                        <span class="font-medium text-[var(--panel-text)]">Jam Layanan:</span>
                        <span class="text-[var(--text-muted)]">Senin – Jumat, 08.30 – 17.00 WIB</span>
                    </div>

                    <div class="flex flex-col gap-1">
                        <span class="font-medium text-[var(--panel-text)]">Respons SLA:</span>
                        <span class="text-[var(--text-muted)]">Penanganan awal dalam 1–2 hari kerja</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card: Prosedur Error / Mismatch --}}
    <div class="rounded-2xl border border-[var(--border)] bg-[var(--panel)]/90 p-5 shadow-sm space-y-4">
        <div class="flex items-start gap-3">
            <span class="inline-flex h-10 w-10 items-center justify-center rounded-xl bg-amber-500/10 text-amber-600 text-lg">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </span>

            <div>
                <h2 class="text-base font-semibold text-[var(--panel-text)]">Prosedur Jika Terjadi Error / Mismatch Data</h2>
                <p class="text-sm text-[var(--text-muted)]">
                    Langkah-langkah yang harus dilakukan ketika menemukan ketidaksesuaian data di ASM Portal,
                    terutama pada menu <strong>Production & Claim</strong> yang menggunakan data TB dari Data Warehouse.
                </p>

                <ol class="mt-3 space-y-3 text-sm list-decimal list-inside text-[var(--panel-text)]">

                    <li>
                        <strong>Catat tanggal, segmen, cabang, dan CoB</strong> yang sedang dipilih saat mismatch ditemukan.
                        <br><span class="text-[var(--text-muted)] text-xs">
                            (Informasi filter membantu IT melacak sumber masalah.)
                        </span>
                    </li>

                    <li>
                        <strong>Screenshot tampilan atau nilai yang dianggap tidak sesuai.</strong>
                        <br><span class="text-[var(--text-muted)] text-xs">
                            Foto dari dashboard sangat membantu pengecekan.
                        </span>
                    </li>

                    <li>
                        <strong>Bagian Akuntansi & Aktuaria wajib melakukan pengecekan TB bulanan terlebih dahulu.</strong>
                        <br>
                        <ul class="list-disc list-inside text-[var(--text-muted)] text-xs mt-1">
                            <li>Pastikan data TB yang dikirim ke DWH sudah lengkap.</li>
                            <li>Pastikan tidak ada perubahan jurnal setelah proses refresh.</li>
                            <li>Pastikan kurs / rate bulanan sudah benar (jika multi currency).</li>
                        </ul>
                    </li>

                    <li>
                        Jika setelah dicek oleh Akuntansi & Aktuaria perbedaan masih muncul,
                        <strong>hubungi IT dengan melampirkan hasil pengecekan TB</strong>.
                    </li>

                    <li>
                        IT akan melakukan:
                        <ul class="list-disc list-inside text-[var(--text-muted)] text-xs mt-1">
                            <li>Pengecekan log proses Data Warehouse</li>
                            <li>Pemeriksaan stored procedure refresh dashboard</li>
                            <li>Validasi data dari Core System vs DWH vs TB</li>
                        </ul>
                    </li>

                    <li>
                        Jika diperlukan, IT akan memberi rekomendasi perbaikan atau melakukan refresh ulang data.
                    </li>
                </ol>
            </div>
        </div>
    </div>

    

</div>
@endsection
