@php
    $status = match ($rpp->status) {
        'completed' => ['Selesai', 'mint'],
        'processing' => ['Diproses', 'amber'],
        default => ['Gagal', 'rose'],
    };
@endphp

<x-pwa-layout :title="$rpp->mata_pelajaran" active="rpp" :detail="$rpp">
    <x-slot name="header">
        <div class="relative z-10 pt-3">
            <div class="flex items-center gap-3">
                <a href="{{ route('pwa.rpp.index') }}" class="press flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/25">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div class="min-w-0 flex-1">
                    <p class="pwa-hero-eyebrow truncate">{{ $rpp->kurikulum }}</p>
                    <h1 class="pwa-display pwa-hero-title truncate">{{ $rpp->mata_pelajaran }}</h1>
                </div>
            </div>
            <p class="mt-2.5 text-[12.5px] leading-5 text-white/85">{{ $rpp->topik }}</p>
        </div>
    </x-slot>

    <!-- Ringkasan cepat -->
    <section class="pwa-card pop-in p-4">
        <div class="flex items-center justify-between">
            <h2 class="pwa-h2">Identitas</h2>
            <span class="pwa-badge" style="background: var(--{{ $status[1] }}-50); color: var(--{{ $status[1] }})">{{ $status[0] }}</span>
        </div>

        <div class="mt-3.5 grid grid-cols-2 gap-2">
            @foreach ([
                ['Unit', $rpp->jenjang ?? 'MI'],
                ['Fase / Kelas', $rpp->fase.' / '.($rpp->kelas ?: '-')],
                ['Semester', $rpp->semester ?: '-'],
                ['Pertemuan', $rpp->jumlah_pertemuan.'×'],
                ['Alokasi', $rpp->alokasi_waktu],
                ['Dibuat', $rpp->created_at->translatedFormat('d M Y')],
            ] as $i => [$label, $nilai])
                <div class="pop-in rounded-2xl px-3 py-2.5" style="--d: {{ 80 + $i * 45 }}ms; background: #F7FAFF">
                    <p class="pwa-sub text-[10.5px] font-bold uppercase tracking-wide">{{ $label }}</p>
                    <p class="mt-0.5 truncate text-[12.5px] font-bold">{{ $nilai }}</p>
                </div>
            @endforeach
        </div>

        <div class="mt-3 space-y-2 border-t pt-3 text-[12.5px]" style="border-color: var(--line)">
            <div class="flex gap-3">
                <span class="pwa-sub w-24 shrink-0 font-semibold">Model</span>
                <span class="flex-1 font-semibold">{{ $rpp->model_pembelajaran }}</span>
            </div>
            <div class="flex gap-3">
                <span class="pwa-sub w-24 shrink-0 font-semibold">Penyusun</span>
                <span class="flex-1 font-semibold">{{ $rpp->nama_guru }}</span>
            </div>
        </div>
    </section>

    @if ($rpp->status === 'completed')
        <section class="pwa-card pop-in p-4" style="--d: 120ms">
            <h2 class="pwa-h2">Unduh &amp; cetak</h2>
            <div class="mt-3 grid grid-cols-3 gap-2">
                @foreach ([
                    ['PDF', route('rpp.pdf', $rpp), 'M7 4h8l4 4v12H7zM15 4v4h4', true],
                    ['Word', route('rpp.word', $rpp), 'M5 5h14v14H5zM9 9l1.5 6L12 11l1.5 4L15 9', false],
                    ['Cetak', route('rpp.print', $rpp), 'M7 9V4h10v5M7 15h10v5H7zM5 9h14v6H5z', false],
                ] as $i => [$label, $url, $icon, $utama])
                    <a href="{{ $url }}" @if ($label === 'Cetak') target="_blank" @endif
                        class="press pop-in flex flex-col items-center gap-1.5 rounded-2xl py-3 text-[12px] font-bold"
                        style="--d: {{ 140 + $i * 55 }}ms; {{ $utama
                            ? 'background: linear-gradient(150deg, var(--brand-700), var(--brand-500)); color: #fff; box-shadow: var(--sh-brand)'
                            : 'background: #F7FAFF; color: var(--brand-700); box-shadow: inset 0 0 0 1px var(--line)' }}">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                        </svg>
                        {{ $label }}
                    </a>
                @endforeach
            </div>
            <a href="{{ route('rpp.show', $rpp) }}" class="press mt-2.5 block rounded-2xl py-3 text-center text-[12.5px] font-bold"
                style="background: var(--brand-50); color: var(--brand-700)">
                Buka versi lengkap
            </a>
        </section>
    @elseif ($rpp->status === 'processing')
        <section class="pwa-card pop-in p-6 text-center" style="--d: 120ms">
            <div class="mx-auto mb-3 h-10 w-10 animate-spin rounded-full border-[3px]" style="border-color: var(--brand-50); border-top-color: var(--brand-700)"></div>
            <p class="pwa-display text-[14.5px] font-extrabold">Masih diproses</p>
            <p class="pwa-sub mt-1 text-[12px]">Muat ulang halaman ini beberapa saat lagi.</p>
        </section>
    @else
        <section class="pwa-card pop-in p-6 text-center" style="--d: 120ms">
            <p class="pwa-display text-[14.5px] font-extrabold" style="color: var(--rose)">Generate gagal</p>
            <p class="pwa-sub mt-1 text-[12px] leading-5">Buat ulang modul dengan data yang sama.</p>
            <a href="{{ route('pwa.rpp.create') }}" class="press mt-4 inline-flex rounded-full px-5 py-2.5 text-[12.5px] font-bold text-white"
                style="background: linear-gradient(150deg, var(--brand-700), var(--brand-500)); box-shadow: var(--sh-brand)">Coba lagi</a>
        </section>
    @endif

    @php $tujuan = data_get($rpp->content_result, 'komponen_inti.tujuan_pembelajaran') ?? data_get($rpp->content_result, 'desain_pembelajaran.tujuan_pembelajaran'); @endphp
    @if (is_array($tujuan) && $tujuan)
        <section class="pwa-card pop-in p-4" style="--d: 180ms">
            <h2 class="pwa-h2">Tujuan pembelajaran</h2>
            <ol class="mt-3 space-y-2.5 text-[12.5px] leading-5">
                @foreach (array_slice($tujuan, 0, 8) as $idx => $item)
                    <li class="flex gap-2.5">
                        <span class="mt-px flex h-5 w-5 shrink-0 items-center justify-center rounded-lg text-[10px] font-extrabold"
                            style="background: var(--brand-50); color: var(--brand-700)">{{ $idx + 1 }}</span>
                        <span class="font-medium">{{ is_array($item) ? ($item['tujuan'] ?? reset($item)) : $item }}</span>
                    </li>
                @endforeach
            </ol>
        </section>
    @endif
</x-pwa-layout>
