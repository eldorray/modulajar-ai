@props(['variant' => 'app'])

{{--
    Banner pasang aplikasi. variant "app" dipakai di dalam PWA (mengapung di atas
    navigasi bawah), variant "landing" dipakai di halaman publik.
--}}
<div x-data="pwaInstall()" x-show="show" x-cloak
    class="fixed inset-x-0 z-40 mx-auto px-4 {{ $variant === 'app' ? 'bottom-[88px] max-w-[430px] px-5' : 'bottom-4 max-w-md' }}">
    <div class="pwa-install-card flex items-center gap-3 p-3 pr-2.5">
        <img src="{{ asset('icons/icon-192.png') }}" alt="" class="h-10 w-10 shrink-0 rounded-xl object-contain">

        <div class="min-w-0 flex-1">
            <p class="pwa-install-title text-[12.5px] font-extrabold leading-tight">Pasang aplikasi RPP Guru</p>
            <p class="mt-0.5 truncate text-[10.5px] font-medium text-[#7D93B6]" x-text="hint"></p>
        </div>

        <button type="button" @click="install()" class="pwa-install-cta shrink-0 rounded-xl px-3.5 py-2.5 text-[12px] font-bold text-white">
            Pasang
        </button>

        <button type="button" @click="dismiss()" aria-label="Tutup banner" class="shrink-0 rounded-lg p-1.5 text-[#A9BBD6] transition active:scale-90">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
            </svg>
        </button>
    </div>

    <!-- Panduan iOS (Safari tidak menyediakan prompt bawaan) -->
    <div x-show="guide" x-cloak @click.self="guide = false" class="fixed inset-0 z-50 flex items-end bg-[#08183A]/60 backdrop-blur-sm">
        <div class="w-full rounded-t-[26px] bg-white p-6">
            <h2 class="pwa-install-title text-[16px] font-extrabold">Pasang lewat Safari</h2>
            <ol class="mt-3 space-y-2.5 text-[13px] leading-5 text-[#0A1F44]">
                @foreach (['Ketuk tombol Bagikan di bilah bawah Safari.', 'Pilih Tambahkan ke Layar Utama.', 'Ketuk Tambah, lalu buka RPP Guru dari layar utama.'] as $i => $langkah)
                    <li class="flex gap-2.5">
                        <span class="mt-px flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#EDF3FF] text-[10px] font-extrabold text-[#1552F0]">{{ $i + 1 }}</span>
                        {{ $langkah }}
                    </li>
                @endforeach
            </ol>
            <button type="button" @click="guide = false" class="pwa-install-cta mt-5 w-full rounded-2xl py-3.5 text-[14px] font-bold text-white">Mengerti</button>
        </div>
    </div>
</div>

<style>
    .pwa-install-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 1px 2px rgba(10, 31, 68, .04), 0 14px 30px -14px rgba(10, 31, 68, .35);
        animation: pwaInstallUp .5s cubic-bezier(.34, 1.56, .5, 1) both;
    }

    .pwa-install-title { font-family: 'Plus Jakarta Sans', Inter, sans-serif; color: #0A1F44; letter-spacing: -.01em; }

    .pwa-install-cta {
        background: linear-gradient(150deg, #1552F0, #4B8BFF);
        box-shadow: 0 12px 24px -12px rgba(21, 82, 240, .6);
        transition: transform .18s cubic-bezier(.34, 1.56, .5, 1);
    }

    .pwa-install-cta:active { transform: scale(.94); }

    @keyframes pwaInstallUp {
        0% { opacity: 0; transform: translateY(24px) scale(.97); }
        65% { opacity: 1; transform: translateY(-5px) scale(1.012); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    @media (prefers-reduced-motion: reduce) {
        .pwa-install-card { animation: none; }
    }
</style>

<script>
    function pwaInstall() {
        return {
            show: false,
            guide: false,
            prompt: null,
            ios: false,
            hint: 'Akses cepat dari layar utama.',
            key: 'pwa-install-snooze',

            init() {
                // Sudah terpasang / dibuka standalone: jangan tampilkan apa pun.
                if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) return;
                if (this.snoozed()) return;

                this.ios = /iphone|ipad|ipod/i.test(navigator.userAgent) && !/crios|fxios/i.test(navigator.userAgent);

                if (this.ios) {
                    this.hint = 'Bagikan → Tambahkan ke Layar Utama.';
                    setTimeout(() => this.show = true, 1200);
                    return;
                }

                window.addEventListener('beforeinstallprompt', (event) => {
                    event.preventDefault();
                    this.prompt = event;
                    this.show = true;
                });

                window.addEventListener('appinstalled', () => {
                    this.show = false;
                    this.snooze(365);
                });
            },

            async install() {
                if (this.ios) {
                    this.guide = true;
                    return;
                }

                if (!this.prompt) return;

                this.prompt.prompt();
                const { outcome } = await this.prompt.userChoice;
                this.prompt = null;
                this.show = false;
                this.snooze(outcome === 'accepted' ? 365 : 7);
            },

            dismiss() {
                this.show = false;
                this.snooze(7);
            },

            snooze(days) {
                try {
                    localStorage.setItem(this.key, String(Date.now() + days * 86400000));
                } catch (error) {
                    // Mode privat: banner cukup hilang untuk sesi ini.
                }
            },

            snoozed() {
                try {
                    return Number(localStorage.getItem(this.key) || 0) > Date.now();
                } catch (error) {
                    return false;
                }
            },
        };
    }

    // Service worker didaftarkan di root agar seluruh situs (termasuk halaman
    // publik) memenuhi syarat pasang aplikasi.
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => navigator.serviceWorker.register('{{ asset('sw.js') }}'));
    }
</script>
