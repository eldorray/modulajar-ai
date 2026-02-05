<x-app-layout>
    <x-slot name="header">Kunci Jawaban LJK</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[hsl(var(--foreground))]">Kunci Jawaban</h2>
                <p class="text-[hsl(var(--muted-foreground))]">Kelola kunci jawaban untuk koreksi LJK otomatis.</p>
            </div>
            <a href="{{ route('ljk-answer-keys.create') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Kunci Jawaban
            </a>
        </div>

        <!-- Answer Keys List -->
        <x-ui.card class="p-0">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Mata Pelajaran</th>
                            <th>Kelas</th>
                            <th>Soal</th>
                            <th>Template</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($answerKeys as $key)
                            <tr>
                                <td class="font-medium">{{ $key->nama }}</td>
                                <td>{{ $key->mata_pelajaran }}</td>
                                <td>{{ $key->kelas ?? '-' }}</td>
                                <td>{{ $key->jumlah_soal }} soal</td>
                                <td>
                                    @if ($key->template)
                                        <a href="{{ route('ljk.show', $key->template) }}"
                                            class="text-primary hover:underline">
                                            {{ $key->template->nama_template }}
                                        </a>
                                    @else
                                        <span class="text-[hsl(var(--muted-foreground))]">-</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('ljk.correction.scan', $key) }}"
                                            class="btn btn-primary btn-sm" title="Koreksi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                                </path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('ljk-answer-keys.show', $key) }}" class="btn btn-ghost btn-sm"
                                            title="Lihat">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>
                                        <a href="{{ route('ljk-answer-keys.edit', $key) }}" class="btn btn-ghost btn-sm"
                                            title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('ljk-answer-keys.destroy', $key) }}" method="POST"
                                            class="inline" onsubmit="return confirm('Hapus kunci jawaban ini?')">
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
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-[hsl(var(--muted-foreground))]">Belum ada kunci jawaban.</p>
                                        <a href="{{ route('ljk-answer-keys.create') }}"
                                            class="btn btn-primary btn-sm mt-2">Buat Kunci Jawaban</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        @if ($answerKeys->hasPages())
            <div class="mt-4">
                {{ $answerKeys->links() }}
            </div>
        @endif

        <a href="{{ route('ljk.index') }}" class="btn btn-ghost">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Kembali ke Template LJK
        </a>
    </div>
</x-app-layout>
