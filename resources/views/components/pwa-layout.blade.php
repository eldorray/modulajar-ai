@props([
    'title' => 'RPP Guru',
    'active' => 'home',
    'header' => null,
    // Modul yang dibuka menu "Detail". Kosong = pakai modul terakhir yang selesai.
    'detail' => null,
    'showInstallBanner' => true,
])

@php
    $detailRpp = $detail ?? \App\Models\Rpp::forUser(auth()->id())->completed()->latest()->first();
@endphp

<!DOCTYPE html>
<html lang="id" class="pwa-root">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
    <title>{{ $title }} — RPP Guru</title>

    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="#082A7E">
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#061A45">
    <meta name="color-scheme" content="light">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="RPP Guru">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="icon" href="{{ asset('favicon.png') }}">

    <script>
        // Tandai sesi standalone sedini mungkin supaya tautan desktop tidak berkedip.
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
            document.documentElement.classList.add('is-standalone');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .pwa-root {
            /* Tinta & garis */
            --ink: #0A1F44;
            --ink-soft: #3E5B87;
            --muted: #7D93B6;
            --line: rgba(116, 145, 190, .18);

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
            --sh-card: 0 1px 1px rgba(10, 31, 68, .04), 0 18px 42px -26px rgba(10, 31, 68, .42);
            --sh-soft: 0 10px 24px -18px rgba(10, 31, 68, .34);
            --sh-brand: 0 14px 28px -14px rgba(21, 82, 240, .58);
            --ease-out: cubic-bezier(.16, 1, .3, 1);

            background: #EEF3FD;
            -webkit-tap-highlight-color: transparent;
            -webkit-text-size-adjust: 100%;
            font-optical-sizing: auto;
            overscroll-behavior-x: none;
        }

        .pwa-body {
            font-family: ui-sans-serif, -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(120% 55% at 50% -5%, rgba(75, 139, 255, .20), transparent 62%),
                linear-gradient(180deg, #F5F8FE 0%, #EDF3FD 46%, #E7EEFA 100%);
            min-height: 100dvh;
            overflow-x: hidden;
            overscroll-behavior-y: none;
        }

        .pwa-display {
            font-family: ui-rounded, -apple-system, BlinkMacSystemFont, "SF Pro Rounded", "SF Pro Display", "Segoe UI", sans-serif;
            letter-spacing: -.025em;
        }

        /* ===== Hero ===== */
        .pwa-hero {
            position: relative;
            overflow: hidden;
            border-radius: 0 0 2rem 2rem;
            padding-top: calc(.5rem + env(safe-area-inset-top, 0px));
            background:
                radial-gradient(140% 120% at 88% -10%, rgba(255, 255, 255, .30) 0%, rgba(255, 255, 255, 0) 45%),
                radial-gradient(90% 90% at 8% 100%, rgba(124, 92, 255, .38) 0%, rgba(124, 92, 255, 0) 60%),
                linear-gradient(152deg, var(--brand-900) 0%, var(--brand-700) 55%, var(--brand-500) 100%);
            box-shadow: 0 24px 54px -32px rgba(8, 42, 126, .9);
            isolation: isolate;
        }

        .pwa-hero::before {
            content: '';
            position: absolute;
            inset: auto -40% -70% -40%;
            height: 150px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .10);
            pointer-events: none;
        }

        .pwa-hero::after {
            content: '';
            position: absolute;
            inset: 0;
            z-index: -1;
            border-radius: inherit;
            box-shadow: inset 0 -1px 0 rgba(255, 255, 255, .18);
            pointer-events: none;
        }

        .pwa-hero-title { font-size: 21px; font-weight: 780; line-height: 1.12; letter-spacing: -.025em; }
        .pwa-hero-eyebrow { font-size: 12px; font-weight: 600; letter-spacing: .01em; color: rgba(255, 255, 255, .84); }

        /* ===== Permukaan ===== */
        .pwa-card {
            background: rgba(255, 255, 255, .88);
            border: 1px solid rgba(255, 255, 255, .78);
            border-radius: 20px;
            box-shadow: var(--sh-card);
            backdrop-filter: blur(20px) saturate(155%);
            -webkit-backdrop-filter: blur(20px) saturate(155%);
        }

        .pwa-sub { color: var(--muted); }

        .pwa-h2 {
            font-family: ui-rounded, -apple-system, BlinkMacSystemFont, "SF Pro Rounded", "SF Pro Display", "Segoe UI", sans-serif;
            font-size: 15px;
            font-weight: 750;
            letter-spacing: -.015em;
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
            0% { opacity: 0; transform: translateY(10px) scale(.985); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes pwaFabIn {
            0% { opacity: 0; transform: translateY(16px) scale(.82); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes pwaGrow {
            from { width: 0; }
        }

        @keyframes pwaSlideUp {
            0% { opacity: 0; transform: translateY(18px) scale(.985); filter: blur(8px); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        .pop-in { animation: pwaPopIn .42s var(--ease-out) both; animation-delay: var(--d, 0ms); }
        .slide-up { animation: pwaSlideUp .38s var(--ease-out) both; }
        .float { transform: translateZ(0); }
        .grow-bar { animation: pwaGrow .7s var(--ease-out) both; animation-delay: var(--d, 120ms); }

        .press {
            touch-action: manipulation;
            transition: transform 100ms ease-out, filter 140ms ease-out, box-shadow .2s var(--ease-out);
            user-select: none;
            -webkit-user-select: none;
            -webkit-touch-callout: none;
        }
        .press:active { transform: scale(.97); filter: brightness(.98); }

        .pwa-root :where(a, button, input, select, textarea):focus-visible {
            outline: 3px solid rgba(75, 139, 255, .48);
            outline-offset: 3px;
        }

        .pwa-root :where(button, a, [role="button"]) {
            touch-action: manipulation;
        }

        .pwa-root :where(button, [role="button"]) {
            user-select: none;
            -webkit-user-select: none;
            -webkit-touch-callout: none;
        }

        /* ===== Navigasi bawah ===== */
        .pwa-nav {
            background: rgba(248, 251, 255, .78);
            border-top: 1px solid rgba(255, 255, 255, .88);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            box-shadow: 0 -14px 36px -26px rgba(10, 31, 68, .52);
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }

        .pwa-nav-item {
            color: #9BAECD;
            border-radius: 14px;
            min-height: 3.25rem;
            padding: 6px 0 4px;
            touch-action: manipulation;
            transition: color .18s ease-out, background .24s var(--ease-out), transform 100ms ease-out;
            user-select: none;
            -webkit-user-select: none;
        }

        .pwa-nav-item:active { transform: scale(.94); }

        .pwa-nav-item[data-active="true"] {
            color: var(--brand-700);
            background: var(--brand-50);
        }

        .pwa-fab {
            background: #fff;
            box-shadow: var(--sh-brand), 0 0 0 5px #fff, 0 0 0 6.5px rgba(21, 82, 240, .16);
            animation: pwaFabIn .5s var(--ease-out) both;
            will-change: transform;
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
            font-size: 1rem;
            font-weight: 500;
            color: var(--ink);
            transition: border-color .18s ease-out, box-shadow .24s var(--ease-out), background .18s ease-out;
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
            min-height: 2.75rem;
            transition: color .18s ease-out, background .18s ease-out, border-color .18s ease-out, transform 100ms ease-out, box-shadow .24s var(--ease-out);
        }

        .pwa-chip:has(input:checked) {
            background: var(--brand-700);
            border-color: var(--brand-700);
            color: #fff;
            box-shadow: var(--sh-brand);
        }

        /* Aplikasi terpasang: tutup jalan ke tampilan desktop */
        @media all and (display-mode: standalone) {
            [data-hide-standalone] { display: none !important; }
        }

        html.is-standalone [data-hide-standalone] { display: none !important; }

        @media (hover: hover) and (pointer: fine) {
            .press:hover { filter: brightness(.985); }
            .pwa-nav-item:hover { color: var(--brand-700); background: rgba(237, 243, 255, .72); }
        }

        @media (prefers-reduced-transparency: reduce) {
            .pwa-card, .pwa-nav {
                background: #fff;
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
            }
        }

        @media (prefers-contrast: more) {
            .pwa-card { background: #fff; border-color: #8CA0BF; }
            .pwa-nav { background: #fff; border-top-color: #60789E; }
            .pwa-sub { color: #425A7E; }
        }

        @media (prefers-reduced-motion: reduce) {
            .pop-in, .pwa-fab, .float, .slide-up, .grow-bar { animation: none !important; }
            .press, .pwa-nav-item, .pwa-chip { transition-duration: 0.01ms !important; }
        }
    </style>
</head>

<body class="pwa-body antialiased">
    <div class="mx-auto min-h-[100dvh] w-full max-w-[430px] pb-[calc(8rem+env(safe-area-inset-bottom,0px))]">
        @if ($header)
            <div class="pwa-hero px-5 pb-14 text-white">
                {{ $header }}
            </div>
        @endif

        <main class="px-5 {{ $header ? '-mt-9' : 'pt-[calc(1.5rem+env(safe-area-inset-top,0px))]' }} space-y-3.5">
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

    @if ($showInstallBanner)
        <x-install-banner variant="app" />
    @endif

    <!-- Navigasi bawah + tombol generate -->
    <nav class="pwa-nav fixed bottom-0 left-0 right-0 z-30" aria-label="Navigasi utama">
        <div class="mx-auto grid max-w-[430px] grid-cols-5 items-end gap-1 px-3 pt-2 pb-1.5">
            @php
                $navItems = [
                    ['key' => 'home', 'label' => 'Home', 'url' => route('pwa.home'), 'icon' => 'M3 10.5 12 3l9 7.5M5.5 9.5V20h13V9.5'],
                    ['key' => 'rpp', 'label' => 'Modul', 'url' => route('pwa.rpp.index'), 'icon' => 'M7 4h8l4 4v12H7zM15 4v4h4M9.5 13h6M9.5 16.5h4'],
                ];
                $navItemsRight = [
                    [
                        'key' => 'detail',
                        'label' => 'Detail',
                        // Isi modul lengkap, halaman khusus PWA
                        'url' => $detailRpp ? route('pwa.rpp.detail', $detailRpp) : route('pwa.rpp.index'),
                        'icon' => 'M4 5.5h16M4 10h16M4 14.5h11M4 19h8',
                    ],
                    ['key' => 'akun', 'label' => 'Akun', 'url' => route('pwa.akun'), 'icon' => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM4.5 20a7.5 7.5 0 0 1 15 0'],
                ];
            @endphp

            @foreach ($navItems as $item)
                <a href="{{ $item['url'] }}" class="pwa-nav-item flex flex-col items-center gap-0.5" data-active="{{ $active === $item['key'] ? 'true' : 'false' }}"
                    @if ($active === $item['key']) aria-current="page" @endif>
                    <svg aria-hidden="true" class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
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
                <a href="{{ $item['url'] }}" class="pwa-nav-item flex flex-col items-center gap-0.5"
                    data-active="{{ $active === $item['key'] ? 'true' : 'false' }}"
                    @if ($active === $item['key']) aria-current="page" @endif
                    @if ($item['hideStandalone'] ?? false) data-hide-standalone @endif>
                    <svg aria-hidden="true" class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="1.9" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                    </svg>
                    <span class="text-[10.5px] font-bold">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

</body>

</html>
