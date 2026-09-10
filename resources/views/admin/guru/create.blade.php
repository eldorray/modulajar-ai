<x-app-layout>
    <x-slot name="header">Tambah Guru</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <a href="{{ route('admin.guru.index') }}" class="btn btn-ghost">Kembali ke Daftar Guru</a>

        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">Tambah Guru</h2>
                <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Buat profil guru sekaligus akun login. Kolom bertanda * wajib diisi.</p>
            </x-slot>

            <form action="{{ route('admin.guru.store') }}" method="POST" class="space-y-6">
                @csrf

                <fieldset class="space-y-4">
                    <legend class="text-lg font-semibold text-[hsl(var(--foreground))]">Identitas Guru</legend>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-ui.input name="nama" label="Nama Lengkap *" :value="old('nama')" :error="$errors->first('nama')" maxlength="255" autocomplete="name" required />
                        <x-ui.input name="nik" label="NIK *" placeholder="16 digit NIK" :value="old('nik')" :error="$errors->first('nik')" inputmode="numeric" pattern="[0-9]{16}" minlength="16" maxlength="16" required />
                        <x-ui.input name="nip" label="NIP" :value="old('nip')" :error="$errors->first('nip')" maxlength="50" />
                        <x-ui.select name="jenis_kelamin" label="Jenis Kelamin" :options="['L' => 'Laki-laki', 'P' => 'Perempuan']" placeholder="Pilih Jenis Kelamin" :value="old('jenis_kelamin')" :error="$errors->first('jenis_kelamin')" />
                        <x-ui.input name="tempat_lahir" label="Tempat Lahir" :value="old('tempat_lahir')" :error="$errors->first('tempat_lahir')" maxlength="100" />
                        <x-ui.input type="date" name="tanggal_lahir" label="Tanggal Lahir" :value="old('tanggal_lahir')" :error="$errors->first('tanggal_lahir')" />
                        <x-ui.input type="tel" name="no_hp" label="No. HP" :value="old('no_hp')" :error="$errors->first('no_hp')" maxlength="20" autocomplete="tel" />
                        <x-ui.input name="jabatan" label="Jabatan" :value="old('jabatan')" :error="$errors->first('jabatan')" maxlength="100" />
                        <x-ui.input name="status" label="Status Kepegawaian" placeholder="Contoh: PNS, Honorer, GTY" :value="old('status')" :error="$errors->first('status')" maxlength="50" />
                        <div class="md:col-span-2">
                            <label for="alamat" class="block text-sm font-medium text-[hsl(var(--foreground))] mb-2">Alamat</label>
                            <textarea id="alamat" name="alamat" rows="3" class="input w-full" autocomplete="street-address">{{ old('alamat') }}</textarea>
                            @error('alamat')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </fieldset>

                <fieldset class="space-y-4 border-t border-[hsl(var(--border))] pt-6">
                    <legend class="text-lg font-semibold text-[hsl(var(--foreground))]">Akun Login Guru</legend>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Email ini digunakan untuk login dan kontak guru. Akun otomatis memiliki role Guru. Simpan dan sampaikan password kepada guru secara aman; password tidak ditampilkan kembali setelah disimpan.</p>
                    <x-ui.input type="email" name="email" label="Email Login *" placeholder="guru@sekolah.sch.id" :value="old('email')" :error="$errors->first('email')" maxlength="255" autocomplete="off" required />
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <x-ui.input type="password" name="password" label="Password *" placeholder="Minimal 8 karakter" :error="$errors->first('password')" autocomplete="new-password" minlength="8" required />
                        <x-ui.input type="password" name="password_confirmation" label="Konfirmasi Password *" placeholder="Ulangi password" :error="$errors->first('password_confirmation')" autocomplete="new-password" minlength="8" required />
                    </div>
                </fieldset>

                <div class="flex flex-col-reverse sm:flex-row sm:justify-end gap-3 pt-4 border-t border-[hsl(var(--border))]">
                    <a href="{{ route('admin.guru.index') }}" class="btn btn-outline">Batal</a>
                    <x-ui.button type="submit" variant="primary">Simpan Guru</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
