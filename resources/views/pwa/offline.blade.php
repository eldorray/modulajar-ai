<x-pwa-layout title="Offline" active="home" :show-install-banner="false">
    <div class="pop-in mt-12 text-center">
        <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-3xl bg-white shadow-md shadow-slate-900/5 ring-1 ring-slate-900/5">
            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.166a1 1 0 111.414 1.414M3 3l18 18" />
                </svg>
            </div>
        </div>

        <div class="inline-flex items-center gap-1.5 rounded-full bg-amber-50 px-3 py-1 text-[11px] font-semibold text-amber-700 ring-1 ring-amber-200/60">
            <span class="h-1.5 w-1.5 rounded-full bg-amber-500 animate-pulse"></span>
            Mode Offline
        </div>

        <h2 class="mt-3 text-lg font-bold tracking-tight text-slate-900">Koneksi Terputus</h2>
        <p class="mx-auto mt-2 max-w-[260px] text-[13px] leading-relaxed text-slate-500">
            Pembuatan Modul Ajar AI membutuhkan akses internet. Pastikan perangkat Anda terhubung, lalu muat ulang halaman.
        </p>

        <button onclick="location.reload()" class="press mx-auto mt-6 inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-900 px-6 py-3 text-xs font-semibold text-white shadow-lg shadow-slate-900/20 active:bg-slate-800">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            Coba Muat Ulang
        </button>
    </div>
</x-pwa-layout>
