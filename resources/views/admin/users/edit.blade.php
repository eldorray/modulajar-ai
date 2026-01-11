<x-app-layout>
    <x-slot name="header">Edit User</x-slot>

    <div class="max-w-2xl mx-auto">
        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">Edit User</h2>
                <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Perbarui data user.</p>
            </x-slot>

            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <x-ui.input
                    name="name"
                    label="Nama Lengkap"
                    placeholder="Masukkan nama lengkap"
                    :value="old('name', $user->name)"
                    :error="$errors->first('name')"
                    required
                />

                <x-ui.input
                    type="email"
                    name="email"
                    label="Email"
                    placeholder="Masukkan alamat email"
                    :value="old('email', $user->email)"
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
                    :value="old('role', $user->role)"
                    :error="$errors->first('role')"
                    required
                />

                <div class="pt-4 border-t border-[hsl(var(--border))]">
                    <p class="text-sm text-[hsl(var(--muted-foreground))] mb-4">Kosongkan password jika tidak ingin mengubah.</p>
                </div>

                <x-ui.input
                    type="password"
                    name="password"
                    label="Password Baru"
                    placeholder="Masukkan password baru"
                    :error="$errors->first('password')"
                />

                <x-ui.input
                    type="password"
                    name="password_confirmation"
                    label="Konfirmasi Password Baru"
                    placeholder="Ulangi password baru"
                />

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-[hsl(var(--border))]">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline">Batal</a>
                    <x-ui.button type="submit" variant="primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update User
                    </x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
