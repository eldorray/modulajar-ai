@props(['variant' => 'app'])

{{--
    Banner pasang aplikasi. variant "app" dipakai di dalam PWA (mengapung di atas
    navigasi bawah), variant "landing" dipakai di halaman publik.
--}}
<div x-data="pwaInstall()" x-show="show" x-cloak
    @keydown.escape.window="guide = false"
    class="{{ $variant === 'app' ? 'pwa-install-app max-w-[430px] px-5' : 'pwa-install-landing max-w-md px-4' }} fixed inset-x-0 z-40 mx-auto">
    <div class="pwa-install-card flex items-center gap-3 p-3 pr-2.5">
        <img src="{{ asset('icons/icon-192.png') }}" alt="" class="h-11 w-11 shrink-0 rounded-[13px] object-cover">

        <div class="min-w-0 flex-1">
            <p class="pwa-install-title text-[12.5px] font-extrabold leading-tight">Pasang aplikasi RPP Guru</p>
            <p class="mt-0.5 truncate text-[10.5px] font-medium text-[#7D93B6]" x-text="hint"></p>
        </div>

        <button type="button" @click="install()" class="pwa-install-cta min-h-11 shrink-0 rounded-xl px-3.5 py-2.5 text-[12px] font-bold text-white">
            Pasang
        </button>

        <button type="button" @click="dismiss()" aria-label="Tutup banner" class="pwa-install-close flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-[#7187AA]">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
            </svg>
        </button>
    </div>

    <!-- Panduan manual ketika browser tidak menyediakan prompt pemasangan bawaan -->
    <div x-show="guide" x-cloak @click.self="guide = false" role="dialog" aria-modal="true" aria-labelledby="pwa-install-guide-title"
        class="pwa-install-scrim fixed inset-0 z-50 flex items-end">
        <div class="pwa-install-sheet mx-auto w-full max-w-[430px] rounded-t-[28px] bg-white px-6 pb-6 pt-3"
            style="padding-bottom: calc(1.5rem + env(safe-area-inset-bottom, 0px))">
            <div class="mx-auto mb-5 h-1.5 w-10 rounded-full bg-[#D7E0EF]" aria-hidden="true"></div>
            <h2 id="pwa-install-guide-title" class="pwa-install-title text-[17px] font-extrabold" x-text="guideTitle"></h2>
            <ol x-show="ios" class="mt-3 space-y-2.5 text-[13px] leading-5 text-[#0A1F44]">
                @foreach (['Ketuk tombol Bagikan di bilah bawah Safari.', 'Pilih Tambahkan ke Layar Utama.', 'Ketuk Tambah, lalu buka RPP Guru dari layar utama.'] as $i => $langkah)
                    <li class="flex gap-2.5">
                        <span class="mt-px flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#EDF3FF] text-[10px] font-extrabold text-[#1552F0]">{{ $i + 1 }}</span>
                        {{ $langkah }}
                    </li>
                @endforeach
            </ol>
            <ol x-show="androidChrome" class="mt-3 space-y-2.5 text-[13px] leading-5 text-[#0A1F44]">
                @foreach (['Ketuk menu tiga titik di kanan atas Chrome.', 'Pilih Pasang aplikasi atau Tambahkan ke layar utama.', 'Konfirmasi Pasang, lalu buka RPP Guru dari layar utama.'] as $i => $langkah)
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
    .pwa-install-app { bottom: calc(5.75rem + env(safe-area-inset-bottom, 0px)); }
    .pwa-install-landing { bottom: calc(1rem + env(safe-area-inset-bottom, 0px)); }

    .pwa-install-card {
        background: rgba(255, 255, 255, .88);
        border: 1px solid rgba(255, 255, 255, .86);
        border-radius: 20px;
        box-shadow: 0 1px 2px rgba(10, 31, 68, .04), 0 18px 40px -20px rgba(10, 31, 68, .48);
        backdrop-filter: blur(22px) saturate(170%);
        -webkit-backdrop-filter: blur(22px) saturate(170%);
        animation: pwaInstallUp .38s cubic-bezier(.16, 1, .3, 1) both;
    }

    .pwa-install-title {
        font-family: ui-rounded, -apple-system, BlinkMacSystemFont, "SF Pro Rounded", "SF Pro Display", "Segoe UI", sans-serif;
        color: #0A1F44;
        letter-spacing: -.015em;
    }

    .pwa-install-cta {
        background: linear-gradient(150deg, #1552F0, #4B8BFF);
        box-shadow: 0 12px 24px -12px rgba(21, 82, 240, .6);
        touch-action: manipulation;
        transition: transform 100ms ease-out, filter 140ms ease-out;
        user-select: none;
        -webkit-user-select: none;
    }

    .pwa-install-cta:active { transform: scale(.96); filter: brightness(.96); }

    .pwa-install-close {
        touch-action: manipulation;
        transition: transform 100ms ease-out, background 140ms ease-out;
        user-select: none;
        -webkit-user-select: none;
    }

    .pwa-install-close:active { transform: scale(.92); background: #EDF3FF; }

    .pwa-install-scrim {
        background: rgba(5, 18, 48, .54);
        backdrop-filter: blur(10px) saturate(120%);
        -webkit-backdrop-filter: blur(10px) saturate(120%);
    }

    .pwa-install-sheet {
        box-shadow: 0 -24px 64px -32px rgba(5, 18, 48, .7);
        animation: pwaSheetUp .34s cubic-bezier(.16, 1, .3, 1) both;
    }

    @keyframes pwaInstallUp {
        0% { opacity: 0; transform: translateY(16px) scale(.985); filter: blur(8px); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    @keyframes pwaSheetUp {
        0% { opacity: 0; transform: translateY(28px); filter: blur(8px); }
        100% { opacity: 1; transform: translateY(0); }
    }

    @media (hover: hover) and (pointer: fine) {
        .pwa-install-cta:hover { filter: brightness(.96); }
        .pwa-install-close:hover { background: #EDF3FF; }
    }

    @media (prefers-reduced-transparency: reduce) {
        .pwa-install-card, .pwa-install-sheet { background: #fff; backdrop-filter: none; -webkit-backdrop-filter: none; }
        .pwa-install-scrim { background: rgba(5, 18, 48, .72); backdrop-filter: none; -webkit-backdrop-filter: none; }
    }

    @media (prefers-reduced-motion: reduce) {
        .pwa-install-card, .pwa-install-sheet { animation: none; }
    }
</style>

<script>
    function pwaInstall() {
        return {
            show: false,
            guide: false,
            prompt: null,
            ios: false,
            androidChrome: false,
            hint: 'Akses cepat dari layar utama.',
            guideTitle: 'Pasang aplikasi',
            key: 'pwa-install-snooze',

            init() {
                // Sudah terpasang / dibuka standalone: jangan tampilkan apa pun.
                if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) return;
                if (this.snoozed()) return;

                this.ios = /iphone|ipad|ipod/i.test(navigator.userAgent);
                this.androidChrome = /android/i.test(navigator.userAgent) && /chrome|crios/i.test(navigator.userAgent) && !/edg|opr|opera|samsungbrowser/i.test(navigator.userAgent);

                if (this.ios) {
                    this.hint = 'Bagikan → Tambahkan ke Layar Utama.';
                    this.guideTitle = 'Pasang di iPhone atau iPad';
                    setTimeout(() => this.show = true, 600);
                    return;
                }

                window.addEventListener('beforeinstallprompt', (event) => {
                    event.preventDefault();
                    this.prompt = event;
                    this.show = true;
                });

                // Chrome Android dapat menahan beforeinstallprompt karena engagement,
                // prompt pernah ditolak, atau evaluasi installability belum selesai.
                // Banner tetap tersedia dan mengarahkan pengguna ke menu Chrome.
                if (this.androidChrome) {
                    this.hint = 'Pasang dari Chrome ke layar utama.';
                    this.guideTitle = 'Pasang aplikasi melalui menu Chrome';
                    setTimeout(() => {
                        const installed = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
                        if (!this.prompt && !installed && !this.snoozed()) this.show = true;
                    }, 900);
                }

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

                if (!this.prompt) {
                    if (this.androidChrome) this.guide = true;
                    return;
                }

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

</script>
