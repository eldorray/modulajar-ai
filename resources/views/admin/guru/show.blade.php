<x-app-layout>
    <x-slot name="header">Detail Guru</x-slot>

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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <x-ui.card class="p-6">
                <div class="flex flex-col items-center text-center">
                    <div
                        class="w-24 h-24 rounded-full bg-[hsl(var(--secondary))] flex items-center justify-center mb-4">
                        @if ($guru->foto_url)
                            <img src="{{ $guru->foto_url }}" alt="{{ $guru->nama }}"
                                class="w-24 h-24 rounded-full object-cover">
                        @else
                            <span class="text-3xl font-bold text-[hsl(var(--secondary-foreground))]">
                                {{ substr($guru->nama, 0, 1) }}
                            </span>
                        @endif
                    </div>
                    <h2 class="text-xl font-bold text-[hsl(var(--foreground))]">{{ $guru->nama }}</h2>
                    <p class="text-[hsl(var(--muted-foreground))]">{{ $guru->jabatan ?? 'Guru' }}</p>

                    @if ($guru->user)
                        <div class="mt-4">
                            <x-ui.badge variant="default">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                                Akun Aktif
                            </x-ui.badge>
                        </div>
                    @else
                        <div class="mt-4">
                            <x-ui.badge variant="outline">Belum Ada Akun</x-ui.badge>
                        </div>
                    @endif
                </div>
            </x-ui.card>

            <!-- Detail Card -->
            <x-ui.card class="p-6 lg:col-span-2">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-[hsl(var(--foreground))]">Informasi Lengkap</h3>
                    <a href="{{ route('admin.guru.edit', $guru) }}" class="btn btn-primary btn-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                            </path>
                        </svg>
                        Edit
                    </a>
                </div>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <dt class="text-sm text-[hsl(var(--muted-foreground))]">NIK</dt>
                        <dd class="font-mono text-[hsl(var(--foreground))]">{{ $guru->nik }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-[hsl(var(--muted-foreground))]">NIP</dt>
                        <dd class="text-[hsl(var(--foreground))]">{{ $guru->nip ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-[hsl(var(--muted-foreground))]">Jenis Kelamin</dt>
                        <dd class="text-[hsl(var(--foreground))]">
                            @if ($guru->jenis_kelamin === 'L')
                                Laki-laki
                            @elseif($guru->jenis_kelamin === 'P')
                                Perempuan
                            @else
                                -
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-[hsl(var(--muted-foreground))]">Tempat, Tanggal Lahir</dt>
                        <dd class="text-[hsl(var(--foreground))]">
                            {{ $guru->tempat_lahir ?? '' }}{{ $guru->tempat_lahir && $guru->tanggal_lahir ? ', ' : '' }}{{ $guru->tanggal_lahir ? $guru->tanggal_lahir->format('d M Y') : '-' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm text-[hsl(var(--muted-foreground))]">No. HP</dt>
                        <dd class="text-[hsl(var(--foreground))]">{{ $guru->no_hp ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-[hsl(var(--muted-foreground))]">Email</dt>
                        <dd class="text-[hsl(var(--foreground))]">{{ $guru->email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-[hsl(var(--muted-foreground))]">Jabatan</dt>
                        <dd class="text-[hsl(var(--foreground))]">{{ $guru->jabatan ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-[hsl(var(--muted-foreground))]">Status</dt>
                        <dd class="text-[hsl(var(--foreground))]">{{ $guru->status ?? '-' }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm text-[hsl(var(--muted-foreground))]">Alamat</dt>
                        <dd class="text-[hsl(var(--foreground))]">{{ $guru->alamat ?? '-' }}</dd>
                    </div>
                </dl>
            </x-ui.card>
        </div>

        <!-- User Account Info -->
        @if ($guru->user)
            <x-ui.card class="p-6">
                <h3 class="text-lg font-semibold text-[hsl(var(--foreground))] mb-4">Informasi Akun User</h3>
                <dl class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <dt class="text-sm text-[hsl(var(--muted-foreground))]">Email Login</dt>
                        <dd class="text-[hsl(var(--foreground))]">{{ $guru->user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm text-[hsl(var(--muted-foreground))]">Role</dt>
                        <dd><x-ui.badge variant="secondary">{{ ucfirst($guru->user->role) }}</x-ui.badge></dd>
                    </div>
                    <div>
                        <dt class="text-sm text-[hsl(var(--muted-foreground))]">Terdaftar Sejak</dt>
                        <dd class="text-[hsl(var(--foreground))]">{{ $guru->user->created_at->format('d M Y') }}</dd>
                    </div>
                </dl>
            </x-ui.card>
        @endif
    </div>
</x-app-layout>
