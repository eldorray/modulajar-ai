<x-app-layout>
    <x-slot name="header">Kelola Guru</x-slot>

    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[hsl(var(--foreground))]">Daftar Guru</h2>
                <p class="text-[hsl(var(--muted-foreground))]">Kelola data guru dan sync dari API Data Induk.</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="document.getElementById('syncModal').showModal()" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Sync dari API
                </button>
            </div>
        </div>

        <!-- Sync Modal -->
        <dialog id="syncModal" class="modal rounded-xl shadow-2xl p-0 backdrop:bg-black/50">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Sync Data Guru dari API</h3>
                    <button type="button" onclick="document.getElementById('syncModal').close()"
                        class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.guru.sync') }}" method="POST" id="syncForm">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Lembaga</label>
                        <select name="source" required class="input w-full">
                            <option value="">-- Pilih Lembaga --</option>
                            <option value="guru-mi">MI Daarul Hikmah</option>
                            <option value="guru-smp">SMP Garuda</option>
                        </select>
                        @error('source')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-blue-800">
                            <strong>Info:</strong> Data guru akan di-sync dari API Data Induk. Guru baru akan dibuatkan
                            akun dengan password = NIP.
                        </p>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('syncModal').close()"
                            class="btn btn-secondary">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary" id="syncBtn"
                            onclick="this.disabled=true; this.innerHTML='<svg class=\'w-4 h-4 animate-spin\' fill=\'none\' viewBox=\'0 0 24 24\'><circle class=\'opacity-25\' cx=\'12\' cy=\'12\' r=\'10\' stroke=\'currentColor\' stroke-width=\'4\'></circle><path class=\'opacity-75\' fill=\'currentColor\' d=\'M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z\'></path></svg> Syncing...'; document.getElementById('syncForm').submit();">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Mulai Sync
                        </button>
                    </div>
                </form>
            </div>
        </dialog>

        <!-- Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <x-ui.card class="p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Total Guru</p>
                        <p class="text-2xl font-bold text-[hsl(var(--foreground))]">{{ \App\Models\Guru::count() }}</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Memiliki Akun</p>
                        <p class="text-2xl font-bold text-[hsl(var(--foreground))]">
                            {{ \App\Models\Guru::whereNotNull('user_id')->count() }}</p>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card class="p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Belum Punya Akun</p>
                        <p class="text-2xl font-bold text-[hsl(var(--foreground))]">
                            {{ \App\Models\Guru::whereNull('user_id')->count() }}</p>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Guru List -->
        <x-ui.card class="p-0">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Jabatan</th>
                            <th>Status</th>
                            <th>Akun User</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gurus as $guru)
                            <tr>
                                <td class="font-mono text-sm">{{ $guru->nip ?? $guru->nik }}</td>
                                <td class="font-medium">{{ $guru->nama }}</td>
                                <td>{{ $guru->jabatan ?? '-' }}</td>
                                <td>
                                    @if ($guru->status)
                                        <x-ui.badge variant="secondary">{{ $guru->status }}</x-ui.badge>
                                    @else
                                        <span class="text-[hsl(var(--muted-foreground))]">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($guru->user)
                                        <x-ui.badge variant="default">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Aktif
                                        </x-ui.badge>
                                    @else
                                        <x-ui.badge variant="outline">Belum Ada</x-ui.badge>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.guru.show', $guru) }}" class="btn btn-ghost btn-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.guru.edit', $guru) }}" class="btn btn-ghost btn-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-[hsl(var(--muted-foreground))]" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                        <p class="text-[hsl(var(--muted-foreground))]">Belum ada data guru.</p>
                                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Klik tombol "Sync dari
                                            API" untuk mengambil data guru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        <!-- Pagination -->
        @if ($gurus->hasPages())
            <div class="mt-4">
                {{ $gurus->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
