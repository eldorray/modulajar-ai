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
    <meta name="theme-color" content="#1552F0">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="RPP Guru">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    <link rel="icon" href="{{ asset('favicon.png') }}">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:600,700,800|inter:400,500,600,700" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .pwa-root {
            /* Tinta & garis */
            --ink: #0A1F44;
            --ink-soft: #3E5B87;
            --muted: #7D93B6;
            --line: #E9EFFA;

            /* Merek */
            --brand-900: #082A7E;
            --brand-700: #1552F0;
            --brand-500: #4B8BFF;
            --brand-50: #EDF3FF;

            /* Aksen status & kategori */
            --mint: #0FB57A;
            --mint-50: #E7F8F1;
            --amber: #E8930C;
            --amber-50: #FEF4E3;
            --violet: #7C5CFF;
            --violet-50: #F1EDFF;
            --rose: #F2405F;
            --rose-50: #FEECEF;

            /* Bayangan */
            --sh-card: 0 1px 2px rgba(10, 31, 68, .04), 0 14px 30px -18px rgba(10, 31, 68, .28);
            --sh-soft: 0 8px 20px -14px rgba(10, 31, 68, .3);
            --sh-brand: 0 12px 24px -12px rgba(21, 82, 240, .6);

            background: #EEF3FD;
        }

        .pwa-body {
            font-family: Inter, ui-sans-serif, system-ui, sans-serif;
            color: var(--ink);
            background: linear-gradient(180deg, #F4F8FF 0%, #EDF3FD 40%, #E7EEFB 100%);
            min-height: 100dvh;
            -webkit-tap-highlight-color: transparent;
            overscroll-behavior-y: contain;
        }

        .pwa-display {
            font-family: 'Plus Jakarta Sans', Inter, sans-serif;
            letter-spacing: -.02em;
        }

        /* ===== Hero ===== */
        .pwa-hero {
            position: relative;
            overflow: hidden;
            border-radius: 0 0 30px 30px;
            padding-top: max(.75rem, env(safe-area-inset-top));
            background:
                radial-gradient(140% 120% at 88% -10%, rgba(255, 255, 255, .30) 0%, rgba(255, 255, 255, 0) 45%),
                radial-gradient(90% 90% at 8% 100%, rgba(124, 92, 255, .38) 0%, rgba(124, 92, 255, 0) 60%),
                linear-gradient(152deg, var(--brand-900) 0%, var(--brand-700) 55%, var(--brand-500) 100%);
            box-shadow: 0 20px 38px -24px rgba(8, 42, 126, .85);
        }

        .pwa-hero::before {
            content: '';
            position: absolute;
            inset: auto -40% -70% -40%;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .10);
        }

        .pwa-hero-title { font-size: 21px; font-weight: 800; line-height: 1.15; }
        .pwa-hero-eyebrow { font-size: 12px; font-weight: 500; color: rgba(255, 255, 255, .82); }

        /* ===== Permukaan ===== */
        .pwa-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: var(--sh-card);
        }

        .pwa-sub { color: var(--muted); }

        .pwa-h2 {
            font-family: 'Plus Jakarta Sans', Inter, sans-serif;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -.01em;
        }

        .pwa-chip-meta {
            display: inline-flex;
            align-items: center;
            border-radius: 8px;
            background: var(--brand-50);
            color: var(--ink-soft);
            font-size: 10.5px;
            font-weight: 600;
            padding: 3px 7px;
        }

        .pwa-badge {
            border-radius: 999px;
            font-size: 10px;
            font-weight: 700;
            padding: 3.5px 9px;
            letter-spacing: .01em;
        }

        /* Cincin progres (conic-gradient, tanpa JS) */
        .pwa-ring {
            --p: 0;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: conic-gradient(var(--brand-700) calc(var(--p) * 1%), var(--brand-50) 0);
            display: grid;
            place-items: center;
        }

        .pwa-ring > span {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #fff;
            display: grid;
            place-items: center;
            font-size: 12px;
            font-weight: 800;
            color: var(--brand-700);
            box-shadow: inset 0 0 0 1px var(--line);
        }

        /* ===== Animasi ===== */
        @keyframes pwaPopIn {
            0% { opacity: 0; transform: translateY(16px) scale(.95); }
            62% { opacity: 1; transform: translateY(-4px) scale(1.015); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes pwaFabIn {
            0% { opacity: 0; transform: translateY(26px) scale(.6); }
            55% { opacity: 1; transform: translateY(-8px) scale(1.1); }
            78% { transform: translateY(2px) scale(.97); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes pwaIdleBounce {
            0%, 88%, 100% { transform: translateY(0); }
            92% { transform: translateY(-7px); }
            96% { transform: translateY(-2px); }
        }

        @keyframes pwaFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-6px); }
        }

        @keyframes pwaGrow {
            from { width: 0; }
        }

        @keyframes pwaSlideUp {
            0% { opacity: 0; transform: translateY(24px) scale(.97); }
            65% { opacity: 1; transform: translateY(-5px) scale(1.012); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        .pop-in { animation: pwaPopIn .55s cubic-bezier(.34, 1.56, .5, 1) both; animation-delay: var(--d, 0ms); }
        .slide-up { animation: pwaSlideUp .5s cubic-bezier(.34, 1.56, .5, 1) both; }
        .float { animation: pwaFloat 4.5s ease-in-out infinite; }
        .grow-bar { animation: pwaGrow .9s cubic-bezier(.22, 1, .36, 1) both; animation-delay: var(--d, 120ms); }

        .press { transition: transform .18s cubic-bezier(.34, 1.56, .5, 1), box-shadow .18s ease; }
        .press:active { transform: scale(.955); }

        /* ===== Navigasi bawah ===== */
        .pwa-nav {
            background: rgba(255, 255, 255, .94);
            backdrop-filter: blur(16px);
            box-shadow: 0 -10px 30px -16px rgba(10, 31, 68, .35);
            padding-bottom: env(safe-area-inset-bottom);
        }

        .pwa-nav-item {
            color: #9BAECD;
            border-radius: 14px;
            padding: 6px 0 4px;
            transition: color .2s ease, background .25s ease, transform .2s cubic-bezier(.34, 1.56, .5, 1);
        }

        .pwa-nav-item:active { transform: scale(.92); }

        .pwa-nav-item[data-active="true"] {
            color: var(--brand-700);
            background: var(--brand-50);
        }

        .pwa-fab {
            background: #fff;
            box-shadow: var(--sh-brand), 0 0 0 5px #fff, 0 0 0 6.5px rgba(21, 82, 240, .16);
            animation: pwaFabIn .7s cubic-bezier(.34, 1.56, .5, 1) both, pwaIdleBounce 7s ease-in-out 1.6s infinite;
        }

        .pwa-fab::after {
            content: '';
            position: absolute;
            inset: -3px;
            border-radius: 999px;
            background: linear-gradient(150deg, var(--brand-700), var(--violet));
            z-index: -1;
        }

        .pwa-fab:active { transform: scale(.92); }

        /* ===== Form ===== */
        .pwa-field {
            width: 100%;
            border: 1px solid var(--line);
            background: #F7FAFF;
            border-radius: 13px;
            padding: .78rem .9rem;
            font-size: .94rem;
            font-weight: 500;
            color: var(--ink);
            transition: border-color .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .pwa-field::placeholder { color: #A9BBD6; font-weight: 400; }

        .pwa-field:focus {
            outline: none;
            background: #fff;
            border-color: var(--brand-500);
            box-shadow: 0 0 0 4px rgba(75, 139, 255, .16);
        }

        .pwa-label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .01em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: .4rem;
        }

        .pwa-chip {
            border: 1px solid var(--line);
            background: #F7FAFF;
            border-radius: 999px;
            padding: .45rem .85rem;
            font-size: 12px;
            font-weight: 600;
            color: var(--ink-soft);
            transition: all .2s cubic-bezier(.34, 1.56, .5, 1);
        }

        .pwa-chip:has(input:checked) {
            background: var(--brand-700);
            border-color: var(--brand-700);
            color: #fff;
            box-shadow: var(--sh-brand);
        }

        @media (prefers-reduced-motion: reduce) {
            .pop-in, .pwa-fab, .float, .slide-up, .grow-bar { animation: none !important; }
            .press, .pwa-nav-item { transition: none; }
        }
    </style>
</head>

<body class="pwa-body antialiased">
    <div class="mx-auto w-full max-w-[430px] pb-32">
        @if ($header)
            <div class="pwa-hero px-5 pb-14 text-white">
                {{ $header }}
            </div>
        @endif

        <main class="px-5 {{ $header ? '-mt-9' : 'pt-6' }} space-y-3.5">
            @if (session('success'))
                <div class="pop-in rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[12.5px] font-semibold text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="pop-in rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-[12.5px] font-semibold text-rose-700">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <!-- Banner pasang aplikasi -->
    <div x-data="pwaInstall()" x-show="show" x-cloak
        class="fixed inset-x-0 bottom-[88px] z-40 mx-auto max-w-[430px] px-5">
        <div class="slide-up pwa-card flex items-center gap-3 p-3 pr-2.5">
            <img src="{{ asset('icons/icon-192.png') }}" alt="" class="h-10 w-10 shrink-0 rounded-xl object-contain">

            <div class="min-w-0 flex-1">
                <p class="pwa-display text-[13px] font-extrabold leading-tight">Pasang RPP Guru</p>
                <p class="pwa-sub mt-0.5 text-[11px] leading-4" x-text="hint"></p>
            </div>

            <button type="button" @click="install()"
                class="press shrink-0 rounded-xl px-3.5 py-2.5 text-[12px] font-bold text-white"
                style="background: linear-gradient(150deg, var(--brand-700), var(--brand-500)); box-shadow: var(--sh-brand)">
                Pasang
            </button>

            <button type="button" @click="dismiss()" aria-label="Tutup banner" class="press shrink-0 rounded-lg p-1.5 text-[#A9BBD6]">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>

        <!-- Panduan iOS (Safari tidak menyediakan prompt bawaan) -->
        <div x-show="guide" x-cloak @click.self="guide = false"
            class="fixed inset-0 z-50 flex items-end bg-[#08183A]/60 backdrop-blur-sm">
            <div class="slide-up w-full rounded-t-[26px] bg-white p-6">
                <h2 class="pwa-display text-[16px] font-extrabold">Pasang lewat Safari</h2>
                <ol class="mt-3 space-y-2.5 text-[13px] leading-5">
                    @foreach (['Ketuk tombol Bagikan di bilah bawah Safari.', 'Pilih Tambahkan ke Layar Utama.', 'Ketuk Tambah, lalu buka RPP Guru dari layar utama.'] as $i => $langkah)
                        <li class="flex gap-2.5">
                            <span class="mt-px flex h-5 w-5 shrink-0 items-center justify-center rounded-full text-[10px] font-extrabold"
                                style="background: var(--brand-50); color: var(--brand-700)">{{ $i + 1 }}</span>
                            {{ $langkah }}
                        </li>
                    @endforeach
                </ol>
                <button type="button" @click="guide = false"
                    class="press mt-5 w-full rounded-2xl py-3.5 text-[14px] font-bold text-white"
                    style="background: linear-gradient(150deg, var(--brand-700), var(--brand-500))">Mengerti</button>
            </div>
        </div>
    </div>

    <!-- Navigasi bawah + tombol generate -->
    <nav class="pwa-nav fixed bottom-0 left-0 right-0 z-30">
        <div class="mx-auto grid max-w-[430px] grid-cols-5 items-end gap-1 px-3 pt-2 pb-1.5">
            @php
                $navItems = [
                    ['key' => 'home', 'label' => 'Home', 'url' => route('pwa.home'), 'icon' => 'M3 10.5 12 3l9 7.5M5.5 9.5V20h13V9.5'],
                    ['key' => 'rpp', 'label' => 'Modul', 'url' => route('pwa.rpp.index'), 'icon' => 'M7 4h8l4 4v12H7zM15 4v4h4M9.5 13h6M9.5 16.5h4'],
                ];
                $navItemsRight = [
                    ['key' => 'akun', 'label' => 'Akun', 'url' => route('pwa.akun'), 'icon' => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM4.5 20a7.5 7.5 0 0 1 15 0'],
                    ['key' => 'desktop', 'label' => 'Desktop', 'url' => route('dashboard'), 'icon' => 'M4 5.5h16v9.5H4zM9 19h6M12 15.5V19'],
                ];
            @endphp

            @foreach ($navItems as $item)
                <a href="{{ $item['url'] }}" class="pwa-nav-item flex flex-col items-center gap-0.5" data-active="{{ $active === $item['key'] ? 'true' : 'false' }}">
                    <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                    </svg>
                    <span class="text-[10.5px] font-bold">{{ $item['label'] }}</span>
                </a>
            @endforeach

            <div class="flex justify-center">
                <a href="{{ route('pwa.rpp.create') }}" aria-label="Buat modul ajar"
                    class="pwa-fab press relative -mt-9 flex h-[62px] w-[62px] items-center justify-center rounded-full">
                    <img src="{{ asset('logo.png') }}" alt="" class="h-9 w-9 object-contain">
                </a>
            </div>

            @foreach ($navItemsRight as $item)
                <a href="{{ $item['url'] }}" class="pwa-nav-item flex flex-col items-center gap-0.5" data-active="{{ $active === $item['key'] ? 'true' : 'false' }}">
                    <svg class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                    </svg>
                    <span class="text-[10.5px] font-bold">{{ $item['label'] }}</span>
                </a>
            @endforeach
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
