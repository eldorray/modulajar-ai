<x-guest-layout>
    <div class="mb-7 text-center sm:text-left">
        <div class="mx-auto mb-5 flex h-14 w-14 items-center justify-center rounded-2xl bg-orange-100 text-orange-600 sm:mx-0">
            <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4" />
            </svg>
        </div>

        <p class="text-xs font-bold uppercase tracking-[0.18em] text-orange-600">Selamat datang kembali</p>
        <h1 class="mt-3 text-3xl font-black tracking-tight text-gray-950 sm:text-4xl">Masuk ke RPP Generator AI</h1>
        <p class="mt-3 text-sm font-medium leading-6 text-gray-500">
            Modul ajar, STS, dan dashboard guru dalam satu ruang kerja.
        </p>
    </div>

    <x-auth-session-status class="mb-5 rounded-2xl border border-green-100 bg-green-50 px-4 py-3 text-sm font-medium text-green-700" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="mb-2 block text-sm font-bold text-gray-800">Email</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-18 8h18V6H3v10z" />
                    </svg>
                </span>
                <input
                    id="email"
                    class="block min-h-12 w-full rounded-2xl border-gray-200 bg-gray-50 pl-12 pr-4 text-base font-medium text-gray-900 shadow-sm transition placeholder:text-gray-400 focus:border-orange-500 focus:bg-white focus:ring-orange-500"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="nama@sekolah.id"
                >
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div x-data="{ showPassword: false }">
            <label for="password" class="mb-2 block text-sm font-bold text-gray-800">Password</label>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4" />
                    </svg>
                </span>
                <input
                    id="password"
                    class="block min-h-12 w-full rounded-2xl border-gray-200 bg-gray-50 pl-12 pr-14 text-base font-medium text-gray-900 shadow-sm transition placeholder:text-gray-400 focus:border-orange-500 focus:bg-white focus:ring-orange-500"
                    x-bind:type="showPassword ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan password"
                >
                <button
                    type="button"
                    class="absolute inset-y-1 right-1 inline-flex min-h-11 min-w-11 cursor-pointer items-center justify-center rounded-xl text-gray-500 transition hover:bg-white hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500"
                    x-on:click="showPassword = !showPassword"
                    x-bind:aria-label="showPassword ? 'Sembunyikan password' : 'Tampilkan password'"
                >
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3l18 18M10.58 10.58a2 2 0 102.83 2.83M9.88 5.08A9.76 9.76 0 0112 5c4.48 0 8.27 2.94 9.54 7a10.03 10.03 0 01-2.5 4.08M6.11 6.11A10.06 10.06 0 002.46 12c.76 2.43 2.42 4.45 4.54 5.64A9.9 9.9 0 0012 19c.73 0 1.45-.08 2.14-.24" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-3 text-sm sm:flex-row sm:items-center sm:justify-between">
            <label for="remember_me" class="inline-flex min-h-11 cursor-pointer items-center gap-3 rounded-2xl text-gray-600">
                <input
                    id="remember_me"
                    type="checkbox"
                    class="h-5 w-5 rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500"
                    name="remember"
                >
                <span class="font-medium">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a
                    class="inline-flex min-h-11 items-center justify-center rounded-2xl px-1 font-bold text-orange-600 transition hover:text-orange-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2"
                    href="{{ route('password.request') }}"
                >
                    Lupa password?
                </a>
            @endif
        </div>

        <button type="submit" class="inline-flex min-h-12 w-full cursor-pointer items-center justify-center rounded-2xl bg-gray-950 px-6 py-3 text-sm font-bold text-white shadow-xl shadow-gray-950/15 transition hover:-translate-y-0.5 hover:bg-black focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-500 focus-visible:ring-offset-2">
            Masuk sekarang
            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
        </button>
    </form>

    <div class="mt-6 rounded-2xl border border-orange-100 bg-orange-50/70 px-4 py-3">
        <p class="text-center text-xs font-semibold leading-5 text-orange-800">
            Akses akun dibuat oleh admin sekolah. Hubungi admin jika belum memiliki akun.
        </p>
    </div>
</x-guest-layout>
