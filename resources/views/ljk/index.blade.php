<x-app-layout>
    <x-slot name="header">Lembar Jawaban (LJK)</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[hsl(var(--foreground))]">Template LJK</h2>
                <p class="text-[hsl(var(--muted-foreground))]">Buat dan kelola template lembar jawaban untuk ujian.</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('ljk.create') }}" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Buat Template
                </a>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('ljk-answer-keys.index') }}" class="block">
                <x-ui.card class="p-4 hover:border-primary transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-[hsl(var(--foreground))]">Kunci Jawaban</p>
                            <p class="text-sm text-[hsl(var(--muted-foreground))]">Kelola kunci jawaban</p>
                        </div>
                    </div>
                </x-ui.card>
            </a>

            <a href="{{ route('ljk.correction.index') }}" class="block">
                <x-ui.card class="p-4 hover:border-primary transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-[hsl(var(--foreground))]">Koreksi LJK</p>
                            <p class="text-sm text-[hsl(var(--muted-foreground))]">Scan & koreksi jawaban</p>
                        </div>
                    </div>
                </x-ui.card>
            </a>

            <a href="{{ route('ljk.correction.results') }}" class="block">
                <x-ui.card class="p-4 hover:border-primary transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-[hsl(var(--foreground))]">Hasil Koreksi</p>
                            <p class="text-sm text-[hsl(var(--muted-foreground))]">Lihat semua hasil</p>
                        </div>
                    </div>
                </x-ui.card>
            </a>
        </div>

        <!-- Template List -->
        <x-ui.card class="p-0">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama Template</th>
                            <th>Jenis Ujian</th>
                            <th>Tahun Ajaran</th>
                            <th>Jumlah Soal</th>
                            <th>Pilihan</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($templates as $template)
                            <tr>
                                <td class="font-medium">{{ $template->nama_template }}</td>
                                <td>
                                    <x-ui.badge variant="secondary">{{ $template->jenis_ujian }}</x-ui.badge>
                                </td>
                                <td>{{ $template->tahun_ajaran ?? '-' }}</td>
                                <td>{{ $template->jumlah_soal }} soal</td>
                                <td>{{ implode(', ', $template->options) }}</td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('ljk.preview', $template) }}" class="btn btn-ghost btn-sm"
                                            title="Preview" target="_blank">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('ljk.print', $template) }}" class="btn btn-ghost btn-sm"
                                            title="Cetak PDF" target="_blank">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('ljk.edit', $template) }}" class="btn btn-ghost btn-sm"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('ljk.destroy', $template) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Hapus template ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-ghost btn-sm text-red-500"
                                                title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </form>
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
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                        <p class="text-[hsl(var(--muted-foreground))]">Belum ada template LJK.</p>
                                        <a href="{{ route('ljk.create') }}" class="btn btn-primary btn-sm mt-2">Buat
                                            Template Pertama</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        @if ($templates->hasPages())
            <div class="mt-4">
                {{ $templates->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
