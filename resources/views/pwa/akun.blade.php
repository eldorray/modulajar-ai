<x-pwa-layout title="Akun" active="akun">
    <x-slot name="header">
        <div class="pt-3 text-center">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-white/15 text-[26px] font-black ring-1 ring-white/25">
                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
            </div>
            <h1 class="mt-3 text-[19px] font-extrabold">{{ $user->name }}</h1>
            <p class="text-[12.5px] text-white/75">{{ $user->email }}</p>
            <span class="pwa-card-glass mt-3 inline-flex px-3 py-1.5 text-[11px] font-bold uppercase tracking-wide">
                {{ $user->role }} · {{ $rppCount }} modul
            </span>
        </div>
    </x-slot>

    <section class="pwa-card pop-in p-4">
        <h2 class="text-[15px] font-bold">Unit sekolah</h2>
        <div class="mt-3 space-y-2">
            @foreach ($units as $unit)
                <div class="flex items-center gap-3 rounded-xl bg-[#F8FBFF] p-3">
                    @if ($unit->logo)
                        <img src="{{ Storage::url($unit->logo) }}" alt="" class="h-10 w-10 rounded-lg bg-white object-contain p-1">
                    @else
                        <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#EAF2FF] text-[12px] font-bold text-[#0B4FD9]">{{ $unit->jenjang }}</span>
                    @endif
                    <div class="min-w-0">
                        <p class="truncate text-[13px] font-bold">{{ $unit->nama_sekolah ?: 'Belum diisi' }}</p>
                        <p class="text-[11.5px] text-[#7C90AF]">{{ $unit->jenjang }} · NPSN {{ $unit->npsn ?: '-' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="pwa-card pop-in divide-y divide-[#EDF2FA]" style="--d: 80ms">
        @php
            $links = [
                ['label' => 'Ubah profil', 'url' => route('profile.edit')],
                ['label' => 'Pengaturan sekolah', 'url' => route('settings.index')],
                ['label' => 'Dashboard desktop', 'url' => route('dashboard')],
            ];
            if ($user->isAdmin()) {
                $links[] = ['label' => 'Pengaturan AI', 'url' => route('admin.ai.edit')];
            }
        @endphp
        @foreach ($links as $link)
            <a href="{{ $link['url'] }}" class="press flex items-center justify-between px-4 py-3.5 text-[13.5px] font-semibold">
                {{ $link['label'] }}
                <svg class="h-4 w-4 text-[#9DB2D3]" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @endforeach
    </section>

    <form method="POST" action="{{ route('logout') }}" class="pop-in" style="--d: 140ms">
        @csrf
        <button type="submit" class="press w-full rounded-2xl bg-rose-50 py-3.5 text-[13.5px] font-bold text-rose-600">
            Keluar
        </button>
    </form>
</x-pwa-layout>
