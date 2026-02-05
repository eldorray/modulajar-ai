<x-app-layout>
    <x-slot name="header">Semua Hasil Koreksi</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[hsl(var(--foreground))]">Hasil Koreksi</h2>
                <p class="text-[hsl(var(--muted-foreground))]">Daftar semua hasil koreksi LJK.</p>
            </div>
            <a href="{{ route('ljk.correction.index') }}" class="btn btn-primary">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Koreksi Baru
            </a>
        </div>

        <!-- Filter -->
        <x-ui.card class="p-4">
            <form method="GET" class="flex items-center gap-4">
                <div class="flex-1">
                    <select name="answer_key_id" class="input w-full" onchange="this.form.submit()">
                        <option value="">-- Semua Kunci Jawaban --</option>
                        @foreach ($answerKeys as $key)
                            <option value="{{ $key->id }}"
                                {{ request('answer_key_id') == $key->id ? 'selected' : '' }}>
                                {{ $key->nama }} - {{ $key->mata_pelajaran }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if (request('answer_key_id'))
                    <a href="{{ route('ljk.correction.results') }}" class="btn btn-ghost btn-sm">Reset</a>
                @endif
            </form>
        </x-ui.card>

        <!-- Results List -->
        <x-ui.card class="p-0">
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Peserta</th>
                            <th>Kunci Jawaban</th>
                            <th>Kelas</th>
                            <th class="text-center">Benar</th>
                            <th class="text-center">Salah</th>
                            <th class="text-center">Kosong</th>
                            <th class="text-center">Skor</th>
                            <th>Tanggal</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($results as $result)
                            <tr>
                                <td>
                                    <p class="font-medium">{{ $result->nama_peserta ?? 'Peserta #' . $result->id }}</p>
                                    <p class="text-xs text-[hsl(var(--muted-foreground))]">
                                        {{ $result->nomor_peserta ?? '-' }}</p>
                                </td>
                                <td>
                                    @if ($result->answerKey)
                                        <a href="{{ route('ljk-answer-keys.show', $result->answerKey) }}"
                                            class="text-primary hover:underline">
                                            {{ $result->answerKey->nama }}
                                        </a>
                                        <p class="text-xs text-[hsl(var(--muted-foreground))]">
                                            {{ $result->answerKey->mata_pelajaran }}</p>
                                    @else
                                        <span class="text-[hsl(var(--muted-foreground))]">-</span>
                                    @endif
                                </td>
                                <td>{{ $result->kelas ?? '-' }}</td>
                                <td class="text-center text-green-600 font-medium">{{ $result->jumlah_benar }}</td>
                                <td class="text-center text-red-600 font-medium">{{ $result->jumlah_salah }}</td>
                                <td class="text-center text-gray-500 font-medium">{{ $result->jumlah_kosong }}</td>
                                <td class="text-center">
                                    <span
                                        class="text-lg font-bold {{ $result->skor >= 75 ? 'text-green-500' : ($result->skor >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                                        {{ $result->skor }}
                                    </span>
                                </td>
                                <td>
                                    <p class="text-sm">{{ $result->created_at->format('d/m/Y') }}</p>
                                    <p class="text-xs text-[hsl(var(--muted-foreground))]">
                                        {{ $result->created_at->format('H:i') }}</p>
                                </td>
                                <td class="text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('ljk.correction.result', $result) }}"
                                            class="btn btn-ghost btn-sm" title="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </a>
                                        <form action="{{ route('ljk.correction.destroy-result', $result) }}"
                                            method="POST" class="inline" onsubmit="return confirm('Hapus hasil ini?')">
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
                                <td colspan="9" class="text-center py-8">
                                    <div class="flex flex-col items-center gap-2">
                                        <svg class="w-12 h-12 text-[hsl(var(--muted-foreground))]" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                            </path>
                                        </svg>
                                        <p class="text-[hsl(var(--muted-foreground))]">Belum ada hasil koreksi.</p>
                                        <a href="{{ route('ljk.correction.index') }}"
                                            class="btn btn-primary btn-sm mt-2">Mulai Koreksi</a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-ui.card>

        @if ($results->hasPages())
            <div class="mt-4">
                {{ $results->links() }}
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
