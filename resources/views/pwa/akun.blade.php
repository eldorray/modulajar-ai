<x-pwa-layout title="Akun" active="akun">
    <x-slot name="header">
        <div class="relative z-10 pt-4 text-center">
            <div class="mx-auto flex h-[76px] w-[76px] items-center justify-center rounded-[26px] bg-white/18 ring-1 ring-white/30">
                <span class="pwa-display text-[26px] font-extrabold">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
            </div>
            <h1 class="pwa-display mt-3 text-[19px] font-extrabold leading-tight">{{ $user->name }}</h1>
            <p class="text-[12px] text-white/80">{{ $user->email }}</p>
            <div class="mt-3 flex items-center justify-center gap-2">
                <span class="rounded-full bg-white/18 px-3 py-1.5 text-[11px] font-bold uppercase tracking-wide ring-1 ring-white/25">{{ $user->role }}</span>
                <span class="rounded-full bg-white/18 px-3 py-1.5 text-[11px] font-bold ring-1 ring-white/25">{{ $rppCount }} modul</span>
            </div>
        </div>
    </x-slot>

    <section class="pwa-card pop-in p-4">
        <h2 class="pwa-h2">Unit sekolah</h2>
        <div class="mt-3 space-y-2">
            @foreach ($units as $i => $unit)
                <div class="pop-in flex items-center gap-3 rounded-2xl p-3" style="--d: {{ 70 + $i * 60 }}ms; background: #F7FAFF">
                    @if ($unit->logo)
                        <img src="{{ Storage::url($unit->logo) }}" alt="" class="h-11 w-11 shrink-0 rounded-xl bg-white object-contain p-1" style="box-shadow: var(--sh-soft)">
                    @else
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-[12px] font-extrabold"
                            style="background: var(--brand-50); color: var(--brand-700)">{{ $unit->jenjang }}</span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[13px] font-bold">{{ $unit->nama_sekolah ?: 'Belum diisi' }}</p>
                        <p class="pwa-sub mt-0.5 text-[11px] font-medium">{{ $unit->jenjang }} · NPSN {{ $unit->npsn ?: '-' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="pwa-card pop-in overflow-hidden" style="--d: 130ms">
        @php
            $links = [
                ['Ubah profil', route('profile.edit'), 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM4.5 20a7.5 7.5 0 0 1 15 0'],
                ['Pengaturan sekolah', route('settings.index'), 'M4 7h16M4 12h16M4 17h10'],
                ['Dashboard desktop', route('dashboard'), 'M4 5.5h16v9.5H4zM9 19h6'],
            ];
            if ($user->isAdmin()) {
                $links[] = ['Pengaturan AI', route('admin.ai.edit'), 'M13 10V3L4 14h7v7l9-11h-7z'];
            }
        @endphp
        @foreach ($links as $i => [$label, $url, $icon])
            <a href="{{ $url }}" class="press flex items-center gap-3 px-4 py-3.5 {{ $i > 0 ? 'border-t' : '' }}" style="border-color: var(--line)">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl" style="background: var(--brand-50); color: var(--brand-700)">
                    <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                    </svg>
                </span>
                <span class="flex-1 text-[13px] font-bold">{{ $label }}</span>
                <svg class="h-4 w-4" style="color: #A9BBD6" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @endforeach
    </section>

    <form method="POST" action="{{ route('logout') }}" class="pop-in" style="--d: 190ms">
        @csrf
        <button type="submit" class="press w-full rounded-2xl py-3.5 text-[13px] font-bold"
            style="background: var(--rose-50); color: var(--rose)">
            Keluar
        </button>
    </form>
</x-pwa-layout>
