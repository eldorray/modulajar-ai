@props([
    'title' => 'RPP Guru',
    'active' => 'home',
    'header' => null,
])

<!DOCTYPE html>
<html lang="id" class="pwa-root">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, maximum-scale=1">
    <title>{{ $title }} — RPP Guru</title>

    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" content="#0B4FD9">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="RPP Guru">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    <link rel="icon" href="{{ asset('favicon.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .pwa-root {
            --pwa-ink: #0B2545;
            --pwa-blue-900: #062C6B;
            --pwa-blue-700: #0B4FD9;
            --pwa-blue-500: #2E90FA;
            --pwa-blue-100: #DCEAFE;
            --pwa-sky: #EDF4FF;
            --pwa-mint: #10B981;
            --pwa-amber: #F59E0B;
            --pwa-rose: #F43F5E;
            background: var(--pwa-sky);
        }

        .pwa-body {
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            color: var(--pwa-ink);
            background:
                radial-gradient(120% 60% at 50% 0%, #F4F9FF 0%, var(--pwa-sky) 55%, #E3EDFF 100%);
            min-height: 100dvh;
            -webkit-tap-highlight-color: transparent;
            overscroll-behavior-y: contain;
        }

        /* ===== Header melengkung ala kartu mobile banking ===== */
        .pwa-hero {
            background: linear-gradient(160deg, var(--pwa-blue-900) 0%, var(--pwa-blue-700) 48%, var(--pwa-blue-500) 100%);
            border-radius: 0 0 34px 34px;
            box-shadow: 0 18px 40px -18px rgba(11, 79, 217, .65);
            position: relative;
            overflow: hidden;
            padding-top: max(1rem, env(safe-area-inset-top));
        }

        .pwa-hero::after,
        .pwa-hero::before {
            content: '';
            position: absolute;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            pointer-events: none;
        }

        .pwa-hero::before { width: 190px; height: 190px; top: -70px; right: -50px; }
        .pwa-hero::after { width: 120px; height: 120px; bottom: -60px; left: -30px; }

        /* ===== Kartu ===== */
        .pwa-card {
            background: #fff;
            border-radius: 22px;
            box-shadow: 0 1px 2px rgba(11, 37, 69, .04), 0 12px 28px -12px rgba(11, 37, 69, .18);
        }

        .pwa-card-glass {
            background: rgba(255, 255, 255, .16);
            border: 1px solid rgba(255, 255, 255, .28);
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        /* ===== Animasi: masuk memantul + tekan ===== */
        @keyframes pwaPopIn {
            0% { opacity: 0; transform: translateY(18px) scale(.94); }
            60% { opacity: 1; transform: translateY(-4px) scale(1.02); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes pwaFabIn {
            0% { opacity: 0; transform: translateY(28px) scale(.6); }
            55% { opacity: 1; transform: translateY(-8px) scale(1.12); }
            75% { transform: translateY(2px) scale(.97); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes pwaIdleBounce {
            0%, 88%, 100% { transform: translateY(0); }
            92% { transform: translateY(-7px); }
            96% { transform: translateY(-2px); }
        }

        @keyframes pwaSlideUp {
            0% { opacity: 0; transform: translateY(26px) scale(.96); }
            65% { opacity: 1; transform: translateY(-5px) scale(1.015); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes pwaFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        .pop-in {
            animation: pwaPopIn .58s cubic-bezier(.34, 1.56, .5, 1) both;
            animation-delay: var(--d, 0ms);
        }

        .press { transition: transform .18s cubic-bezier(.34, 1.56, .5, 1), box-shadow .18s ease; }
        .press:active { transform: scale(.94); }

        .float { animation: pwaFloat 4.5s ease-in-out infinite; }

        .slide-up { animation: pwaSlideUp .5s cubic-bezier(.34, 1.56, .5, 1) both; }

        /* ===== Navigasi bawah ===== */
        .pwa-nav {
            background: rgba(255, 255, 255, .92);
            backdrop-filter: blur(14px);
            box-shadow: 0 -8px 30px -12px rgba(11, 37, 69, .28);
            padding-bottom: env(safe-area-inset-bottom);
        }

        .pwa-nav-item { color: #8CA3C7; transition: color .2s ease, transform .2s cubic-bezier(.34, 1.56, .5, 1); }
        .pwa-nav-item:active { transform: scale(.9); }
        .pwa-nav-item[data-active="true"] { color: var(--pwa-blue-700); }
        .pwa-nav-item[data-active="true"] .pwa-nav-dot { opacity: 1; transform: scale(1); }
        .pwa-nav-dot {
            width: 5px; height: 5px; border-radius: 999px; background: var(--pwa-blue-700);
            opacity: 0; transform: scale(.3); transition: all .25s cubic-bezier(.34, 1.56, .5, 1);
        }

        .pwa-fab {
            background: linear-gradient(150deg, var(--pwa-blue-700), var(--pwa-blue-500));
            box-shadow: 0 10px 24px -6px rgba(11, 79, 217, .7), 0 0 0 6px rgba(255, 255, 255, .95);
            animation: pwaFabIn .7s cubic-bezier(.34, 1.56, .5, 1) both, pwaIdleBounce 6s ease-in-out 1.4s infinite;
        }

        .pwa-fab:active { transform: scale(.9); }

        /* ===== Form mobile ===== */
        .pwa-field {
            width: 100%;
            border: 1px solid #DDE7F7;
            background: #F8FBFF;
            border-radius: 14px;
            padding: .8rem .95rem;
            font-size: .95rem;
            color: var(--pwa-ink);
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .pwa-field:focus {
            outline: none;
            background: #fff;
            border-color: var(--pwa-blue-500);
            box-shadow: 0 0 0 4px rgba(46, 144, 250, .16);
        }

        .pwa-label { font-size: .8rem; font-weight: 600; color: #4A648C; margin-bottom: .35rem; display: block; }
        .pwa-chip {
            border: 1px solid #DDE7F7; background: #F8FBFF; border-radius: 999px;
            padding: .45rem .85rem; font-size: .8rem; font-weight: 600; color: #4A648C;
            transition: all .2s cubic-bezier(.34, 1.56, .5, 1);
        }
        .pwa-chip:has(input:checked) {
            background: var(--pwa-blue-700); border-color: var(--pwa-blue-700); color: #fff;
            box-shadow: 0 6px 14px -6px rgba(11, 79, 217, .8);
        }

        @media (prefers-reduced-motion: reduce) {
            .pop-in, .pwa-fab, .float, .slide-up { animation: none !important; }
            .press, .pwa-nav-item { transition: none; }
        }
    </style>
</head>

<body class="pwa-body antialiased">
    <div class="mx-auto w-full max-w-md pb-28">
        @if ($header)
            <div class="pwa-hero px-5 pb-8 text-white">
                {{ $header }}
            </div>
        @endif

        <main class="px-5 {{ $header ? '-mt-6' : 'pt-6' }} space-y-5">
            @if (session('success'))
                <div class="pop-in rounded-2xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="pop-in rounded-2xl bg-rose-50 border border-rose-200 px-4 py-3 text-sm font-medium text-rose-700">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>


    <!-- Banner pasang aplikasi -->
    <div x-data="pwaInstall()" x-show="show" x-cloak
        class="fixed inset-x-0 bottom-[86px] z-40 mx-auto max-w-md px-5">
        <div class="slide-up pwa-card flex items-center gap-3 p-3.5 pr-3">
            <img src="{{ asset('icons/icon-192.png') }}" alt="" class="h-11 w-11 shrink-0 rounded-xl object-contain">

            <div class="min-w-0 flex-1">
                <p class="text-[13.5px] font-bold leading-tight">Pasang RPP Guru</p>
                <p class="mt-0.5 text-[11.5px] leading-4 text-[#7C90AF]" x-text="hint"></p>
            </div>

            <button type="button" @click="install()"
                class="press shrink-0 rounded-xl bg-[#0B4FD9] px-4 py-2.5 text-[12.5px] font-bold text-white shadow-[0_8px_18px_-10px_rgba(11,79,217,.95)]">
                Pasang
            </button>

            <button type="button" @click="dismiss()" aria-label="Tutup banner"
                class="press shrink-0 rounded-lg p-1.5 text-[#9DB2D3]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>

        <!-- Panduan iOS (Safari tidak menyediakan prompt bawaan) -->
        <div x-show="guide" x-cloak @click.self="guide = false"
            class="fixed inset-0 z-50 flex items-end bg-[#0B2545]/60 backdrop-blur-sm">
            <div class="slide-up w-full rounded-t-[28px] bg-white p-6">
                <h2 class="text-[16px] font-extrabold">Pasang lewat Safari</h2>
                <ol class="mt-3 space-y-2.5 text-[13px] leading-5">
                    <li class="flex gap-2.5">
                        <span class="mt-px flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#EAF2FF] text-[10px] font-bold text-[#0B4FD9]">1</span>
                        Ketuk tombol Bagikan di bilah bawah Safari.
                    </li>
                    <li class="flex gap-2.5">
                        <span class="mt-px flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#EAF2FF] text-[10px] font-bold text-[#0B4FD9]">2</span>
                        Pilih <strong>Tambahkan ke Layar Utama</strong>.
                    </li>
                    <li class="flex gap-2.5">
                        <span class="mt-px flex h-5 w-5 shrink-0 items-center justify-center rounded-full bg-[#EAF2FF] text-[10px] font-bold text-[#0B4FD9]">3</span>
                        Ketuk <strong>Tambah</strong>, lalu buka RPP Guru dari layar utama.
                    </li>
                </ol>
                <button type="button" @click="guide = false"
                    class="press mt-5 w-full rounded-2xl bg-[#0B4FD9] py-3.5 text-[14px] font-bold text-white">Mengerti</button>
            </div>
        </div>
    </div>

    <!-- Navigasi bawah + tombol generate -->
    <nav class="pwa-nav fixed bottom-0 left-0 right-0 z-40">
        <div class="mx-auto grid max-w-md grid-cols-5 items-end px-2 pt-2 pb-1.5">
            <a href="{{ route('pwa.home') }}" class="pwa-nav-item flex flex-col items-center gap-1 py-1.5" data-active="{{ $active === 'home' ? 'true' : 'false' }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 10.5 12 3l9 7.5M5.5 9.5V20h13V9.5" />
                </svg>
                <span class="text-[11px] font-semibold">Home</span>
                <span class="pwa-nav-dot"></span>
            </a>

            <a href="{{ route('pwa.rpp.index') }}" class="pwa-nav-item flex flex-col items-center gap-1 py-1.5" data-active="{{ $active === 'rpp' ? 'true' : 'false' }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h8l4 4v12H7zM15 4v4h4M9.5 13h6M9.5 16.5h4" />
                </svg>
                <span class="text-[11px] font-semibold">RPP</span>
                <span class="pwa-nav-dot"></span>
            </a>

            <div class="flex justify-center">
                <a href="{{ route('pwa.rpp.create') }}" aria-label="Buat modul ajar"
                    class="pwa-fab press -mt-8 flex h-16 w-16 items-center justify-center rounded-full">
                    <img src="{{ asset('logo.png') }}" alt="" class="h-9 w-9 object-contain drop-shadow">
                </a>
            </div>

            <a href="{{ route('pwa.akun') }}" class="pwa-nav-item flex flex-col items-center gap-1 py-1.5" data-active="{{ $active === 'akun' ? 'true' : 'false' }}">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM4.5 20a7.5 7.5 0 0 1 15 0" />
                </svg>
                <span class="text-[11px] font-semibold">Akun</span>
                <span class="pwa-nav-dot"></span>
            </a>

            <a href="{{ route('dashboard') }}" class="pwa-nav-item flex flex-col items-center gap-1 py-1.5" data-active="false">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 5.5h9M4 12h9M4 18.5h9M16 8.5 20 12l-4 3.5M20 12h-7" />
                </svg>
                <span class="text-[11px] font-semibold">Desktop</span>
                <span class="pwa-nav-dot"></span>
            </a>
        </div>
    </nav>

    <script>
        function pwaInstall() {
            return {
                show: false,
                guide: false,
                prompt: null,
                ios: false,
                hint: 'Akses lebih cepat dari layar utama.',
                key: 'pwa-install-snooze',

                init() {
                    // Sudah terpasang / dibuka standalone: jangan tampilkan apa pun.
                    const standalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
                    if (standalone || this.snoozed()) return;

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
    </script>

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => navigator.serviceWorker.register('{{ asset('sw.js') }}', { scope: '/app' }));
        }
    </script>
</body>

</html>
