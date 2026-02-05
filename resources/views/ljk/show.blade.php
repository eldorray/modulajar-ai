<x-app-layout>
    <x-slot name="header">Detail Template LJK</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Template Info -->
        <x-ui.card>
            <x-ui.card-header>
                <div class="flex items-center justify-between">
                    <div>
                        <x-ui.card-title>{{ $ljk->nama_template }}</x-ui.card-title>
                        <x-ui.card-description>Dibuat {{ $ljk->created_at->diffForHumans() }}</x-ui.card-description>
                    </div>
                    <x-ui.badge variant="secondary">{{ $ljk->jenis_ujian }}</x-ui.badge>
                </div>
            </x-ui.card-header>

            <x-ui.card-content>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 border rounded-lg">
                        <p class="text-2xl font-bold text-primary">{{ $ljk->jumlah_soal }}</p>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Jumlah Soal</p>
                    </div>
                    <div class="text-center p-4 border rounded-lg">
                        <p class="text-2xl font-bold text-primary">{{ implode(', ', $ljk->options) }}</p>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Pilihan Jawaban</p>
                    </div>
                    <div class="text-center p-4 border rounded-lg">
                        <p class="text-2xl font-bold text-primary">{{ $ljk->tahun_ajaran ?? '-' }}</p>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Tahun Ajaran</p>
                    </div>
                    <div class="text-center p-4 border rounded-lg">
                        <p class="text-2xl font-bold {{ $ljk->show_essay_lines ? 'text-green-500' : 'text-gray-400' }}">
                            {{ $ljk->show_essay_lines ? 'Ya' : 'Tidak' }}
                        </p>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Area Essay</p>
                    </div>
                </div>

                @if ($ljk->kop_image)
                    <div class="mt-6">
                        <p class="label mb-2">Preview Kop Surat:</p>
                        <div class="border rounded-lg p-2 bg-gray-50">
                            <img src="{{ $ljk->kop_image_url }}" alt="Kop Surat" class="max-h-24 mx-auto">
                        </div>
                    </div>
                @endif

                @if ($ljk->mata_pelajaran_list)
                    <div class="mt-6">
                        <p class="label mb-2">Mata Pelajaran:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($ljk->mata_pelajaran_list as $mapel)
                                <x-ui.badge variant="secondary">{{ $mapel }}</x-ui.badge>
                            @endforeach
                        </div>
                    </div>
                @endif
            </x-ui.card-content>

            <x-ui.card-footer class="flex items-center gap-2">
                <a href="{{ route('ljk.preview', $ljk) }}" class="btn btn-primary" target="_blank">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                        </path>
                    </svg>
                    Preview
                </a>
                <a href="{{ route('ljk.print', $ljk) }}" class="btn btn-secondary" target="_blank">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                        </path>
                    </svg>
                    Cetak PDF
                </a>
                <a href="{{ route('ljk.edit', $ljk) }}" class="btn btn-ghost">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('ljk-answer-keys.create', ['template_id' => $ljk->id]) }}" class="btn btn-ghost">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Buat Kunci Jawaban
                </a>
            </x-ui.card-footer>
        </x-ui.card>

        <!-- Answer Keys for this template -->
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Kunci Jawaban Terkait</x-ui.card-title>
            </x-ui.card-header>
            <x-ui.card-content>
                @if ($ljk->answerKeys->count() > 0)
                    <div class="space-y-2">
                        @foreach ($ljk->answerKeys as $key)
                            <div class="flex items-center justify-between p-3 border rounded-lg">
                                <div>
                                    <p class="font-medium">{{ $key->nama }}</p>
                                    <p class="text-sm text-[hsl(var(--muted-foreground))]">{{ $key->mata_pelajaran }} -
                                        {{ $key->kelas }}</p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <a href="{{ route('ljk.correction.scan', $key) }}"
                                        class="btn btn-primary btn-sm">Koreksi</a>
                                    <a href="{{ route('ljk-answer-keys.show', $key) }}"
                                        class="btn btn-ghost btn-sm">Lihat</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-[hsl(var(--muted-foreground))] py-4">Belum ada kunci jawaban untuk
                        template ini.</p>
                @endif
            </x-ui.card-content>
        </x-ui.card>

        <a href="{{ route('ljk.index') }}" class="btn btn-ghost">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18">
                </path>
            </svg>
            Kembali ke Daftar
        </a>
    </div>
</x-app-layout>
