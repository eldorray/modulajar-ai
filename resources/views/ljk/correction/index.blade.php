<x-app-layout>
    <x-slot name="header">Koreksi LJK</x-slot>

    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[hsl(var(--foreground))]">Koreksi Lembar Jawaban</h2>
                <p class="text-[hsl(var(--muted-foreground))]">Pilih kunci jawaban dan mulai koreksi dengan kamera atau
                    input manual.</p>
            </div>
        </div>

        <!-- Select Answer Key -->
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Pilih Kunci Jawaban</x-ui.card-title>
                <x-ui.card-description>Pilih kunci jawaban yang akan digunakan untuk koreksi.</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                @if ($answerKeys->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($answerKeys as $key)
                            <div class="border rounded-lg p-4 hover:border-primary transition-colors">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <h4 class="font-medium">{{ $key->nama }}</h4>
                                        <p class="text-sm text-[hsl(var(--muted-foreground))]">{{ $key->mata_pelajaran }}
                                        </p>
                                    </div>
                                    <x-ui.badge variant="secondary">{{ $key->jumlah_soal }} soal</x-ui.badge>
                                </div>
                                <p class="text-xs text-[hsl(var(--muted-foreground))] mb-3">
                                    {{ $key->kelas ?? 'Semua Kelas' }}</p>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('ljk.correction.scan', $key) }}"
                                        class="btn btn-primary btn-sm flex-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                            </path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                        Scan Kamera
                                    </a>
                                    <a href="{{ route('ljk.correction.manual', $key) }}"
                                        class="btn btn-secondary btn-sm flex-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                        Manual
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <svg class="w-12 h-12 mx-auto text-[hsl(var(--muted-foreground))]" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="mt-2 text-[hsl(var(--muted-foreground))]">Belum ada kunci jawaban.</p>
                        <a href="{{ route('ljk-answer-keys.create') }}" class="btn btn-primary mt-4">Buat Kunci
                            Jawaban</a>
                    </div>
                @endif
            </x-ui.card-content>
        </x-ui.card>

        <!-- Recent Results -->
        @if ($recentResults->count() > 0)
            <x-ui.card>
                <x-ui.card-header>
                    <div class="flex items-center justify-between">
                        <x-ui.card-title>Hasil Koreksi Terakhir</x-ui.card-title>
                        <a href="{{ route('ljk.correction.results') }}"
                            class="text-sm text-primary hover:underline">Lihat Semua</a>
                    </div>
                </x-ui.card-header>
                <x-ui.card-content>
                    <div class="space-y-3">
                        @foreach ($recentResults as $result)
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div>
                                    <p class="font-medium">{{ $result->nama_peserta ?? 'Peserta #' . $result->id }}</p>
                                    <p class="text-sm text-[hsl(var(--muted-foreground))]">
                                        {{ $result->answerKey?->mata_pelajaran }} - {{ $result->kelas ?? '-' }}
                                        <span class="mx-1">•</span>
                                        {{ $result->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="text-right">
                                        <p
                                            class="text-xl font-bold {{ $result->skor >= 75 ? 'text-green-500' : ($result->skor >= 50 ? 'text-yellow-500' : 'text-red-500') }}">
                                            {{ $result->skor }}
                                        </p>
                                        <p class="text-xs text-[hsl(var(--muted-foreground))]">
                                            B:{{ $result->jumlah_benar }} S:{{ $result->jumlah_salah }}
                                            K:{{ $result->jumlah_kosong }}
                                        </p>
                                    </div>
                                    <a href="{{ route('ljk.correction.result', $result) }}"
                                        class="btn btn-ghost btn-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-ui.card-content>
            </x-ui.card>
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
