<x-app-layout>
    <x-slot name="header">Pengaturan Sekolah</x-slot>

    <div class="max-w-3xl mx-auto">
        @if(session('success'))
        <x-ui.alert type="success" class="mb-4">
            {{ session('success') }}
        </x-ui.alert>
        @endif

        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">Identitas Sekolah</h2>
                <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Data ini akan ditampilkan pada dokumen Modul Ajar (PDF).</p>
            </x-slot>

            <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf

                <!-- Logo Upload -->
                <div class="space-y-4 pb-4 border-b border-[hsl(var(--border))]">
                    <h3 class="text-sm font-semibold text-[hsl(var(--muted-foreground))] uppercase tracking-wide">Logo Sekolah</h3>
                    
                    <div class="flex items-start gap-6">
                        <!-- Preview Logo -->
                        <div class="flex-shrink-0">
                            @if($settings->logo)
                            <div class="relative">
                                <img src="{{ Storage::url($settings->logo) }}" alt="Logo Sekolah" class="w-24 h-24 object-contain border rounded-lg bg-white p-2">
                                <form action="{{ route('settings.delete-logo') }}" method="POST" class="absolute -top-2 -right-2" onsubmit="return confirm('Hapus logo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-6 h-6 bg-red-500 text-white rounded-full text-xs hover:bg-red-600 transition">
                                        ×
                                    </button>
                                </form>
                            </div>
                            @else
                            <div class="w-24 h-24 border-2 border-dashed border-[hsl(var(--border))] rounded-lg flex items-center justify-center text-[hsl(var(--muted-foreground))]">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Upload Input -->
                        <div class="flex-1">
                            <x-ui.input
                                type="file"
                                name="logo"
                                label="Upload Logo"
                                accept="image/png,image/jpeg,image/jpg"
                                :error="$errors->first('logo')"
                            />
                            <p class="text-xs text-[hsl(var(--muted-foreground))] mt-1">Format: PNG, JPG. Maksimal 2MB.</p>
                        </div>
                    </div>
                </div>

                <!-- Identitas Sekolah -->
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-[hsl(var(--muted-foreground))] uppercase tracking-wide">Data Sekolah</h3>

                    <x-ui.input
                        name="nama_sekolah"
                        label="Nama Sekolah"
                        placeholder="Contoh: SMP Negeri 1 Jakarta"
                        :value="old('nama_sekolah', $settings->nama_sekolah)"
                        :error="$errors->first('nama_sekolah')"
                    />

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.input
                            name="nsm"
                            label="NSM (Nomor Statistik Madrasah)"
                            placeholder="Contoh: 121135010001"
                            :value="old('nsm', $settings->nsm)"
                            :error="$errors->first('nsm')"
                        />

                        <x-ui.input
                            name="npsn"
                            label="NPSN (Nomor Pokok Sekolah Nasional)"
                            placeholder="Contoh: 20100001"
                            :value="old('npsn', $settings->npsn)"
                            :error="$errors->first('npsn')"
                        />
                    </div>

                    <x-ui.textarea
                        name="alamat"
                        label="Alamat Sekolah"
                        placeholder="Contoh: Jl. Pendidikan No. 1, Kelurahan Menteng, Kecamatan Menteng, Jakarta Pusat 10310"
                        rows="3"
                        :error="$errors->first('alamat')"
                    >{{ old('alamat', $settings->alamat) }}</x-ui.textarea>
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-[hsl(var(--border))]">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </x-ui.card>

        <div class="mt-6">
            <x-ui.alert type="info">
                <strong>Info:</strong> Logo dan nama sekolah akan ditampilkan pada halaman sampul PDF Modul Ajar.
            </x-ui.alert>
        </div>
    </div>
</x-app-layout>
