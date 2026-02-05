<x-app-layout>
    <x-slot name="header">Hasil Koreksi</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Score Card -->
        <x-ui.card
            class="bg-gradient-to-r {{ $result->skor >= 75 ? 'from-green-500 to-emerald-600' : ($result->skor >= 50 ? 'from-yellow-500 to-orange-600' : 'from-red-500 to-rose-600') }} text-white">
            <x-ui.card-content class="py-8">
                <div class="text-center">
                    <p class="text-lg opacity-90 mb-2">Skor</p>
                    <p class="text-6xl font-bold mb-2">{{ $result->skor }}</p>
                    <p class="text-xl font-medium">Grade: {{ $result->grade }}</p>
                </div>

                <div class="flex justify-center gap-8 mt-6">
                    <div class="text-center">
                        <p class="text-3xl font-bold">{{ $result->jumlah_benar }}</p>
                        <p class="text-sm opacity-80">Benar</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold">{{ $result->jumlah_salah }}</p>
                        <p class="text-sm opacity-80">Salah</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold">{{ $result->jumlah_kosong }}</p>
                        <p class="text-sm opacity-80">Kosong</p>
                    </div>
                </div>
            </x-ui.card-content>
        </x-ui.card>

        <!-- Student Info -->
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Informasi Peserta</x-ui.card-title>
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Nama</p>
                        <p class="font-medium">{{ $result->nama_peserta ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">No. Peserta</p>
                        <p class="font-medium">{{ $result->nomor_peserta ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Kelas</p>
                        <p class="font-medium">{{ $result->kelas ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Mata Pelajaran</p>
                        <p class="font-medium">{{ $result->answerKey?->mata_pelajaran ?? '-' }}</p>
                    </div>
                </div>
            </x-ui.card-content>
        </x-ui.card>

        <!-- Answer Comparison -->
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Detail Jawaban</x-ui.card-title>
                <x-ui.card-description>Perbandingan jawaban siswa dengan kunci jawaban.</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="grid grid-cols-5 md:grid-cols-10 gap-2">
                    @foreach ($gradeDetails['details'] as $detail)
                        <div
                            class="border rounded p-2 text-center {{ $detail['status'] === 'correct'
                                ? 'bg-green-50 border-green-300'
                                : ($detail['status'] === 'wrong'
                                    ? 'bg-red-50 border-red-300'
                                    : 'bg-gray-50 border-gray-300') }}">
                            <div class="font-bold text-sm mb-1">{{ $detail['nomor'] }}</div>
                            <div class="flex flex-col items-center gap-1">
                                @if ($detail['status'] === 'correct')
                                    <span class="text-lg font-bold text-green-600">{{ $detail['jawaban'] }}</span>
                                    <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @elseif($detail['status'] === 'wrong')
                                    <span
                                        class="text-lg font-bold text-red-600 line-through">{{ $detail['jawaban'] }}</span>
                                    <span class="text-xs text-green-600">({{ $detail['kunci'] }})</span>
                                @else
                                    <span class="text-lg font-bold text-gray-400">-</span>
                                    <span class="text-xs text-green-600">({{ $detail['kunci'] }})</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Legend -->
                <div class="flex items-center justify-center gap-6 mt-6 pt-4 border-t">
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-green-50 border border-green-300 rounded"></div>
                        <span class="text-sm">Benar</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-red-50 border border-red-300 rounded"></div>
                        <span class="text-sm">Salah</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-4 h-4 bg-gray-50 border border-gray-300 rounded"></div>
                        <span class="text-sm">Kosong</span>
                    </div>
                </div>
            </x-ui.card-content>
        </x-ui.card>

        <!-- Scan Image (if available) -->
        @if ($result->scan_image)
            <x-ui.card>
                <x-ui.card-header>
                    <x-ui.card-title>Gambar Scan</x-ui.card-title>
                </x-ui.card-header>
                <x-ui.card-content>
                    <img src="{{ $result->scan_image_url }}" alt="Scan LJK"
                        class="max-w-full mx-auto rounded-lg border">
                </x-ui.card-content>
            </x-ui.card>
        @endif

        <!-- Actions -->
        <div class="flex items-center justify-between">
            <a href="{{ route('ljk.correction.index') }}" class="btn btn-ghost">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Koreksi Lagi
            </a>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="btn btn-secondary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Cetak
                </button>
                <form action="{{ route('ljk.correction.destroy-result', $result) }}" method="POST" class="inline"
                    onsubmit="return confirm('Hapus hasil koreksi ini?')">
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
            </div>
        </div>
    </div>
</x-app-layout>
