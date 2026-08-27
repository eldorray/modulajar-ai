<x-pwa-layout title="Beranda" active="home">
    <x-slot name="header">
        <div class="flex items-start justify-between pt-3">
            <div>
                <p class="text-[13px] font-medium text-white/70">Assalamualaikum,</p>
                <h1 class="text-[22px] font-extrabold leading-tight">{{ auth()->user()->name }}</h1>
            </div>
            <a href="{{ route('pwa.akun') }}" class="press flex h-11 w-11 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/25">
                <span class="text-base font-bold">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            </a>
        </div>

        <div class="pwa-card-glass pop-in mt-5 px-4 py-4" style="--d: 60ms">
            <p class="text-[12px] font-medium text-white/75">Modul Ajar dibuat</p>
            <div class="mt-1 flex items-end gap-2">
                <span class="text-[34px] font-black leading-none">{{ $stats['total'] }}</span>
                <span class="pb-1 text-[13px] font-semibold text-white/80">dokumen</span>
            </div>
            <p class="mt-2 text-[12px] text-white/70">{{ $bulanIni }} dibuat bulan ini · {{ number_format($tokens, 0, ',', '.') }} token terpakai</p>
        </div>
    </x-slot>

    <!-- Ringkasan status -->
    <section class="pwa-card pop-in p-4" style="--d: 120ms">
        <div class="grid grid-cols-3 divide-x divide-[#EDF2FA] text-center">
            <div class="px-1">
                <p class="text-[22px] font-extrabold text-emerald-500">{{ $stats['completed'] }}</p>
                <p class="text-[11px] font-semibold text-[#7C90AF]">Selesai</p>
            </div>
            <div class="px-1">
                <p class="text-[22px] font-extrabold text-amber-500">{{ $stats['processing'] }}</p>
                <p class="text-[11px] font-semibold text-[#7C90AF]">Diproses</p>
            </div>
            <div class="px-1">
                <p class="text-[22px] font-extrabold text-rose-500">{{ $stats['failed'] }}</p>
                <p class="text-[11px] font-semibold text-[#7C90AF]">Gagal</p>
            </div>
        </div>
    </section>

    <!-- Menu cepat -->
    <section class="pwa-card pop-in p-4" style="--d: 180ms">
        <div class="grid grid-cols-4 gap-3 text-center">
            @php
                $menu = [
                    ['label' => 'Buat Modul', 'url' => route('pwa.rpp.create'), 'icon' => 'M12 4v16m8-8H4'],
                    ['label' => 'Modul Saya', 'url' => route('pwa.rpp.index'), 'icon' => 'M7 4h8l4 4v12H7zM15 4v4h4'],
                    ['label' => 'Akun', 'url' => route('pwa.akun'), 'icon' => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM4.5 20a7.5 7.5 0 0 1 15 0'],
                    ['label' => 'Desktop', 'url' => route('dashboard'), 'icon' => 'M4 5h16v10H4zM9 19h6'],
                ];
            @endphp
            @foreach ($menu as $i => $item)
                <a href="{{ $item['url'] }}" class="press pop-in flex flex-col items-center gap-2" style="--d: {{ 220 + $i * 60 }}ms">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#EAF2FF] text-[#0B4FD9] shadow-[0_6px_14px_-8px_rgba(11,79,217,.9)]">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                        </svg>
                    </span>
                    <span class="text-[11px] font-semibold leading-tight text-[#4A648C]">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Mapel terbanyak -->
    @if ($perMapel->isNotEmpty())
        <section class="pwa-card pop-in p-4" style="--d: 260ms">
            <h2 class="text-[15px] font-bold">Mata pelajaran teratas</h2>
            <div class="mt-3 space-y-3">
                @foreach ($perMapel as $mapel)
                    <div>
                        <div class="flex items-center justify-between text-[12px] font-semibold">
                            <span class="truncate pr-2 text-[#4A648C]">{{ $mapel->mata_pelajaran }}</span>
                            <span class="text-[#0B4FD9]">{{ $mapel->jumlah }}</span>
                        </div>
                        <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-[#EDF2FA]">
                            <div class="h-full rounded-full bg-gradient-to-r from-[#0B4FD9] to-[#2E90FA] transition-[width] duration-700"
                                style="width: {{ max(8, round($mapel->jumlah / max(1, $perMapel->max('jumlah')) * 100)) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Terbaru -->
    <section class="space-y-3">
        <div class="flex items-center justify-between px-1">
            <h2 class="text-[15px] font-bold">Terbaru</h2>
            <a href="{{ route('pwa.rpp.index') }}" class="text-[12px] font-bold text-[#0B4FD9]">Lihat semua</a>
        </div>

        @forelse ($recent as $i => $rpp)
            <a href="{{ route('pwa.rpp.show', $rpp) }}" class="pwa-card press pop-in flex items-center gap-3 p-3.5" style="--d: {{ 300 + $i * 70 }}ms">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-[#EAF2FF] text-[13px] font-extrabold text-[#0B4FD9]">
                    {{ $rpp->jenjang ?? 'MI' }}
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-[14px] font-bold">{{ $rpp->mata_pelajaran }}</span>
                    <span class="block truncate text-[12px] text-[#7C90AF]">{{ $rpp->topik }}</span>
                </span>
                @php
                    $badge = match ($rpp->status) {
                        'completed' => ['Selesai', 'bg-emerald-50 text-emerald-600'],
                        'processing' => ['Proses', 'bg-amber-50 text-amber-600'],
                        default => ['Gagal', 'bg-rose-50 text-rose-600'],
                    };
                @endphp
                <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $badge[1] }}">{{ $badge[0] }}</span>
            </a>
        @empty
            <div class="pwa-card pop-in p-6 text-center" style="--d: 300ms">
                <img src="{{ asset('logo.png') }}" alt="" class="float mx-auto mb-3 h-14 w-14 object-contain opacity-90">
                <p class="text-[14px] font-bold">Belum ada modul ajar</p>
                <p class="mt-1 text-[12px] text-[#7C90AF]">Tekan tombol biru di tengah untuk membuat yang pertama.</p>
                <a href="{{ route('pwa.rpp.create') }}" class="press mt-4 inline-flex rounded-full bg-[#0B4FD9] px-5 py-2.5 text-[13px] font-bold text-white shadow-[0_10px_22px_-10px_rgba(11,79,217,.95)]">
                    Buat modul ajar
                </a>
            </div>
        @endforelse
    </section>
</x-pwa-layout>
