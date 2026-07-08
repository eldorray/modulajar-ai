<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="overflow-x-hidden">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        <link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased overflow-x-hidden bg-white">
        <div class="relative min-h-screen w-full overflow-hidden bg-[radial-gradient(circle_at_top_left,_rgba(251,146,60,0.18),_transparent_34rem),linear-gradient(135deg,_#fff7ed_0%,_#ffffff_45%,_#f8fafc_100%)]">
            <div class="pointer-events-none absolute -left-28 top-24 h-72 w-72 rounded-full bg-orange-200/40 blur-3xl"></div>
            <div class="pointer-events-none absolute -right-28 bottom-12 h-80 w-80 rounded-full bg-amber-100/70 blur-3xl"></div>

            <div class="relative z-10 mx-auto flex min-h-screen w-full max-w-6xl flex-col px-4 py-5 sm:px-6 lg:px-8">
                <header class="flex items-center justify-between gap-4">
                    <a href="/" class="inline-flex min-h-11 min-w-0 items-center gap-3 rounded-full pr-3 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-400 to-orange-600 shadow-lg shadow-orange-500/20">
                            <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </span>
                        <span class="truncate text-base font-extrabold tracking-tight text-gray-950 sm:text-lg">RPP Generator AI</span>
                    </a>

                    <a href="/" class="inline-flex min-h-11 shrink-0 items-center justify-center rounded-full border border-orange-100 bg-white/80 px-4 text-sm font-semibold text-gray-700 shadow-sm shadow-orange-900/5 transition hover:border-orange-200 hover:bg-white focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2">
                        Beranda
                    </a>
                </header>

                <main class="flex flex-1 items-center py-8 sm:py-10 lg:py-12">
                    <div class="grid w-full items-center gap-8 lg:grid-cols-[minmax(0,1fr)_minmax(380px,440px)] lg:gap-12">
                        <section class="hidden lg:block">
                            <p class="mb-4 text-xs font-bold uppercase tracking-[0.2em] text-orange-600">Ruang kerja guru</p>
                            <h1 class="max-w-xl text-5xl font-black leading-[1.02] tracking-tight text-gray-950">
                                Siapkan perangkat ajar tanpa memulai dari halaman kosong.
                            </h1>
                            <p class="mt-6 max-w-lg text-base font-medium leading-8 text-gray-600">
                                RPP Generator AI membantu guru menyusun modul ajar, soal STS, ekspor PDF, dan memantau token dalam satu dashboard yang rapi.
                            </p>

                            <div class="mt-10 grid max-w-xl grid-cols-2 gap-4">
                                <div class="rounded-3xl border border-white/80 bg-white/70 p-5 shadow-xl shadow-orange-900/5 backdrop-blur">
                                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-2xl bg-orange-100 text-orange-600">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M7 4h7l3 3v13H7V4z" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-gray-950">Modul ajar siap ekspor</p>
                                    <p class="mt-2 text-xs leading-5 text-gray-500">Struktur dokumen rapi untuk kebutuhan sekolah.</p>
                                </div>

                                <div class="rounded-3xl border border-white/80 bg-white/70 p-5 shadow-xl shadow-orange-900/5 backdrop-blur">
                                    <div class="mb-4 flex h-10 w-10 items-center justify-center rounded-2xl bg-gray-900 text-white">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17a4 4 0 100-8 4 4 0 000 8zm7-1.5L21 18m-6-9V5m-4 4H7" />
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-gray-950">STS lebih cepat</p>
                                    <p class="mt-2 text-xs leading-5 text-gray-500">Buat soal dan kunci jawaban dari satu alur.</p>
                                </div>
                            </div>
                        </section>

                        <section class="mx-auto w-full max-w-md">
                            <div class="overflow-hidden rounded-[1.75rem] border border-white/80 bg-white/90 p-5 shadow-2xl shadow-orange-900/10 backdrop-blur sm:p-7">
                                {{ $slot }}
                            </div>
                        </section>
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
