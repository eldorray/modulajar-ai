@php
    $status = match ($rpp->status) {
        'completed' => ['Selesai', 'bg-emerald-50 text-emerald-700 border-emerald-200/70'],
        'processing' => ['Diproses', 'bg-amber-50 text-amber-700 border-amber-200/70'],
        default => ['Gagal', 'bg-rose-50 text-rose-700 border-rose-200/70'],
    };
@endphp

<x-pwa-layout :title="$rpp->mata_pelajaran" active="rpp" :detail="$rpp">
    <x-slot name="header">
        <div class="relative z-10 pt-2">
            <div class="flex items-center gap-3.5">
                <a href="{{ route('pwa.rpp.index') }}" class="press flex h-11 w-11 shrink-0 items-center justify-center rounded-[18px] bg-white/12 backdrop-blur-md ring-1 ring-white/20 text-white" aria-label="Kembali">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="min-w-0 flex-1">
                    <p class="pwa-hero-eyebrow truncate">{{ $rpp->kurikulum }}</p>
                    <h1 class="pwa-display pwa-hero-title truncate">{{ $rpp->mata_pelajaran }}</h1>
                </div>
            </div>
            <p class="mt-2 text-[12.5px] leading-5 text-slate-200 line-clamp-2">{{ $rpp->topik }}</p>
        </div>
    </x-slot>

    <!-- Ringkasan Cepat Identitas Modul -->
    <section class="pwa-card pop-in p-5" style="--d: 40ms">
        <div class="flex items-center justify-between pb-3 border-b" style="border-color: var(--line)">
            <h2 class="pwa-h2">Identitas Modul</h2>
            <span class="pwa-badge border {{ $status[1] }}">{{ $status[0] }}</span>
        </div>

        <div class="mt-4 grid grid-cols-2 gap-3">
            @foreach ([
                ['Unit', $rpp->jenjang ?? 'MI'],
                ['Fase / Kelas', $rpp->fase.' / '.($rpp->kelas ?: '-')],
                ['Semester', $rpp->semester ?: '-'],
                ['Pertemuan', $rpp->jumlah_pertemuan.'× Pertemuan'],
                ['Alokasi Waktu', $rpp->alokasi_waktu],
                ['Dibuat', $rpp->created_at?->translatedFormat('d M Y') ?? '-'],
            ] as $i => [$label, $nilai])
                <div class="pop-in rounded-2xl p-3.5 border border-slate-100 bg-slate-50/80" style="--d: {{ 60 + $i * 30 }}ms">
                    <p class="pwa-sub text-[10.5px] font-bold uppercase tracking-wider text-slate-500">{{ $label }}</p>
                    <p class="mt-0.5 truncate text-[12.5px] font-bold text-slate-800">{{ $nilai }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-3 space-y-2 border-t pt-3 text-[12.5px]" style="border-color: var(--line)">
            <div class="flex gap-3">
                <span class="pwa-sub w-24 shrink-0 font-semibold text-slate-500">Model</span>
                <span class="flex-1 font-semibold text-slate-800">{{ $rpp->model_pembelajaran }}</span>
            </div>
            <div class="flex gap-3">
                <span class="pwa-sub w-24 shrink-0 font-semibold text-slate-500">Penyusun</span>
                <span class="flex-1 font-semibold text-slate-800">{{ $rpp->nama_guru }}</span>
            </div>
        </div>
    </section>

    @if ($rpp->status === 'completed')
        <a href="{{ route('rpp.edit', ['rpp' => $rpp, 'from' => 'pwa']) }}" class="pwa-card press pop-in flex items-center justify-center gap-3 p-4 font-bold text-slate-700 hover:text-blue-600 text-[13px]" style="--d: 80ms">
            <svg class="h-4 w-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
            Edit RPP &amp; Tema Dokumen
        </a>

        <section class="pwa-card pop-in p-5" style="--d: 100ms">
            <h2 class="pwa-h2">Unduh &amp; Cetak</h2>
            <div class="mt-4 grid grid-cols-3 gap-3">
                @foreach ([
                    ['PDF', route('rpp.pdf', $rpp), 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z', true],
                    ['Word', route('rpp.word', $rpp), 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z', false],
                    ['Cetak', route('rpp.print', $rpp), 'M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0 1 10.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0 .229 2.523a1.125 1.125 0 0 1-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0 0 21 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 0 0-1.913-.247M6.34 18H5.25A2.25 2.25 0 0 1 3 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 0 1 1.913-.247m10.5 0a48.536 48.536 0 0 0-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659', false],
                ] as $i => [$label, $url, $icon, $utama])
                    <a href="{{ $url }}" @if ($label === 'Cetak') target="_blank" @endif
                        class="press pop-in flex flex-col items-center gap-1.5 rounded-2xl py-3 text-[12px] font-bold"
                        style="--d: {{ 120 + $i * 40 }}ms; {{ $utama
                            ? 'background: linear-gradient(135deg, #1D4ED8, #2563EB); color: #FFFFFF; box-shadow: var(--sh-brand)'
                            : 'background: #F8FAFC; color: #1E293B; border: 1px solid #E2E8F0' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                        </svg>
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            <a href="{{ route('pwa.rpp.detail', $rpp) }}" class="press mt-3 block rounded-xl py-3 text-center text-[12.5px] font-bold border border-blue-200 bg-blue-50/80 text-blue-700 hover:bg-blue-100/80 transition">
                Buka Isi Modul Lengkap →
            </a>
        </section>
    @elseif ($rpp->status === 'processing')
        <section class="pwa-card pop-in p-7 text-center" style="--d: 80ms">
            <div class="relative mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-50 border border-amber-100 p-2">
                <div class="h-8 w-8 animate-spin rounded-full border-[3px] border-amber-200 border-t-amber-600"></div>
            </div>
            <p class="pwa-display text-[15px] font-extrabold text-slate-800">Modul Sedang Diproses AI</p>
            <p class="pwa-sub mt-1 text-[12px] text-slate-500">Halaman ini akan otomatis memuat ulang saat modul selesai.</p>
            <script>setTimeout(() => window.location.reload(), 5000);</script>
        </section>
    @else
        <section class="pwa-card pop-in p-7 text-center" style="--d: 80ms">
            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-rose-50 border border-rose-100 text-rose-600">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <p class="pwa-display text-[15px] font-extrabold text-rose-700">Penyusunan Modul Gagal</p>
            <p class="pwa-sub mt-1 text-[12px] leading-5 text-slate-500">{{ $rpp->failure_message ?: 'Terjadi kendala saat menyusun modul. Silakan buat ulang.' }}</p>
            <a href="{{ route('pwa.rpp.create') }}" class="press mt-4 inline-flex rounded-xl px-5 py-2.5 text-[12.5px] font-bold text-white shadow-sm"
                style="background: linear-gradient(135deg, #1D4ED8, #2563EB); box-shadow: var(--sh-brand)">Coba Lagi</a>
        </section>
    @endif

    @php $tujuan = data_get($rpp->content_result, 'komponen_inti.tujuan_pembelajaran') ?? data_get($rpp->content_result, 'desain_pembelajaran.tujuan_pembelajaran'); @endphp
    @if (is_array($tujuan) && $tujuan)
        <section class="pwa-card pop-in p-5" style="--d: 140ms">
            <h2 class="pwa-h2">Tujuan Pembelajaran</h2>
            <ol class="mt-4 space-y-3 text-[12.5px] leading-relaxed">
                @foreach (array_slice($tujuan, 0, 8) as $idx => $item)
                    <li class="flex items-start gap-3 p-3.5 rounded-2xl bg-slate-50/80 border border-slate-100">
                        <span class="mt-0.5 flex h-[22px] w-[22px] shrink-0 items-center justify-center rounded-[8px] text-[10px] font-extrabold bg-blue-100 text-blue-700">{{ $idx + 1 }}</span>
                        <span class="font-medium text-slate-700">{{ is_array($item) ? ($item['tujuan'] ?? reset($item)) : $item }}</span>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif
</x-pwa-layout>
