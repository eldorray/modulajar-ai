<x-pwa-layout title="Ubah Profil" active="akun">
    <x-slot name="header">
        <div class="relative z-10 flex items-center gap-3.5 pt-1">
            <a href="{{ route('pwa.akun') }}" class="press flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15 backdrop-blur-md ring-1 ring-white/25 text-white" aria-label="Kembali ke akun">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="min-w-0">
                <p class="pwa-hero-eyebrow">Akun</p>
                <h1 class="pwa-hero-title">Ubah Profil Akun</h1>
            </div>
        </div>
    </x-slot>

    <!-- Identitas -->
    <section class="pwa-card pop-in p-5" style="--d: 40ms">
        <h2 class="pwa-h2">Informasi Profil</h2>
        <p class="pwa-sub mt-0.5 text-[11.5px]">Nama tampil di dokumen & aplikasi.</p>

        <form method="POST" action="{{ route('profile.update') }}" class="mt-4 space-y-4">
            @csrf
            @method('patch')

            <div>
                <label for="name" class="pwa-label">Nama Lengkap</label>
                <input id="name" name="name" type="text" class="pwa-field" value="{{ old('name', $user->name) }}" required autocomplete="name">
                @error('name') <p class="mt-1.5 text-[11.5px] font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="email" class="pwa-label">Email</label>
                <input id="email" name="email" type="email" class="pwa-field" value="{{ old('email', $user->email) }}" required autocomplete="username">
                @error('email') <p class="mt-1.5 text-[11.5px] font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="press w-full rounded-2xl py-3.5 text-[13px] font-bold text-white"
                style="background: linear-gradient(135deg, #1D4ED8, #2563EB); box-shadow: var(--sh-brand)">
                Simpan Profil
            </button>
        </form>
    </section>

    <!-- Password -->
    <section class="pwa-card pop-in p-5" style="--d: 80ms">
        <h2 class="pwa-h2">Ganti Password</h2>
        <p class="pwa-sub mt-0.5 text-[11.5px]">Pakai password panjang & acak.</p>

        <form method="POST" action="{{ route('password.update') }}" class="mt-4 space-y-4">
            @csrf
            @method('put')

            <div>
                <label for="current_password" class="pwa-label">Password Sekarang</label>
                <input id="current_password" name="current_password" type="password" class="pwa-field" autocomplete="current-password">
                @if ($errors->updatePassword->has('current_password'))
                    <p class="mt-1.5 text-[11.5px] font-semibold text-rose-600">{{ $errors->updatePassword->first('current_password') }}</p>
                @endif
            </div>

            <div>
                <label for="password" class="pwa-label">Password Baru</label>
                <input id="password" name="password" type="password" class="pwa-field" autocomplete="new-password">
                @if ($errors->updatePassword->has('password'))
                    <p class="mt-1.5 text-[11.5px] font-semibold text-rose-600">{{ $errors->updatePassword->first('password') }}</p>
                @endif
            </div>

            <div>
                <label for="password_confirmation" class="pwa-label">Ulangi Password Baru</label>
                <input id="password_confirmation" name="password_confirmation" type="password" class="pwa-field" autocomplete="new-password">
                @if ($errors->updatePassword->has('password_confirmation'))
                    <p class="mt-1.5 text-[11.5px] font-semibold text-rose-600">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                @endif
            </div>

            <button type="submit" class="press w-full rounded-2xl py-3.5 text-[13px] font-bold text-white"
                style="background: linear-gradient(135deg, #1D4ED8, #2563EB); box-shadow: var(--sh-brand)">
                Perbarui Password
            </button>
        </form>
    </section>
</x-pwa-layout>
