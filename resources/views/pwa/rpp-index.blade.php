<x-pwa-layout title="Modul Ajar Saya" active="rpp">
    <x-slot name="header">
        <div class="flex items-center justify-between pt-3">
            <div>
                <p class="text-[13px] font-medium text-white/70">Koleksi kamu</p>
                <h1 class="text-[22px] font-extrabold leading-tight">Modul Ajar Saya</h1>
            </div>
            <span class="pwa-card-glass px-3 py-2 text-[12px] font-bold">{{ $rpps->total() }} dokumen</span>
        </div>
    </x-slot>

    @forelse ($rpps as $i => $rpp)
        @php
            $badge = match ($rpp->status) {
                'completed' => ['Selesai', 'bg-emerald-50 text-emerald-600'],
                'processing' => ['Diproses', 'bg-amber-50 text-amber-600'],
                default => ['Gagal', 'bg-rose-50 text-rose-600'],
            };
        @endphp
        <article class="pwa-card pop-in p-4" style="--d: {{ $i * 60 }}ms">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h2 class="truncate text-[15px] font-bold">{{ $rpp->mata_pelajaran }}</h2>
                    <p class="mt-0.5 line-clamp-2 text-[12px] leading-5 text-[#7C90AF]">{{ $rpp->topik }}</p>
                </div>
                <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold {{ $badge[1] }}">{{ $badge[0] }}</span>
            </div>

            <div class="mt-3 flex flex-wrap gap-1.5 text-[11px] font-semibold text-[#4A648C]">
                <span class="rounded-lg bg-[#F2F7FF] px-2 py-1">{{ $rpp->jenjang ?? 'MI' }}</span>
                <span class="rounded-lg bg-[#F2F7FF] px-2 py-1">Fase {{ $rpp->fase }}</span>
                @if ($rpp->kelas)
                    <span class="rounded-lg bg-[#F2F7FF] px-2 py-1">Kelas {{ $rpp->kelas }}</span>
                @endif
                <span class="rounded-lg bg-[#F2F7FF] px-2 py-1">{{ $rpp->created_at->translatedFormat('d M Y') }}</span>
            </div>

            <div class="mt-4 flex items-center gap-2">
                <a href="{{ route('pwa.rpp.show', $rpp) }}"
                    class="press flex-1 rounded-xl bg-[#0B4FD9] py-2.5 text-center text-[13px] font-bold text-white shadow-[0_10px_20px_-12px_rgba(11,79,217,.95)]">
                    Buka
                </a>
                @if ($rpp->status === 'completed')
                    <a href="{{ route('rpp.pdf', $rpp) }}"
                        class="press rounded-xl border border-[#DDE7F7] bg-[#F8FBFF] px-4 py-2.5 text-[13px] font-bold text-[#0B4FD9]">
                        PDF
                    </a>
                @endif
            </div>
        </article>
    @empty
        <div class="pwa-card pop-in p-7 text-center">
            <img src="{{ asset('logo.png') }}" alt="" class="float mx-auto mb-3 h-16 w-16 object-contain">
            <p class="text-[15px] font-bold">Belum ada modul ajar</p>
            <p class="mt-1 text-[12.5px] leading-5 text-[#7C90AF]">Modul yang kamu generate akan tersimpan di sini.</p>
            <a href="{{ route('pwa.rpp.create') }}"
                class="press mt-5 inline-flex rounded-full bg-[#0B4FD9] px-6 py-3 text-[13px] font-bold text-white shadow-[0_12px_24px_-10px_rgba(11,79,217,.95)]">
                Buat sekarang
            </a>
        </div>
    @endforelse

    @if ($rpps->hasPages())
        <div class="pwa-card p-3">
            {{ $rpps->links() }}
        </div>
    @endif
</x-pwa-layout>
