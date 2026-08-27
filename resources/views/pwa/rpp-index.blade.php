<x-pwa-layout title="Modul Ajar Saya" active="rpp">
    <x-slot name="header">
        <div class="relative z-10 flex items-end justify-between pt-3">
            <div>
                <p class="pwa-hero-eyebrow">Koleksi kamu</p>
                <h1 class="pwa-display pwa-hero-title">Modul Ajar</h1>
            </div>
            <span class="rounded-full bg-white/18 px-3 py-1.5 text-[11.5px] font-bold ring-1 ring-white/25">
                {{ $rpps->total() }} dokumen
            </span>
        </div>
    </x-slot>

    @forelse ($rpps as $i => $rpp)
        @php
            $status = match ($rpp->status) {
                'completed' => ['Selesai', 'mint'],
                'processing' => ['Proses', 'amber'],
                default => ['Gagal', 'rose'],
            };
        @endphp
        <a href="{{ route('pwa.rpp.show', $rpp) }}" class="pwa-card press pop-in flex items-start gap-3 p-3.5" style="--d: {{ $i * 55 }}ms">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-[13px]"
                style="background: var(--{{ $status[1] }}-50); color: var(--{{ $status[1] }})">
                <svg class="h-[19px] w-[19px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h8l4 4v12H7zM15 4v4h4" />
                </svg>
            </span>

            <span class="min-w-0 flex-1">
                <span class="flex items-start justify-between gap-2">
                    <span class="truncate text-[14px] font-bold">{{ $rpp->mata_pelajaran }}</span>
                    <span class="pwa-badge shrink-0" style="background: var(--{{ $status[1] }}-50); color: var(--{{ $status[1] }})">{{ $status[0] }}</span>
                </span>
                <span class="pwa-sub mt-0.5 block truncate text-[11.5px] font-medium">{{ $rpp->topik }}</span>

                <span class="mt-2 flex flex-wrap gap-1.5">
                    <span class="pwa-chip-meta">{{ $rpp->jenjang ?? 'MI' }}</span>
                    <span class="pwa-chip-meta">Fase {{ $rpp->fase }}</span>
                    @if ($rpp->kelas)
                        <span class="pwa-chip-meta">Kelas {{ $rpp->kelas }}</span>
                    @endif
                    <span class="pwa-chip-meta">{{ $rpp->created_at->translatedFormat('d M Y') }}</span>
                </span>
            </span>

            <svg class="mt-3 h-4 w-4 shrink-0" style="color: #A9BBD6" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    @empty
        <div class="pwa-card pop-in p-8 text-center">
            <img src="{{ asset('logo.png') }}" alt="" class="float mx-auto mb-4 h-16 w-16 object-contain">
            <p class="pwa-display text-[15px] font-extrabold">Belum ada modul ajar</p>
            <p class="pwa-sub mt-1 text-[12.5px] leading-5">Modul yang kamu buat akan tersimpan di sini.</p>
            <a href="{{ route('pwa.rpp.create') }}" class="press mt-5 inline-flex rounded-full px-6 py-3 text-[13px] font-bold text-white"
                style="background: linear-gradient(150deg, var(--brand-700), var(--brand-500)); box-shadow: var(--sh-brand)">
                Buat sekarang
            </a>
        </div>
    @endforelse

    @if ($rpps->hasPages())
        <div class="pwa-card px-3 py-2">
            {{ $rpps->onEachSide(1)->links() }}
        </div>
    @endif
</x-pwa-layout>
