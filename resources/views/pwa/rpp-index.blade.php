<x-pwa-layout title="Modul Ajar Saya" active="rpp">
    <x-slot name="header">
        <div class="relative z-10 flex items-center justify-between pt-2">
            <div>
                <p class="pwa-hero-eyebrow">Koleksi Tersimpan</p>
                <h1 class="pwa-display pwa-hero-title">Modul Ajar</h1>
            </div>
            <span class="rounded-full bg-white/15 px-3 py-1.5 text-[11.5px] font-bold text-white backdrop-blur-md ring-1 ring-white/25 shadow-xs">
                {{ $rpps->total() }} dokumen
            </span>
        </div>
    </x-slot>

    @forelse ($rpps as $i => $rpp)
        @php
            $status = match ($rpp->status) {
                'completed' => ['Selesai', 'bg-emerald-50 text-emerald-700 border-emerald-200/70'],
                'processing' => ['Proses', 'bg-amber-50 text-amber-700 border-amber-200/70'],
                default => ['Gagal', 'bg-rose-50 text-rose-700 border-rose-200/70'],
            };
        @endphp
        <a href="{{ route('pwa.rpp.show', $rpp) }}" class="pwa-card press pop-in flex items-start gap-4 p-4" style="--d: {{ min(180, $i * 35) }}ms">
            <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-[18px] bg-blue-50/80 text-blue-600 border border-blue-100/70 shadow-xs mt-0.5">
                <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                </svg>
            </span>

            <span class="min-w-0 flex-1">
                <span class="flex items-start justify-between gap-3">
                    <span class="truncate text-[14px] font-bold text-slate-900">{{ $rpp->mata_pelajaran }}</span>
                    <span class="pwa-badge shrink-0 border {{ $status[1] }}">{{ $status[0] }}</span>
                </span>
                <span class="pwa-sub mt-0.5 block truncate text-[11.5px] font-medium text-slate-500">{{ $rpp->topik }}</span>

                <span class="mt-2.5 flex flex-wrap gap-1.5">
                    <span class="pwa-chip-meta">{{ $rpp->jenjang ?? 'MI' }}</span>
                    <span class="pwa-chip-meta">Fase {{ $rpp->fase }}</span>
                    @if ($rpp->kelas)
                        <span class="pwa-chip-meta">Kelas {{ $rpp->kelas }}</span>
                    @endif
                    <span class="pwa-chip-meta">{{ $rpp->created_at?->translatedFormat('d M Y') ?? '-' }}</span>
                </span>
            </span>

            <svg class="mt-4 h-4 w-4 shrink-0 text-slate-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    @empty
        <div class="pwa-card pop-in p-8 text-center" style="--d: 80ms">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-[20px] bg-slate-50 border border-slate-100 p-2">
                <img src="{{ asset('logo.png') }}" alt="" class="h-10 w-10 object-contain">
            </div>
            <p class="pwa-display text-[15px] font-extrabold text-slate-800">Belum Ada Modul Ajar</p>
            <p class="pwa-sub mt-1 text-[12px] leading-5 text-slate-500">Modul ajar yang kamu buat dengan AI akan tersimpan di sini.</p>
            <a href="{{ route('pwa.rpp.create') }}" class="press mt-5 inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-[12.5px] font-bold text-white shadow-md"
                style="background: linear-gradient(135deg, #1D4ED8, #2563EB); box-shadow: var(--sh-brand)">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Buat Sekarang
            </a>
        </div>
    @endforelse

    @if ($rpps->hasPages())
        <div class="pwa-card pop-in p-4" style="--d: 200ms">
            {{ $rpps->onEachSide(1)->links() }}
        </div>
    @endif
</x-pwa-layout>
