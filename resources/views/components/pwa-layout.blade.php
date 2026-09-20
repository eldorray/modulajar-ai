@props([
    'title' => 'RPP Guru',
    'active' => 'home',
    'header' => null,
    // Modul yang dibuka menu "Detail". Kosong = pakai modul terakhir yang selesai.
    'detail' => null,
    'showInstallBanner' => true,
])

@php
    $detailRpp = $detail ?? (auth()->check() ? \App\Models\Rpp::forUser(auth()->id())->completed()->latest()->first() : null);
@endphp

<!DOCTYPE html>
<html lang="id" class="pwa-root">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover, interactive-widget=resizes-content">
    <title>{{ $title }} — RPP Guru</title>

    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <meta name="theme-color" media="(prefers-color-scheme: light)" content="#0F172A">
    <meta name="theme-color" media="(prefers-color-scheme: dark)" content="#0B132B">
    <meta name="color-scheme" content="light">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="RPP Guru">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('icons/apple-touch-icon.png') }}">
    <link rel="icon" href="{{ asset('favicon.png') }}">

    <!-- Google Font Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <script>
        // Tandai sesi standalone sedini mungkin supaya tautan desktop tidak berkedip.
        if (window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true) {
            document.documentElement.classList.add('is-standalone');
        }
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        .pwa-root {
            /* Tinta & Slate */
            --ink: #0F172A;
            --ink-soft: #334155;
            --muted: #64748B;
            --line: #E2E8F0;
            --line-subtle: rgba(226, 232, 240, 0.7);

            /* Brand (Deep Slate & Royal Indigo) */
            --brand-950: #0B132B;
            --brand-900: #0F172A;
            --brand-800: #1E293B;
            --brand-700: #2563EB;
            --brand-600: #3B82F6;
            --brand-500: #60A5FA;
            --brand-50: #EFF6FF;

            /* Aksen Status & Kategori (WCAG Compliant) */
            --mint: #059669;
            --mint-50: #ECFDF5;
            --amber: #D97706;
            --amber-50: #FFFBEB;
            --violet: #6366F1;
            --violet-50: #EEF2FF;
            --rose: #E11D48;
            --rose-50: #FFF1F2;

            /* Bayangan & Elevasi Natural */
            --sh-card: 0 1px 3px 0 rgba(15, 23, 42, 0.04), 0 8px 24px -6px rgba(15, 23, 42, 0.05);
            --sh-card-hover: 0 4px 6px -1px rgba(15, 23, 42, 0.05), 0 12px 28px -6px rgba(15, 23, 42, 0.08);
            --sh-soft: 0 4px 14px -4px rgba(15, 23, 42, 0.08);
            --sh-brand: 0 10px 25px -5px rgba(37, 99, 235, 0.38);
            --sh-floating: 0 12px 32px -8px rgba(15, 23, 42, 0.12);

            /* Kurva Gerak Berstandar Emil Kowalski */
            --ease-spring: cubic-bezier(0.16, 1, 0.3, 1);
            --ease-out: cubic-bezier(0.2, 0, 0, 1);
            --ease-fast: cubic-bezier(0, 0, 0.2, 1);

            background: #F8FAFC;
            -webkit-tap-highlight-color: transparent;
            -webkit-text-size-adjust: 100%;
            font-optical-sizing: auto;
            overscroll-behavior-x: none;
        }

        .pwa-body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            color: var(--ink);
            background: #F8FAFC;
            background-image:
                radial-gradient(at 0% 0%, rgba(37, 99, 235, 0.04) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(99, 102, 241, 0.03) 0px, transparent 50%);
            min-height: 100dvh;
            overflow-x: hidden;
            overscroll-behavior-y: contain;
        }

        .pwa-display {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            letter-spacing: -.025em;
        }

        /* ===== Hero Header ===== */
        .pwa-hero {
            position: relative;
            overflow: hidden;
            border-radius: 0 0 1.75rem 1.75rem;
            padding-top: calc(.85rem + env(safe-area-inset-top, 0px));
            background:
                radial-gradient(100% 100% at 85% 0%, rgba(37, 99, 235, 0.35) 0%, transparent 60%),
                radial-gradient(80% 80% at 10% 100%, rgba(79, 70, 229, 0.3) 0%, transparent 60%),
                linear-gradient(160deg, #0F172A 0%, #1E3A8A 55%, #1D4ED8 100%);
            box-shadow: 0 16px 36px -12px rgba(15, 23, 42, 0.45);
            isolation: isolate;
        }

        .pwa-card-glass {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 20px;
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.22), 0 8px 24px -6px rgba(0, 0, 0, 0.18);
        }

        .pwa-hero-title {
            font-size: 20px;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -.025em;
            color: #FFFFFF;
        }

        .pwa-hero-eyebrow {
            font-size: 11.5px;
            font-weight: 600;
            letter-spacing: .015em;
            color: rgba(226, 232, 240, 0.78);
        }

        /* ===== Permukaan Kartu ===== */
        .pwa-card {
            background: #FFFFFF;
            border: 1px solid rgba(226, 232, 240, 0.85);
            border-radius: 20px;
            box-shadow: var(--sh-card);
            transition: box-shadow .2s var(--ease-spring), border-color .15s ease-out;
        }

        .pwa-sub { color: var(--muted); }

        .pwa-h2 {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 14.5px;
            font-weight: 750;
            letter-spacing: -.015em;
            color: var(--ink);
        }

        .pwa-chip-meta {
            display: inline-flex;
            align-items: center;
            border-radius: 8px;
            background: #F1F5F9;
            color: var(--ink-soft);
            font-size: 11px;
            font-weight: 600;
            padding: 3px 8px;
            border: 1px solid rgba(226, 232, 240, 0.6);
        }

        .pwa-badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            font-size: 10.5px;
            font-weight: 700;
            padding: 3.5px 10px;
            letter-spacing: .01em;
        }

        /* Cincin Progres Modern */
        .pwa-ring {
            --p: 0;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: conic-gradient(var(--brand-700) calc(var(--p) * 1%), #E2E8F0 0);
            display: grid;
            place-items: center;
            transition: background 0.5s var(--ease-spring);
        }

        .pwa-ring > span {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: #FFFFFF;
            display: grid;
            place-items: center;
            font-size: 12px;
            font-weight: 800;
            color: var(--brand-700);
            box-shadow: inset 0 0 0 1px rgba(226, 232, 240, 0.8);
        }

        /* ===== Rekayasa Animasi (Emil Kowalski Philosophy) ===== */
        @keyframes pwaPopIn {
            0% { opacity: 0; transform: translateY(6px); }
            100% { opacity: 1; transform: translateY(0); }
        }

        @keyframes pwaFabFloat {
            0% { opacity: 0; transform: translateY(12px) scale(.88); }
            100% { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes pwaGrow {
            from { width: 0; }
        }

        @keyframes pwaPulseGlow {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: .85; }
        }

        .pop-in {
            animation: pwaPopIn .32s var(--ease-spring) both;
            animation-delay: var(--d, 0ms);
        }

        .grow-bar {
            animation: pwaGrow .65s var(--ease-spring) both;
            animation-delay: var(--d, 80ms);
        }

        .pulse-glow {
            animation: pwaPulseGlow 2.4s ease-in-out infinite;
        }

        /* Respons Sentuhan Instan (60ms tactile latency) */
        .press {
            touch-action: manipulation;
            transition: transform 70ms var(--ease-fast), filter 70ms ease-out, box-shadow .18s var(--ease-spring);
            user-select: none;
            -webkit-user-select: none;
            -webkit-touch-callout: none;
            will-change: transform;
        }

        .press:active {
            transform: scale(.968) translateZ(0);
            filter: brightness(.97);
        }

        .pwa-root :where(a, button, input, select, textarea):focus-visible {
            outline: 2.5px solid var(--brand-600);
            outline-offset: 2.5px;
        }

        .pwa-root :where(button, a, [role="button"]) {
            touch-action: manipulation;
        }

        .pwa-root :where(button, [role="button"]) {
            user-select: none;
            -webkit-user-select: none;
            -webkit-touch-callout: none;
        }

        /* ===== Transisi Halaman PWA ===== */
        /* Halaman lama luncur ke arah tab baru; halaman baru muncul dari sisi itu. */
        .pwa-view {
            animation: pwaViewIn 340ms var(--ease-spring) both;
        }

        @keyframes pwaViewIn {
            from { opacity: 0; transform: translateX(calc(var(--swipe-dir, 0) * 20px)) scale(.992); }
            to { opacity: 1; transform: translateX(0) scale(1); }
        }

        html.pwa-leaving .pwa-view {
            animation: pwaViewOut 150ms var(--ease-fast) both;
        }

        @keyframes pwaViewOut {
            from { opacity: 1; transform: translateX(0) scale(1); }
            to { opacity: 0; transform: translateX(calc(var(--swipe-dir, 0) * -14px)) scale(.996); }
        }

        /* Pil indikator posisi tab: meluncur antar item via transform, tanpa layout thrash. */
        .pwa-nav-pill {
            position: absolute;
            top: 8px;
            bottom: calc(8px + env(safe-area-inset-bottom, 0px));
            left: 0;
            border-radius: 14px;
            background: var(--brand-50);
            box-shadow: inset 0 0 0 1.5px rgba(37, 99, 235, 0.12);
            opacity: 0;
            transform: translateX(var(--pill-x, 0)) scale(.8);
            transition: transform 320ms var(--ease-spring), width 320ms var(--ease-spring), opacity 200ms ease-out;
            pointer-events: none;
            will-change: transform;
        }

        .pwa-nav-pill[data-on="true"] { opacity: 1; transform: translateX(var(--pill-x, 0)) scale(1); }

        /* ===== Navigasi Bawah Dock ===== */
        .pwa-nav {
            background: rgba(255, 255, 255, 0.92);
            border-top: 1px solid rgba(226, 232, 240, 0.85);
            backdrop-filter: blur(24px) saturate(180%);
            -webkit-backdrop-filter: blur(24px) saturate(180%);
            box-shadow: 0 -8px 25px -8px rgba(15, 23, 42, 0.08);
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }

        .pwa-nav-item {
            position: relative;
            z-index: 1;
            color: #64748B;
            border-radius: 14px;
            min-height: 3.25rem;
            padding: 6px 0 4px;
            touch-action: manipulation;
            transition: color .22s var(--ease-spring), transform 70ms ease-out;
            user-select: none;
            -webkit-user-select: none;
        }

        .pwa-nav-item svg {
            transition: transform .3s var(--ease-spring);
        }

        .pwa-nav-item:active { transform: scale(.93); }
        .pwa-nav-item:active svg { transform: scale(1.08); }

        .pwa-nav-item[data-active="true"] {
            color: var(--brand-700);
        }

        .pwa-nav-item[data-active="true"] svg { transform: translateY(-1px); }

        .pwa-fab {
            background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 55%, #4F46E5 100%);
            box-shadow: 0 10px 26px -6px rgba(37, 99, 235, 0.52), 0 0 0 4.5px #FFFFFF, 0 0 0 6px rgba(37, 99, 235, 0.15);
            animation: pwaFabFloat .42s var(--ease-spring) both;
            will-change: transform;
        }

        .pwa-fab:active { transform: scale(.91); }

        /* ===== Form & Input Ergonomis ===== */
        .pwa-field {
            width: 100%;
            border: 1.5px solid var(--line);
            background: #FFFFFF;
            border-radius: 14px;
            padding: .75rem 1rem;
            font-size: 0.9375rem;
            font-weight: 500;
            color: var(--ink);
            transition: border-color .15s ease-out, box-shadow .2s var(--ease-spring), background .15s ease-out;
        }

        .pwa-field::placeholder { color: #94A3B8; font-weight: 400; }

        .pwa-field:focus {
            outline: none;
            border-color: var(--brand-600);
            box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.14);
        }

        .pwa-label {
            display: block;
            font-size: 11.5px;
            font-weight: 700;
            letter-spacing: .02em;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: .4rem;
        }

        .pwa-chip {
            border: 1.5px solid var(--line);
            background: #FFFFFF;
            border-radius: 999px;
            padding: .45rem .9rem;
            font-size: 12px;
            font-weight: 600;
            color: var(--ink-soft);
            min-height: 2.75rem;
            transition: color .15s ease-out, background .15s ease-out, border-color .15s ease-out, transform 70ms ease-out, box-shadow .2s var(--ease-spring);
        }

        .pwa-chip:has(input:checked) {
            background: var(--brand-700);
            border-color: var(--brand-700);
            color: #FFFFFF;
            box-shadow: var(--sh-brand);
        }

        /* Mode standalone PWA */
        @media all and (display-mode: standalone) {
            [data-hide-standalone] { display: none !important; }
        }

        html.is-standalone [data-hide-standalone] { display: none !important; }

        @media (hover: hover) and (pointer: fine) {
            .press:hover { filter: brightness(.985); }
            .pwa-nav-item:hover { color: var(--brand-700); background: rgba(239, 246, 255, 0.7); }
        }

        @media (prefers-reduced-transparency: reduce) {
            .pwa-card, .pwa-nav {
                background: #FFFFFF;
                backdrop-filter: none;
                -webkit-backdrop-filter: none;
            }
        }

        @media (prefers-contrast: more) {
            .pwa-card { background: #FFFFFF; border-color: #64748B; }
            .pwa-nav { background: #FFFFFF; border-top-color: #475569; }
            .pwa-sub { color: #334155; }
        }

        @media (prefers-reduced-motion: reduce) {
            .pop-in, .pwa-fab, .grow-bar, .pulse-glow { animation: none !important; }
            .press, .pwa-nav-item, .pwa-nav-item svg, .pwa-chip { transition-duration: 0.01ms !important; }
            .pwa-nav-pill { transition: none !important; }
            .pwa-view, html.pwa-leaving .pwa-view { animation: none !important; }
        }
    </style>
</head>

<body class="pwa-body antialiased">
    <div class="pwa-view mx-auto min-h-[100dvh] w-full max-w-[430px] pb-[calc(8rem+env(safe-area-inset-bottom,0px))]">
        @if ($header)
            <div class="pwa-hero px-5 pb-8 text-white">
                {{ $header }}
            </div>
        @endif

        <main class="px-5 {{ $header ? '-mt-4' : 'pt-[calc(1.5rem+env(safe-area-inset-top,0px))]' }} space-y-5">
            @if (session('success'))
                <div class="pop-in rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-[12.5px] font-semibold text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="pop-in rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-[12.5px] font-semibold text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    @if ($showInstallBanner)
        <x-install-banner variant="app" />
    @endif

    <!-- Navigasi Bawah Dock + Tombol Generate AI -->
    <nav class="pwa-nav fixed bottom-0 left-0 right-0 z-30" aria-label="Navigasi utama">
        <div class="relative mx-auto grid max-w-[430px] grid-cols-5 items-end gap-1 px-3 pt-2 pb-1.5" id="pwa-nav-grid">
            <span class="pwa-nav-pill" id="pwa-nav-pill" aria-hidden="true" data-on="false"></span>
            @php
                $navItems = [
                    ['key' => 'home', 'label' => 'Home', 'url' => route('pwa.home'), 'icon' => 'M3 10.5 12 3l9 7.5M5.5 9.5V20h13V9.5'],
                    ['key' => 'rpp', 'label' => 'Modul', 'url' => route('pwa.rpp.index'), 'icon' => 'M7 4h8l4 4v12H7zM15 4v4h4M9.5 13h6M9.5 16.5h4'],
                ];
                $navItemsRight = [
                    [
                        'key' => 'detail',
                        'label' => 'Detail',
                        'url' => $detailRpp ? route('pwa.rpp.detail', $detailRpp) : route('pwa.rpp.index'),
                        'icon' => 'M4 5.5h16M4 10h16M4 14.5h11M4 19h8',
                    ],
                    ['key' => 'akun', 'label' => 'Akun', 'url' => route('pwa.akun'), 'icon' => 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8ZM4.5 20a7.5 7.5 0 0 1 15 0'],
                ];
            @endphp

            @foreach ($navItems as $item)
                <a href="{{ $item['url'] }}" class="pwa-nav-item flex flex-col items-center gap-0.5" data-nav-key="{{ $item['key'] }}" data-active="{{ $active === $item['key'] ? 'true' : 'false' }}"
                    @if ($active === $item['key']) aria-current="page" @endif>
                    <svg aria-hidden="true" class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                    </svg>
                    <span class="text-[10.5px] font-bold">{{ $item['label'] }}</span>
                </a>
            @endforeach

            <div class="flex justify-center">
                <a href="{{ route('pwa.rpp.create') }}" aria-label="Buat modul ajar"
                    class="pwa-fab press relative -mt-9 flex h-[62px] w-[62px] items-center justify-center rounded-full">
                    <img src="{{ asset('logo.png') }}" alt="" class="h-9 w-9 object-contain drop-shadow-sm">
                </a>
            </div>

            @foreach ($navItemsRight as $item)
                <a href="{{ $item['url'] }}" class="pwa-nav-item flex flex-col items-center gap-0.5"
                    data-nav-key="{{ $item['key'] }}"
                    data-active="{{ $active === $item['key'] ? 'true' : 'false' }}"
                    @if ($active === $item['key']) aria-current="page" @endif
                    @if ($item['hideStandalone'] ?? false) data-hide-standalone @endif>
                    <svg aria-hidden="true" class="h-[22px] w-[22px]" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                    </svg>
                    <span class="text-[10.5px] font-bold">{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </nav>

    <script>
        (() => {
            // Urutan tab menentukan arah geser transisi halaman.
            const ORDER = ['home', 'rpp', 'create', 'detail', 'akun'];
            const KEY = 'pwa:lastTab';
            const KEY_FROM = KEY + ':from';
            const active = @js($active);
            const html = document.documentElement;
            const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

            // Arah masuk: +1 dari kanan, -1 dari kiri, 0 tanpa geser (refresh/halaman non-nav).
            try {
                const last = sessionStorage.getItem(KEY);
                const from = last ? ORDER.indexOf(last) : -1;
                const to = ORDER.indexOf(active);
                if (from !== -1 && to !== -1 && from !== to) {
                    html.style.setProperty('--swipe-dir', to > from ? '1' : '-1');
                } else {
                    html.style.setProperty('--swipe-dir', '0');
                }
                sessionStorage.setItem(KEY, active);
            } catch (_) {
                html.style.setProperty('--swipe-dir', '0');
            }

            // Posisikan pil indikator di bawah item aktif.
            const pill = document.getElementById('pwa-nav-pill');
            const grid = document.getElementById('pwa-nav-grid');
            const current = grid?.querySelector('[data-nav-key="' + active + '"]');
            if (pill && grid && current) {
                const place = () => {
                    const itemBox = current.getBoundingClientRect();
                    const gridBox = grid.getBoundingClientRect();
                    pill.style.width = itemBox.width + 'px';
                    pill.style.setProperty('--pill-x', (itemBox.left - gridBox.left) + 'px');
                    pill.dataset.on = 'true';
                };
                const fromKey = html.style.getPropertyValue('--swipe-dir') !== '0' ? sessionStorage.getItem(KEY_FROM) : null;
                const fromEl = fromKey ? grid.querySelector('[data-nav-key="' + fromKey + '"]') : null;
                if (fromEl) {
                    // Mulai dari posisi tab lama, lalu meluncur ke tab aktif.
                    const placeAt = (el) => {
                        const itemBox = el.getBoundingClientRect();
                        const gridBox = grid.getBoundingClientRect();
                        pill.style.width = itemBox.width + 'px';
                        pill.style.setProperty('--pill-x', (itemBox.left - gridBox.left) + 'px');
                    };
                    pill.style.transition = 'none';
                    placeAt(fromEl);
                    pill.dataset.on = 'true';
                    requestAnimationFrame(() => requestAnimationFrame(() => {
                        pill.style.transition = '';
                        place();
                    }));
                } else {
                    pill.style.transition = 'none';
                    place();
                    requestAnimationFrame(() => requestAnimationFrame(() => { pill.style.transition = ''; }));
                }
                window.addEventListener('resize', place, { passive: true });
            }

            // Keluar halus saat ketuk item nav: halaman lama luncur keluar sebelum unload.
            grid?.addEventListener('click', (event) => {
                const link = event.target.closest('a.pwa-nav-item');
                if (!link || reduceMotion.matches) return;
                if (link.dataset.navKey === active) { event.preventDefault(); return; }
                if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return;
                event.preventDefault();
                const dir = ORDER.indexOf(link.dataset.navKey) > ORDER.indexOf(active) ? '1' : '-1';
                html.style.setProperty('--swipe-dir', dir);
                try { sessionStorage.setItem(KEY_FROM, active); } catch (_) {}
                html.classList.add('pwa-leaving');
                setTimeout(() => { window.location.href = link.href; }, 140);
            });

            // Halaman masuk dari bfcache: pastikan state keluar dibersihkan.
            window.addEventListener('pageshow', (event) => {
                if (event.persisted) {
                    html.classList.remove('pwa-leaving');
                    try { sessionStorage.removeItem(KEY_FROM); } catch (_) {}
                }
            });
        })();
    </script>

</body>

</html>
