<x-pwa-layout title="Akun" active="akun">
    <x-slot name="header">
        <div class="relative z-10 pt-3 text-center">
            <div class="mx-auto flex h-[76px] w-[76px] items-center justify-center rounded-[24px] bg-white/12 backdrop-blur-md ring-1 ring-white/25 shadow-sm">
                <span class="pwa-display text-[26px] font-extrabold text-white">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
            </div>
            <h1 class="pwa-display mt-3 text-[19px] font-extrabold leading-tight text-white">{{ $user->name }}</h1>
            <p class="text-[12px] text-slate-300">{{ $user->email }}</p>
            <div class="mt-3 flex items-center justify-center gap-2">
                <span class="rounded-full bg-white/15 px-3 py-1 text-[10.5px] font-bold uppercase tracking-wider text-white backdrop-blur-sm ring-1 ring-white/20">{{ $user->role }}</span>
                <span class="rounded-full bg-white/15 px-3 py-1 text-[10.5px] font-bold text-white backdrop-blur-sm ring-1 ring-white/20">{{ $rppCount }} modul dibuat</span>
            </div>
        </div>
    </x-slot>

    <!-- Unit Sekolah Guru -->
    <section class="pwa-card pop-in p-4" style="--d: 40ms">
        <h2 class="pwa-h2">Unit Sekolah Terdaftar</h2>
        <div class="mt-3 space-y-2">
            @foreach ($units as $i => $unit)
                <div class="pop-in flex items-center gap-3.5 rounded-2xl p-3 border border-slate-100 bg-slate-50/80" style="--d: {{ 60 + $i * 30 }}ms">
                    @if ($unit->logo)
                        <img src="{{ Storage::url($unit->logo) }}" alt="" class="h-11 w-11 shrink-0 rounded-xl bg-white object-contain p-1 border border-slate-200/60 shadow-xs">
                    @else
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-[12px] font-extrabold bg-blue-100 text-blue-700 border border-blue-200/60">
                            {{ $unit->jenjang }}
                        </span>
                    @endif
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-[13px] font-bold text-slate-900">{{ $unit->nama_sekolah ?: 'Belum Diisi' }}</p>
                        <p class="pwa-sub mt-0.5 text-[11px] font-medium text-slate-500">{{ $unit->jenjang }} · NPSN: {{ $unit->npsn ?: '-' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <!-- Menu & Pengaturan -->
    <section class="pwa-card pop-in overflow-hidden" style="--d: 80ms">
        @php
            $links = [
                ['Ubah Profil Akun', route('pwa.profil'), 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z', false],
                ['Pengaturan Kop & Sekolah', route('pwa.kop'), 'M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.332A48.36 48.36 0 0 0 12 9.75c-2.551 0-5.056.2-7.5.582V21M3 21h18M12 6.75h.008v.008H12V6.75Z', false],
                ['Buka Dashboard Desktop', route('dashboard'), 'M9 17.25v1.007a3 3 0 0 1-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0 1 15 18.257V17.25m6-12V15a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 15V5.25m18 0A2.25 2.25 0 0 0 18.75 3H5.25A2.25 2.25 0 0 0 3 5.25m18 0V12a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 1 3 12V5.25', true],
            ];
            if ($user->isAdmin()) {
                $links[] = ['Pengaturan AI Admin', route('admin.ai.edit'), 'M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z', false];
            }
        @endphp
        @foreach ($links as $i => [$label, $url, $icon, $hideStandalone])
            <a href="{{ $url }}" class="press flex items-center gap-3.5 px-4 py-3.5 {{ $i > 0 ? 'border-t' : '' }}"
                style="border-color: var(--line)" @if ($hideStandalone) data-hide-standalone @endif>
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-blue-50 text-blue-600 border border-blue-100/80">
                    <svg class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                    </svg>
                </span>
                <span class="flex-1 text-[13px] font-bold text-slate-800">{{ $label }}</span>
                <svg class="h-4 w-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        @endforeach
    </section>

    <!-- Tombol Logout -->
    <form method="POST" action="{{ route('logout') }}" class="pop-in" style="--d: 120ms">
        @csrf
        <button type="submit" class="press w-full rounded-2xl py-3.5 text-[13px] font-bold flex items-center justify-center gap-2 border border-rose-200/80 bg-rose-50/80 text-rose-700 shadow-xs hover:bg-rose-100 transition">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
            </svg>
            Keluar dari Akun
        </button>
    </form>
</x-pwa-layout>
