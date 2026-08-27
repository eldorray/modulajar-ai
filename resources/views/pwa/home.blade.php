@php
    $persenSelesai = $stats['total'] > 0 ? (int) round($stats['completed'] / $stats['total'] * 100) : 0;
@endphp

<x-pwa-layout title="Beranda" active="home">
    <x-slot name="header">
        <div class="relative z-10 flex items-center gap-3 pt-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-white/18 ring-1 ring-white/30">
                <span class="pwa-display text-[15px] font-extrabold">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="pwa-hero-eyebrow">{{ now()->translatedFormat('l, d F Y') }}</p>
                <h1 class="pwa-display pwa-hero-title truncate">{{ auth()->user()->name }}</h1>
            </div>
            <a href="{{ route('pwa.akun') }}" class="press flex h-9 w-9 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/25">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
    </x-slot>

    <!-- Kartu ringkasan: angka besar + cincin progres + status -->
    <section class="pwa-card pop-in p-4">
        <div class="flex items-center gap-4">
            <div class="min-w-0 flex-1">
                <p class="pwa-sub text-[11.5px] font-bold uppercase tracking-wide">Modul ajar dibuat</p>
                <div class="mt-1 flex items-end gap-1.5">
                    <span class="pwa-display text-[38px] font-extrabold leading-none">{{ $stats['total'] }}</span>
                    <span class="pwa-sub pb-1.5 text-[12px] font-semibold">dokumen</span>
                </div>
                <p class="pwa-sub mt-1.5 text-[11.5px] font-medium">
                    {{ $bulanIni }} bulan ini · {{ number_format($tokens, 0, ',', '.') }} token
                </p>
            </div>

            <div class="pwa-ring shrink-0" style="--p: {{ $persenSelesai }}" role="img"
                aria-label="{{ $persenSelesai }} persen selesai">
                <span>{{ $persenSelesai }}%</span>
            </div>
        </div>

        <div class="mt-4 grid grid-cols-3 gap-2">
            @foreach ([
                ['Selesai', $stats['completed'], 'mint'],
                ['Proses', $stats['processing'], 'amber'],
                ['Gagal', $stats['failed'], 'rose'],
            ] as $i => [$label, $nilai, $warna])
                <div class="pop-in rounded-2xl px-2.5 py-2.5 text-center" style="--d: {{ 90 + $i * 60 }}ms; background: var(--{{ $warna }}-50)">
                    <p class="pwa-display text-[19px] font-extrabold leading-none" style="color: var(--{{ $warna }})">{{ $nilai }}</p>
                    <p class="mt-1 text-[10.5px] font-bold" style="color: var(--{{ $warna }})">{{ $label }}</p>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Menu cepat -->
    <section class="pwa-card pop-in px-3 py-4" style="--d: 120ms">
        <div class="grid grid-cols-4 gap-1">
            @php
                $menu = [
                    ['Buat modul', route('pwa.rpp.create'), 'brand', 'M12 5v14M5 12h14'],
                    ['Modul saya', route('pwa.rpp.index'), 'violet', 'M7 4h8l4 4v12H7zM15 4v4h4M10 13h6M10 16.5h4'],
                    ['Sekolah', route('settings.index'), 'mint', 'M4 20V9.5L12 4l8 5.5V20M9.5 20v-6h5v6'],
                    ['Akun', route('pwa.akun'), 'amber', 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM4.5 20a7.5 7.5 0 0 1 15 0'],
                ];
            @endphp
            @foreach ($menu as $i => [$label, $url, $warna, $icon])
                <a href="{{ $url }}" class="press pop-in flex flex-col items-center gap-2 rounded-2xl py-1.5" style="--d: {{ 150 + $i * 55 }}ms">
                    <span class="flex h-12 w-12 items-center justify-center rounded-[16px]"
                        style="background: var(--{{ $warna === 'brand' ? 'brand-50' : $warna . '-50' }}); color: var(--{{ $warna === 'brand' ? 'brand-700' : $warna }}); box-shadow: var(--sh-soft)">
                        <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                        </svg>
                    </span>
                    <span class="text-[10.5px] font-bold leading-none" style="color: var(--ink-soft)">{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Ajakan generate -->
    <a href="{{ route('pwa.rpp.create') }}" class="press pop-in block overflow-hidden rounded-[20px] p-4 text-white" style="--d: 200ms;
        background: radial-gradient(120% 140% at 90% 0%, rgba(255,255,255,.22) 0%, rgba(255,255,255,0) 55%), linear-gradient(150deg, var(--brand-900), var(--brand-700) 60%, var(--violet));
        box-shadow: var(--sh-brand)">
        <div class="flex items-center gap-3">
            <img src="{{ asset('logo.png') }}" alt="" class="float h-11 w-11 shrink-0 rounded-xl bg-white/90 object-contain p-1.5">
            <div class="min-w-0 flex-1">
                <p class="pwa-display text-[14.5px] font-extrabold leading-tight">Susun modul ajar baru</p>
                <p class="mt-0.5 text-[11.5px] leading-4 text-white/80">Isi topik dan alokasi waktu, AI menyusun sisanya.</p>
            </div>
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-white/20">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        </div>
    </a>

    <!-- Sebaran mata pelajaran -->
    @if ($perMapel->isNotEmpty())
        @php $maks = max(1, $perMapel->max('jumlah')); $warnaBar = ['brand-700', 'violet', 'mint', 'amber']; @endphp
        <section class="pwa-card pop-in p-4" style="--d: 240ms">
            <div class="flex items-center justify-between">
                <h2 class="pwa-h2">Mata pelajaran teratas</h2>
                <span class="pwa-chip-meta">{{ $perMapel->count() }} mapel</span>
            </div>
            <div class="mt-3.5 space-y-3">
                @foreach ($perMapel as $i => $mapel)
                    <div>
                        <div class="flex items-baseline justify-between gap-2">
                            <span class="truncate text-[12.5px] font-bold" style="color: var(--ink-soft)">{{ $mapel->mata_pelajaran }}</span>
                            <span class="pwa-display shrink-0 text-[12.5px] font-extrabold" style="color: var(--{{ $warnaBar[$i % 4] }})">{{ $mapel->jumlah }}</span>
                        </div>
                        <div class="mt-1.5 h-[7px] w-full overflow-hidden rounded-full" style="background: #F1F5FD">
                            <div class="grow-bar h-full rounded-full" style="--d: {{ 280 + $i * 90 }}ms; width: {{ max(10, round($mapel->jumlah / $maks * 100)) }}%;
                                background: linear-gradient(90deg, var(--{{ $warnaBar[$i % 4] }}), color-mix(in srgb, var(--{{ $warnaBar[$i % 4] }}) 55%, white))"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Terbaru -->
    <section class="space-y-2.5 pt-1">
        <div class="flex items-center justify-between px-1">
            <h2 class="pwa-h2">Terbaru</h2>
            <a href="{{ route('pwa.rpp.index') }}" class="text-[12px] font-bold" style="color: var(--brand-700)">Lihat semua</a>
        </div>

        @forelse ($recent as $i => $rpp)
            @php
                $status = match ($rpp->status) {
                    'completed' => ['Selesai', 'mint'],
                    'processing' => ['Proses', 'amber'],
                    default => ['Gagal', 'rose'],
                };
            @endphp
            <a href="{{ route('pwa.rpp.show', $rpp) }}" class="pwa-card press pop-in flex items-center gap-3 p-3" style="--d: {{ 300 + $i * 65 }}ms">
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[13px]"
                    style="background: var(--{{ $status[1] }}-50); color: var(--{{ $status[1] }})">
                    <svg class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h8l4 4v12H7zM15 4v4h4" />
                    </svg>
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-[13.5px] font-bold">{{ $rpp->mata_pelajaran }}</span>
                    <span class="pwa-sub mt-0.5 block truncate text-[11.5px] font-medium">
                        Fase {{ $rpp->fase }} · {{ $rpp->created_at->translatedFormat('d M') }} · {{ $rpp->jenjang ?? 'MI' }}
                    </span>
                </span>
                <span class="pwa-badge shrink-0" style="background: var(--{{ $status[1] }}-50); color: var(--{{ $status[1] }})">{{ $status[0] }}</span>
            </a>
        @empty
            <div class="pwa-card pop-in p-7 text-center" style="--d: 300ms">
                <img src="{{ asset('logo.png') }}" alt="" class="float mx-auto mb-3 h-14 w-14 object-contain">
                <p class="pwa-display text-[14.5px] font-extrabold">Belum ada modul ajar</p>
                <p class="pwa-sub mt-1 text-[12px] leading-5">Ketuk tombol logo di tengah untuk membuat yang pertama.</p>
            </div>
        @endforelse
    </section>
</x-pwa-layout>
