<x-app-layout>
    <x-slot name="header">Detail Kunci Jawaban</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <x-ui.card>
            <x-ui.card-header>
                <div class="flex items-center justify-between">
                    <div>
                        <x-ui.card-title>{{ $ljkAnswerKey->nama }}</x-ui.card-title>
                        <x-ui.card-description>{{ $ljkAnswerKey->mata_pelajaran }} -
                            {{ $ljkAnswerKey->kelas ?? 'Semua Kelas' }}</x-ui.card-description>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('ljk.correction.scan', $ljkAnswerKey) }}" class="btn btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Koreksi dengan Kamera
                        </a>
                        <a href="{{ route('ljk.correction.manual', $ljkAnswerKey) }}" class="btn btn-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Input Manual
                        </a>
                    </div>
                </div>
            </x-ui.card-header>

            <x-ui.card-content>
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="text-center p-4 border rounded-lg">
                        <p class="text-2xl font-bold text-primary">{{ $ljkAnswerKey->jumlah_soal }}</p>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Jumlah Soal</p>
                    </div>
                    <div class="text-center p-4 border rounded-lg">
                        <p class="text-2xl font-bold text-primary">{{ implode(', ', $ljkAnswerKey->options) }}</p>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Pilihan</p>
                    </div>
                    <div class="text-center p-4 border rounded-lg">
                        <p class="text-2xl font-bold text-primary">{{ $ljkAnswerKey->results->count() }}</p>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Sudah Dikoreksi</p>
                    </div>
                </div>

                <!-- Answer Key Display -->
                <div>
                    <h4 class="label mb-3">Kunci Jawaban:</h4>
                    <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                        @foreach ($ljkAnswerKey->kunci_jawaban as $index => $answer)
                            <div class="bg-[hsl(var(--muted)/.3)] border rounded p-2 text-center">
                                <div class="font-bold text-sm">{{ $index + 1 }}</div>
                                <div class="text-lg font-bold text-primary">{{ $answer }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </x-ui.card-content>

            <x-ui.card-footer class="flex items-center gap-2">
                <a href="{{ route('ljk-answer-keys.edit', $ljkAnswerKey) }}" class="btn btn-ghost">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Edit
                </a>
                <form action="{{ route('ljk-answer-keys.destroy', $ljkAnswerKey) }}" method="POST" class="inline"
                    onsubmit="return confirm('Hapus kunci jawaban ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-ghost text-red-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Hapus
                    </button>
                </form>
            </x-ui.card-footer>
        </x-ui.card>

        <!-- Recent Results -->
        @if ($ljkAnswerKey->results->count() > 0)
            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>Hasil Koreksi Terakhir</x-ui.card-title>
                </x-ui.card-header>
                <x-ui.card-content>
                    <div class="space-y-2">
                        @foreach ($ljkAnswerKey->results->take(5) as $result)
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div>
                                    <p class="font-medium">{{ $result->nama_peserta ?? 'Peserta ' . $result->id }}</p>
                                    <p class="text-sm text-[hsl(var(--muted-foreground))]">
                                        Benar: {{ $result->jumlah_benar }} | Salah: {{ $result->jumlah_salah }} |
                                        Kosong: {{ $result->jumlah_kosong }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="text-right">
                                        <p
                                            class="text-2xl font-bold {{ $result->skor >= 75 ? 'text-green-500' : ($result->skor >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                                            {{ $result->skor }}
                                        </p>
                                        <p class="text-xs text-[hsl(var(--muted-foreground))]">Skor</p>
                                    </div>
                                    <a href="{{ route('ljk.correction.result', $result) }}"
                                        class="btn btn-ghost btn-sm">Detail</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card-content>
            </x-ui.card>
        @endif

        <a href="{{ route('ljk-answer-keys.index') }}" class="btn btn-ghost">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Kembali ke Daftar
        </a>
    </div>
</x-app-layout>
