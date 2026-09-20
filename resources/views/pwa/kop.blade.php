<x-pwa-layout title="Kop & Sekolah" active="akun">
    <x-slot name="header">
        <div class="relative z-10 flex items-center gap-3.5 pt-1">
            <a href="{{ route('pwa.akun') }}" class="press flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15 backdrop-blur-md ring-1 ring-white/25 text-white" aria-label="Kembali ke akun">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div class="min-w-0">
                <p class="pwa-hero-eyebrow">Pengaturan</p>
                <h1 class="pwa-hero-title">Kop & Sekolah</h1>
            </div>
        </div>
    </x-slot>

    <!-- Tab Jenjang -->
    <div class="pop-in flex gap-2 overflow-x-auto scrollbar-hide -mx-5 px-5 pb-1" style="--d: 40ms">
        @foreach (\App\Models\SchoolSetting::JENJANG as $j)
            <a href="{{ route('pwa.kop', ['unit' => $j]) }}"
                class="press shrink-0 rounded-full px-4 py-2 text-[12px] font-bold transition-colors"
                style="{{ $unit === $j
                    ? 'background: var(--brand-700); color: #fff; box-shadow: var(--sh-brand)'
                    : 'background: #fff; color: var(--ink-soft); border: 1.5px solid var(--line)' }}">
                {{ $j }}
            </a>
        @endforeach
    </div>

    <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <input type="hidden" name="jenjang" value="{{ $unit }}">
        <input type="hidden" name="form_context" value="pwa">

        <!-- Logo Kiri -->
        <section class="pwa-card pop-in p-5" style="--d: 80ms">
            <h2 class="pwa-h2">Logo Kiri</h2>
            <div class="mt-3 flex items-start gap-4">
                @if ($settings->logo)
                    <div class="relative shrink-0">
                        <img src="{{ Storage::url($settings->logo) }}" alt="Logo" class="h-20 w-20 rounded-2xl border object-contain bg-white p-1.5" style="border-color: var(--line)">
                        <button type="button" onclick="if(confirm('Hapus logo kiri?')) document.getElementById('del-logo').submit()"
                            class="press absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-rose-500 text-[13px] font-black text-white shadow-md">×</button>
                    </div>
                @else
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl border-2 border-dashed text-slate-300" style="border-color: var(--line)">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif
                <div class="flex-1">
                    <input type="file" name="logo" accept="image/png,image/jpeg,image/jpg" class="pwa-field text-[12px]">
                    <p class="pwa-sub mt-1 text-[11px]">PNG/JPG, maks 2MB.</p>
                    @error('logo') <p class="mt-1 text-[11.5px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <!-- Logo Kanan -->
        <section class="pwa-card pop-in p-5" style="--d: 100ms">
            <h2 class="pwa-h2">Logo Kanan</h2>
            <div class="mt-3 flex items-start gap-4">
                @if ($settings->logo_kanan)
                    <div class="relative shrink-0">
                        <img src="{{ Storage::url($settings->logo_kanan) }}" alt="Logo kanan" class="h-20 w-20 rounded-2xl border object-contain bg-white p-1.5" style="border-color: var(--line)">
                        <button type="button" onclick="if(confirm('Hapus logo kanan?')) document.getElementById('del-logo-kanan').submit()"
                            class="press absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-rose-500 text-[13px] font-black text-white shadow-md">×</button>
                    </div>
                @else
                    <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-2xl border-2 border-dashed text-slate-300" style="border-color: var(--line)">
                        <svg class="h-7 w-7" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif
                <div class="flex-1">
                    <input type="file" name="logo_kanan" accept="image/png,image/jpeg,image/jpg" class="pwa-field text-[12px]">
                    <p class="pwa-sub mt-1 text-[11px]">Sisi kanan kop surat.</p>
                    @error('logo_kanan') <p class="mt-1 text-[11.5px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <!-- Kop Surat -->
        <section class="pwa-card pop-in p-5" style="--d: 120ms">
            <h2 class="pwa-h2">Kop Surat (Gambar)</h2>
            <p class="pwa-sub mt-0.5 text-[11px] leading-4">Bila diisi, gambar ini dipakai langsung sebagai header PDF.</p>
            <div class="mt-3 space-y-3">
                @if ($settings->kop_surat)
                    <div class="relative">
                        <img src="{{ Storage::url($settings->kop_surat) }}" alt="Kop surat" class="w-full rounded-2xl border object-contain bg-white p-2" style="border-color: var(--line)">
                        <button type="button" onclick="if(confirm('Hapus kop surat?')) document.getElementById('del-kop').submit()"
                            class="press absolute -right-2 -top-2 flex h-6 w-6 items-center justify-center rounded-full bg-rose-500 text-[13px] font-black text-white shadow-md">×</button>
                    </div>
                @endif
                <input type="file" name="kop_surat" accept="image/png,image/jpeg,image/jpg" class="pwa-field text-[12px]">
                <p class="pwa-sub text-[11px]">PNG/JPG, maks 4MB. Lebar penuh (min 800px).</p>
                @error('kop_surat') <p class="mt-1 text-[11.5px] font-semibold text-rose-600">{{ $message }}</p> @enderror
            </div>
        </section>

        <!-- Identitas -->
        <section class="pwa-card pop-in p-5" style="--d: 140ms">
            <h2 class="pwa-h2">Identitas Sekolah</h2>
            <div class="mt-3 space-y-4">
                <div>
                    <label for="nama_sekolah" class="pwa-label">Nama Sekolah</label>
                    <input id="nama_sekolah" name="nama_sekolah" type="text" class="pwa-field" value="{{ old('nama_sekolah', $settings->nama_sekolah) }}" placeholder="Contoh: SMP Negeri 1 Jakarta">
                    @error('nama_sekolah') <p class="mt-1.5 text-[11.5px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label for="nsm" class="pwa-label">NSM</label>
                        <input id="nsm" name="nsm" type="text" inputmode="numeric" class="pwa-field" value="{{ old('nsm', $settings->nsm) }}">
                        @error('nsm') <p class="mt-1.5 text-[11.5px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="npsn" class="pwa-label">NPSN</label>
                        <input id="npsn" name="npsn" type="text" inputmode="numeric" class="pwa-field" value="{{ old('npsn', $settings->npsn) }}">
                        @error('npsn') <p class="mt-1.5 text-[11.5px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div>
                    <label for="alamat" class="pwa-label">Alamat</label>
                    <textarea id="alamat" name="alamat" rows="3" class="pwa-field">{{ old('alamat', $settings->alamat) }}</textarea>
                    @error('alamat') <p class="mt-1.5 text-[11.5px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </section>

        <button type="submit" class="press pop-in w-full rounded-2xl py-4 text-[13.5px] font-bold text-white" style="--d: 160ms; background: linear-gradient(135deg, #1D4ED8, #2563EB); box-shadow: var(--sh-brand)">
            Simpan Pengaturan {{ $unit }}
        </button>
    </form>

    <!-- Hidden delete forms -->
    <form id="del-logo" action="{{ route('settings.delete-logo') }}" method="POST" class="hidden">@csrf @method('DELETE')<input type="hidden" name="jenjang" value="{{ $unit }}"><input type="hidden" name="form_context" value="pwa"></form>
    <form id="del-logo-kanan" action="{{ route('settings.delete-logo-kanan') }}" method="POST" class="hidden">@csrf @method('DELETE')<input type="hidden" name="jenjang" value="{{ $unit }}"><input type="hidden" name="form_context" value="pwa"></form>
    <form id="del-kop" action="{{ route('settings.delete-kop-surat') }}" method="POST" class="hidden">@csrf @method('DELETE')<input type="hidden" name="jenjang" value="{{ $unit }}"><input type="hidden" name="form_context" value="pwa"></form>
</x-pwa-layout>
