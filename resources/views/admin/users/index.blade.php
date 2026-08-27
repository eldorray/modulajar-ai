<x-app-layout>
    <x-slot name="header">Kelola User</x-slot>

    <div class="space-y-6">
        <!-- Header Actions -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[hsl(var(--foreground))]">Daftar User</h2>
                <p class="text-[hsl(var(--muted-foreground))]">Kelola semua user dalam aplikasi.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.users.template') }}" class="btn btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Template Excel
                </a>
                <button type="button" onclick="document.getElementById('importModal').showModal()" class="btn btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                    </svg>
                    Import Excel
                </button>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah User
                </a>
            </div>
        </div>

        <!-- Import Modal -->
        <dialog id="importModal" class="modal rounded-xl shadow-2xl p-0 backdrop:bg-black/50">
            <div class="bg-white rounded-xl p-6 w-full max-w-md">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Import User dari Excel</h3>
                    <button type="button" onclick="document.getElementById('importModal').close()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">File Excel</label>
                        <input 
                            type="file" 
                            name="file" 
                            accept=".xlsx,.xls,.csv" 
                            required
                            class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        >
                        <p class="text-xs text-gray-500 mt-1">Format: .xlsx, .xls, .csv (Maks. 5MB)</p>
                    </div>
                    
                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mb-4">
                        <p class="text-sm text-amber-800">
                            <strong>Tips:</strong> Download template terlebih dahulu untuk memastikan format yang benar.
                        </p>
                    </div>
                    
                    <div class="flex justify-end gap-2">
                        <button type="button" onclick="document.getElementById('importModal').close()" class="btn btn-secondary">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                            Import
                        </button>
                    </div>
                </form>
            </div>
        </dialog>

        <!-- User List -->
        <form action="{{ route('admin.users.reset-password-batch') }}" method="POST"
            x-data="{ dipilih: [], samakan: false, passwordBatch: '' }"
            @submit="if (! confirm('Reset password ' + dipilih.length + ' user terpilih?')) $event.preventDefault()">
            @csrf

            <!-- Toolbar batch -->
            <div x-show="dipilih.length" x-cloak x-transition
                class="mb-4 flex flex-col gap-3 rounded-xl border border-[hsl(var(--border))] bg-[hsl(var(--muted))] p-4 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm font-medium text-[hsl(var(--foreground))]">
                    <span x-text="dipilih.length"></span> user dipilih
                </p>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <label class="flex items-center gap-2 text-sm text-[hsl(var(--muted-foreground))]">
                        <input type="checkbox" x-model="samakan" class="h-4 w-4 rounded border-[hsl(var(--border))]">
                        Pakai password yang sama
                    </label>

                    <input type="password" name="password" x-model="passwordBatch" x-show="samakan" x-cloak
                        minlength="8" maxlength="64" placeholder="Password baru (min 8 karakter)"
                        class="input w-full sm:w-64" :required="samakan" :disabled="! samakan">

                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                        </svg>
                        Reset password terpilih
                    </button>
                </div>
            </div>

            <x-ui.card class="p-0">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-10">
                                <input type="checkbox" aria-label="Pilih semua user"
                                    class="h-4 w-4 rounded border-[hsl(var(--border))]"
                                    @change="dipilih = $event.target.checked ? {{ $users->pluck('id')->reject(fn ($id) => $id === auth()->id())->values()->toJson() }} : []">
                            </th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Total RPP</th>
                            <th>Password</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                @if($user->id !== auth()->id())
                                    <input type="checkbox" name="users[]" value="{{ $user->id }}" x-model.number="dipilih"
                                        aria-label="Pilih {{ $user->name }}"
                                        class="h-4 w-4 rounded border-[hsl(var(--border))]">
                                @endif
                            </td>
                            <td class="font-medium">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <x-ui.badge variant="default">Admin</x-ui.badge>
                                @else
                                    <x-ui.badge variant="secondary">Guru</x-ui.badge>
                                @endif
                            </td>
                            <td>{{ $user->rpps()->count() }}</td>
                            <td>
                                @if($user->temp_password)
                                    <div x-data="{ lihat: false }" class="flex items-center gap-1.5">
                                        <span class="font-mono text-sm" x-show="! lihat">••••••••</span>
                                        <span class="font-mono text-sm font-semibold" x-show="lihat" x-cloak>{{ $user->temp_password }}</span>

                                        <button type="button" @click="lihat = ! lihat"
                                            :aria-label="lihat ? 'Sembunyikan password' : 'Lihat password'"
                                            class="btn btn-ghost btn-sm px-1.5">
                                            <!-- Mata terbuka -->
                                            <svg x-show="! lihat" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <!-- Mata tertutup -->
                                            <svg x-show="lihat" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                            </svg>
                                        </button>

                                        <button type="button" @click="navigator.clipboard?.writeText('{{ $user->temp_password }}')"
                                            aria-label="Salin password" class="btn btn-ghost btn-sm px-1.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h8a2 2 0 002-2v-1m-6-6h8a2 2 0 002-2V4a2 2 0 00-2-2h-8a2 2 0 00-2 2v6a2 2 0 002 2z" />
                                            </svg>
                                        </button>
                                    </div>
                                    <p class="mt-0.5 text-xs text-[hsl(var(--muted-foreground))]">
                                        Reset {{ $user->temp_password_at?->diffForHumans() }}
                                    </p>
                                @else
                                    <span class="text-sm text-[hsl(var(--muted-foreground))]">Belum direset</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($user->id !== auth()->id())
                                    <button type="submit" form="reset-password-{{ $user->id }}" class="btn btn-ghost btn-sm"
                                        aria-label="Reset password {{ $user->name }}" title="Reset password">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                        </svg>
                                    </button>
                                    @endif
                                    @if($user->temp_password)
                                    <button type="submit" form="clear-temp-{{ $user->id }}" class="btn btn-ghost btn-sm"
                                        aria-label="Sembunyikan password {{ $user->name }}" title="Hapus password sementara dari daftar">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029M3 3l18 18" />
                                        </svg>
                                    </button>
                                    @endif
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    @if($user->id !== auth()->id())
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-ghost btn-sm text-[hsl(var(--destructive))]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            </x-ui.card>
        </form>

        <!-- Form aksi per user (di luar tabel agar tidak bersarang) -->
        @foreach($users as $user)
            @if($user->id !== auth()->id())
                <form id="reset-password-{{ $user->id }}" action="{{ route('admin.users.reset-password', $user) }}"
                    method="POST" class="hidden"
                    onsubmit="return confirm('Reset password {{ $user->name }}? Password lama tidak bisa dipakai lagi.')">
                    @csrf
                </form>
            @endif
            @if($user->temp_password)
                <form id="clear-temp-{{ $user->id }}" action="{{ route('admin.users.clear-temp-password', $user) }}"
                    method="POST" class="hidden"
                    onsubmit="return confirm('Hapus password {{ $user->name }} dari daftar? Password tetap berlaku untuk login.')">
                    @csrf
                    @method('DELETE')
                </form>
            @endif
        @endforeach

        <!-- Pagination -->
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
