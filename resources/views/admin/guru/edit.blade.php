<x-app-layout>
    <x-slot name="header">Edit Guru</x-slot>

    <div class="space-y-6">
        <!-- Back Button -->
        <div>
            <a href="{{ route('admin.guru.index') }}" class="btn btn-ghost">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                    </path>
                </svg>
                Kembali
            </a>
        </div>

        <x-ui.card class="p-6">
            <div class="mb-6">
                <h2 class="text-xl font-bold text-[hsl(var(--foreground))]">Edit Data Guru</h2>
                <p class="text-[hsl(var(--muted-foreground))]">NIK: {{ $guru->nik }}</p>
            </div>

            <form action="{{ route('admin.guru.update', $guru) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nama -->
                    <div>
                        <label for="nama" class="block text-sm font-medium text-[hsl(var(--foreground))] mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nama" name="nama" value="{{ old('nama', $guru->nama) }}"
                            required class="input w-full">
                        @error('nama')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- NIP -->
                    <div>
                        <label for="nip" class="block text-sm font-medium text-[hsl(var(--foreground))] mb-2">
                            NIP
                        </label>
                        <input type="text" id="nip" name="nip" value="{{ old('nip', $guru->nip) }}"
                            class="input w-full">
                        @error('nip')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jenis Kelamin -->
                    <div>
                        <label for="jenis_kelamin" class="block text-sm font-medium text-[hsl(var(--foreground))] mb-2">
                            Jenis Kelamin
                        </label>
                        <select id="jenis_kelamin" name="jenis_kelamin" class="input w-full">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="L"
                                {{ old('jenis_kelamin', $guru->jenis_kelamin) === 'L' ? 'selected' : '' }}>Laki-laki
                            </option>
                            <option value="P"
                                {{ old('jenis_kelamin', $guru->jenis_kelamin) === 'P' ? 'selected' : '' }}>Perempuan
                            </option>
                        </select>
                        @error('jenis_kelamin')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tempat Lahir -->
                    <div>
                        <label for="tempat_lahir" class="block text-sm font-medium text-[hsl(var(--foreground))] mb-2">
                            Tempat Lahir
                        </label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir"
                            value="{{ old('tempat_lahir', $guru->tempat_lahir) }}" class="input w-full">
                        @error('tempat_lahir')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tanggal Lahir -->
                    <div>
                        <label for="tanggal_lahir" class="block text-sm font-medium text-[hsl(var(--foreground))] mb-2">
                            Tanggal Lahir
                        </label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir"
                            value="{{ old('tanggal_lahir', $guru->tanggal_lahir?->format('Y-m-d')) }}"
                            class="input w-full">
                        @error('tanggal_lahir')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- No HP -->
                    <div>
                        <label for="no_hp" class="block text-sm font-medium text-[hsl(var(--foreground))] mb-2">
                            No. HP
                        </label>
                        <input type="text" id="no_hp" name="no_hp" value="{{ old('no_hp', $guru->no_hp) }}"
                            class="input w-full">
                        @error('no_hp')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-[hsl(var(--foreground))] mb-2">
                            Email
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email', $guru->email) }}"
                            class="input w-full">
                        @error('email')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Jabatan -->
                    <div>
                        <label for="jabatan" class="block text-sm font-medium text-[hsl(var(--foreground))] mb-2">
                            Jabatan
                        </label>
                        <input type="text" id="jabatan" name="jabatan"
                            value="{{ old('jabatan', $guru->jabatan) }}" class="input w-full">
                        @error('jabatan')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-[hsl(var(--foreground))] mb-2">
                            Status
                        </label>
                        <input type="text" id="status" name="status" value="{{ old('status', $guru->status) }}"
                            class="input w-full" placeholder="Contoh: PNS, Honorer, GTY">
                        @error('status')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Alamat -->
                    <div class="md:col-span-2">
                        <label for="alamat" class="block text-sm font-medium text-[hsl(var(--foreground))] mb-2">
                            Alamat
                        </label>
                        <textarea id="alamat" name="alamat" rows="3" class="input w-full">{{ old('alamat', $guru->alamat) }}</textarea>
                        @error('alamat')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-6 pt-6 border-t border-[hsl(var(--border))]">
                    <a href="{{ route('admin.guru.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </x-ui.card>
    </div>
</x-app-layout>
