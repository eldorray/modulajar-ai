<x-app-layout>
    <x-slot name="header">Tambah User</x-slot>

    <div class="max-w-2xl mx-auto">
        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">Form Tambah User</h2>
                <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Buat akun user baru.</p>
            </x-slot>

            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                @csrf

                <x-ui.input
                    name="name"
                    label="Nama Lengkap"
                    placeholder="Masukkan nama lengkap"
                    :value="old('name')"
                    :error="$errors->first('name')"
                    required
                />

                <x-ui.input
                    type="email"
                    name="email"
                    label="Email"
                    placeholder="Masukkan alamat email"
                    :value="old('email')"
                    :error="$errors->first('email')"
                    required
                />

                <x-ui.select
                    name="role"
                    label="Role"
                    :options="[
                        'guru' => 'Guru',
                        'admin' => 'Admin',
                    ]"
                    placeholder="Pilih Role"
                    :value="old('role', 'guru')"
                    :error="$errors->first('role')"
                    required
                />

                <x-ui.input
                    type="password"
                    name="password"
                    label="Password"
                    placeholder="Masukkan password"
                    :error="$errors->first('password')"
                    required
                />

                <x-ui.input
                    type="password"
                    name="password_confirmation"
                    label="Konfirmasi Password"
                    placeholder="Ulangi password"
                    required
                />

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-[hsl(var(--border))]">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Batal</a>
                    <x-ui.button type="submit" variant="primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Simpan User
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
