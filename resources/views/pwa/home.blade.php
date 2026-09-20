<x-pwa-layout title="Beranda" active="home">
    <x-slot name="header">
        <div class="relative z-10 flex items-center justify-between pt-1">
            <div>
                <p class="text-[12.5px] font-medium text-blue-200/90">Assalamualaikum,</p>
                <h1 class="pwa-display text-[21px] font-black leading-tight text-white">{{ auth()->user()->name }}</h1>
            </div>
            <a href="{{ route('pwa.akun') }}" class="press flex h-11 w-11 items-center justify-center rounded-2xl bg-white/15 backdrop-blur-md ring-1 ring-white/25 text-white shadow-sm" aria-label="Profil akun">
                <span class="pwa-display text-[16px] font-extrabold text-white">{{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}</span>
            </a>
        </div>

        <!-- Kartu Total Dokumen (Frosted Glass di dalam Header) -->
        <div class="pwa-card-glass pop-in mt-5 p-5 text-white" style="--d: 40ms">
            <p class="text-[11px] font-bold tracking-wider text-blue-200/80 uppercase">Modul Ajar Saya</p>
            <div class="mt-1 flex items-baseline gap-2">
                <span class="pwa-display text-[34px] font-black leading-none text-white">{{ $stats['total'] }}</span>
                <span class="text-[13px] font-medium text-blue-100/80">dokumen</span>
            </div>
            <p class="mt-2 text-[11.5px] text-blue-100/75">
                <span class="font-bold text-white">{{ $bulanIni }}</span> dibuat bulan ini · <span class="font-bold text-white">{{ number_format($tokens, 0, ',', '.') }}</span> token AI terpakai
            </p>
        </div>
    </x-slot>

    <!-- Ringkasan Status 3 Kolom Minimalis & Elegan -->
    <section class="pwa-card pop-in p-5" style="--d: 80ms">
        <div class="grid grid-cols-3 divide-x divide-slate-100 text-center">
            <div class="px-1">
                <div class="flex items-center justify-center gap-1.5 text-emerald-600">
                    <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                    <p class="pwa-display text-[22px] font-extrabold leading-none">{{ $stats['completed'] }}</p>
                </div>
                <p class="mt-1 text-[11px] font-bold text-slate-500">Selesai</p>
            </div>
            <div class="px-1">
                <div class="flex items-center justify-center gap-1.5 text-amber-600">
                    @if ($stats['processing'] > 0)
                        <span class="h-2 w-2 rounded-full bg-amber-500 animate-ping"></span>
                    @else
                        <span class="h-2 w-2 rounded-full bg-amber-400"></span>
                    @endif
                    <p class="pwa-display text-[22px] font-extrabold leading-none">{{ $stats['processing'] }}</p>
                </div>
                <p class="mt-1 text-[11px] font-bold text-slate-500">Diproses</p>
            </div>
            <div class="px-1">
                <div class="flex items-center justify-center gap-1.5 {{ $stats['failed'] > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                    <span class="h-2 w-2 rounded-full {{ $stats['failed'] > 0 ? 'bg-rose-500' : 'bg-slate-300' }}"></span>
                    <p class="pwa-display text-[22px] font-extrabold leading-none">{{ $stats['failed'] }}</p>
                </div>
                <p class="mt-1 text-[11px] font-bold text-slate-500">Gagal</p>
            </div>
        </div>
    </section>

    <!-- Menu Cepat 4-Kolom -->
    <section class="pwa-card pop-in p-5" style="--d: 120ms">
        <div class="grid grid-cols-4 gap-2 text-center">
            @php
                $menu = [
                    ['Buat Modul', route('pwa.rpp.create'), 'bg-blue-50 text-blue-600 border-blue-100/80', 'M12 4.5v15m7.5-7.5h-15'],
                    ['Koleksi', route('pwa.rpp.index'), 'bg-indigo-50 text-indigo-600 border-indigo-100/80', 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z'],
                    ['Sekolah', route('settings.index'), 'bg-emerald-50 text-emerald-600 border-emerald-100/80', 'M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342'],
                    ['Profil', route('pwa.akun'), 'bg-amber-50 text-amber-600 border-amber-100/80', 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z'],
                ];
            @endphp
            @foreach ($menu as $i => [$label, $url, $themeClasses, $icon])
                <a href="{{ $url }}" class="press pop-in flex flex-col items-center gap-1.5" style="--d: {{ 120 + $i * 30 }}ms">
                    <span class="flex h-12 w-12 items-center justify-center rounded-2xl border shadow-xs {{ $themeClasses }}">
                        <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                        </svg>
                    </span>
                    <span class="text-[11.5px] font-bold text-slate-700">{{ $label }}</span>
                </a>
            @endforeach
        </div>
    </section>

    <!-- Ajakan Generate AI Modern Card -->
    <a href="{{ route('pwa.rpp.create') }}" class="press pop-in relative block overflow-hidden rounded-[20px] p-5 text-white shadow-md" style="--d: 160ms;
        background: radial-gradient(100% 120% at 100% 0%, rgba(96, 165, 250, 0.35) 0%, transparent 60%), linear-gradient(135deg, #1E3A8A 0%, #1D4ED8 60%, #2563EB 100%);
        box-shadow: 0 12px 28px -8px rgba(29, 78, 216, 0.45)">
        <div class="flex items-center gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[18px] bg-white/15 backdrop-blur-md p-2 ring-1 ring-white/25">
                <img src="{{ asset('logo.png') }}" alt="" class="h-full w-full object-contain">
            </div>
            <div class="min-w-0 flex-1">
                <span class="inline-flex items-center rounded-full bg-blue-400/25 px-2 py-0.5 text-[10px] font-bold tracking-wide text-blue-200">AI GENERATOR</span>
                <p class="pwa-display text-[15px] font-extrabold leading-tight text-white mt-0.5">Susun Modul Ajar Baru</p>
                <p class="mt-0.5 text-[11.5px] leading-4 text-blue-100/80 truncate">Tentukan topik & CP, AI susun sintaks dan asesmen.</p>
            </div>
            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl bg-white/20 text-white shadow-xs">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </span>
        </div>
    </a>

    <!-- Sebaran mata pelajaran -->
    @if ($perMapel->isNotEmpty())
        @php
            $maks = max(1, $perMapel->max('jumlah'));
            $warnaGradients = [
                'from-blue-600 to-indigo-600',
                'from-indigo-600 to-violet-600',
                'from-emerald-600 to-teal-500',
                'from-amber-500 to-orange-500',
            ];
        @endphp
        <section class="pwa-card pop-in p-5" style="--d: 180ms">
            <div class="flex items-center justify-between pb-3 border-b" style="border-color: var(--line)">
                <h2 class="pwa-h2">Mata Pelajaran Teratas</h2>
                <span class="pwa-chip-meta">{{ $perMapel->count() }} mapel</span>
            </div>
            <div class="mt-3.5 space-y-3">
                @foreach ($perMapel as $i => $mapel)
                    <div>
                        <div class="flex items-baseline justify-between gap-2">
                            <span class="truncate text-[12.5px] font-bold text-slate-800">{{ $mapel->mata_pelajaran }}</span>
                            <span class="pwa-display shrink-0 text-[12.5px] font-extrabold text-blue-600">{{ $mapel->jumlah }} modul</span>
                        </div>
                        <div class="mt-1.5 h-[6.5px] w-full overflow-hidden rounded-full bg-slate-100">
                            <div class="grow-bar h-full rounded-full bg-gradient-to-r {{ $warnaGradients[$i % 4] }}"
                                style="--d: {{ 200 + $i * 50 }}ms; width: {{ max(8, round($mapel->jumlah / $maks * 100)) }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Modul Terbaru -->
    <section class="space-y-3 pt-2">
        <div class="flex items-center justify-between px-1 mb-2">
            <h2 class="pwa-h2">Modul Terbaru</h2>
            <a href="{{ route('pwa.rpp.index') }}" class="text-[12px] font-bold text-blue-600 hover:text-blue-700">Lihat Semua →</a>
        </div>

        @forelse ($recent as $i => $rpp)
            @php
                $status = match ($rpp->status) {
                    'completed' => ['Selesai', 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                    'processing' => ['Proses', 'bg-amber-50 text-amber-700 border-amber-200'],
                    default => ['Gagal', 'bg-rose-50 text-rose-700 border-rose-200'],
                };
            @endphp
            <a href="{{ route('pwa.rpp.show', $rpp) }}" class="pwa-card press pop-in flex items-center gap-4 p-4" style="--d: {{ 200 + $i * 45 }}ms">
                <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[18px] bg-blue-50/80 text-blue-600 border border-blue-100/60 shadow-xs">
                    <svg class="h-[20px] w-[20px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </span>
                <span class="min-w-0 flex-1">
                    <span class="block truncate text-[13.5px] font-bold text-slate-900">{{ $rpp->mata_pelajaran }}</span>
                    <span class="pwa-sub mt-0.5 block truncate text-[11.5px] font-medium text-slate-500">
                        Fase {{ $rpp->fase }} · {{ $rpp->created_at?->translatedFormat('d M') ?? '-' }} · {{ $rpp->jenjang ?? 'MI' }}
                    </span>
                </span>
                <span class="pwa-badge shrink-0 border {{ $status[1] }}">{{ $status[0] }}</span>
            </a>
        @empty
            <div class="pwa-card pop-in p-8 text-center" style="--d: 200ms">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-50 border border-slate-100 p-2">
                    <img src="{{ asset('logo.png') }}" alt="" class="h-10 w-10 object-contain">
                </div>
                <p class="pwa-display text-[14.5px] font-extrabold text-slate-800">Belum Ada Modul Ajar</p>
                <p class="pwa-sub mt-1 text-[12px] leading-5 text-slate-500">Ketuk tombol (+) di dock bawah untuk membuat modul pertamamu.</p>
            </div>
        @endforelse
    </section>
</x-pwa-layout>
