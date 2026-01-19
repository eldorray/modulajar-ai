<x-app-layout>
    <x-slot name="header">Detail Soal STS</x-slot>

    <div class="max-w-5xl mx-auto">
        <!-- Header Actions -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('sts.index') }}" class="btn btn-outline btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
        @if($sts->status === 'completed' || !empty($sts->content_result))
                <a href="{{ route('sts.word', $sts) }}" class="btn btn-blue btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download Word
                </a>
                @endif
            </div>
        </div>

        @if(empty($sts->content_result))
            <x-ui.alert type="warning" class="mb-6">
                Soal STS ini belum selesai diproses atau mengalami kegagalan.
            </x-ui.alert>
        @else
            @php $content = $sts->content_result; @endphp

            <!-- Info Card -->
            <x-ui.card class="mb-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-xs text-[hsl(var(--muted-foreground))]">Mata Pelajaran</p>
                        <p class="font-medium text-[hsl(var(--foreground))]">{{ $sts->mata_pelajaran }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-[hsl(var(--muted-foreground))]">Kelas / Fase</p>
                        <p class="font-medium text-[hsl(var(--foreground))]">{{ $sts->kelas }} / {{ $sts->fase }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-[hsl(var(--muted-foreground))]">Topik</p>
                        <p class="font-medium text-[hsl(var(--foreground))]">{{ Str::limit($sts->topik, 50) }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-[hsl(var(--muted-foreground))]">Jumlah Soal</p>
                        <p class="font-medium text-[hsl(var(--foreground))]">{{ $sts->jumlah_soal }} soal</p>
                    </div>
                </div>
            </x-ui.card>

            <!-- Kisi-kisi -->
            @if(!empty($content['kisi_kisi']))
            <x-ui.card class="mb-6">
                <h3 class="text-lg font-semibold text-[hsl(var(--foreground))] mb-4">📋 Kisi-Kisi Soal</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[hsl(var(--border))]">
                                <th class="py-2 px-3 text-left font-medium text-[hsl(var(--muted-foreground))]">No</th>
                                <th class="py-2 px-3 text-left font-medium text-[hsl(var(--muted-foreground))]">Tujuan Pembelajaran</th>
                                <th class="py-2 px-3 text-left font-medium text-[hsl(var(--muted-foreground))]">Materi</th>
                                <th class="py-2 px-3 text-left font-medium text-[hsl(var(--muted-foreground))]">Level</th>
                                <th class="py-2 px-3 text-left font-medium text-[hsl(var(--muted-foreground))]">Bentuk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($content['kisi_kisi'] as $index => $item)
                            <tr class="border-b border-[hsl(var(--border))]">
                                <td class="py-2 px-3 text-[hsl(var(--foreground))]">{{ $item['nomor_soal'] ?? $index + 1 }}</td>
                                <td class="py-2 px-3 text-[hsl(var(--foreground))]">{{ $item['tujuan_pembelajaran'] ?? '-' }}</td>
                                <td class="py-2 px-3 text-[hsl(var(--foreground))]">{{ $item['materi'] ?? '-' }}</td>
                                <td class="py-2 px-3"><x-ui.badge variant="secondary">{{ $item['level_kognitif'] ?? '-' }}</x-ui.badge></td>
                                <td class="py-2 px-3 text-[hsl(var(--foreground))]">{{ $item['bentuk_soal'] ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
            @endif

            <!-- Soal Pilihan Ganda -->
            @if(!empty($content['soal_pilihan_ganda']))
            <x-ui.card class="mb-6">
                <h3 class="text-lg font-semibold text-[hsl(var(--foreground))] mb-4">A. Soal Pilihan Ganda</h3>
                <div class="space-y-6">
                    @foreach($content['soal_pilihan_ganda'] as $index => $soal)
                    <div class="p-4 bg-[hsl(var(--accent))] rounded-lg">
                        <p class="font-medium text-[hsl(var(--foreground))] mb-3">{{ $index + 1 }}. {{ $soal['pertanyaan'] ?? '' }}</p>
                        @if(!empty($soal['pilihan']))
                        <div class="space-y-2 pl-4">
                            @foreach($soal['pilihan'] as $key => $pilihan)
                            <p class="text-[hsl(var(--foreground))]">{{ $key }}. {{ $pilihan }}</p>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </x-ui.card>
            @endif

            <!-- Soal PG Kompleks -->
            @if(!empty($content['soal_pg_kompleks']))
            <x-ui.card class="mb-6">
                <h3 class="text-lg font-semibold text-[hsl(var(--foreground))] mb-4">B. Soal Pilihan Ganda Kompleks</h3>
                <p class="text-sm text-[hsl(var(--muted-foreground))] mb-4">Tentukan pernyataan berikut Benar atau Salah!</p>
                <div class="space-y-6">
                    @foreach($content['soal_pg_kompleks'] as $index => $soal)
                    <div class="p-4 bg-[hsl(var(--accent))] rounded-lg">
                        <p class="font-medium text-[hsl(var(--foreground))] mb-3">{{ $index + 1 }}. {{ $soal['pertanyaan'] ?? '' }}</p>
                        @if(!empty($soal['pernyataan']))
                        <div class="space-y-2 pl-4">
                            @foreach($soal['pernyataan'] as $p)
                            <p class="text-[hsl(var(--foreground))]">• {{ $p['teks'] ?? '' }}</p>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </x-ui.card>
            @endif

            <!-- Soal Menjodohkan -->
            @if(!empty($content['soal_menjodohkan']))
            <x-ui.card class="mb-6">
                <h3 class="text-lg font-semibold text-[hsl(var(--foreground))] mb-4">C. Soal Menjodohkan</h3>
                <p class="text-sm text-[hsl(var(--muted-foreground))] mb-4">Jodohkan pernyataan di kolom kiri dengan jawaban di kolom kanan!</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-4 bg-[hsl(var(--accent))] rounded-lg">
                        <p class="font-medium text-[hsl(var(--muted-foreground))] mb-3">Pernyataan:</p>
                        @foreach($content['soal_menjodohkan'] as $index => $soal)
                        <p class="text-[hsl(var(--foreground))] mb-2">{{ $index + 1 }}. {{ $soal['soal'] ?? '' }}</p>
                        @endforeach
                    </div>
                    <div class="p-4 bg-[hsl(var(--accent))] rounded-lg">
                        <p class="font-medium text-[hsl(var(--muted-foreground))] mb-3">Pilihan Jawaban:</p>
                        @foreach($content['soal_menjodohkan'] as $index => $soal)
                        <p class="text-[hsl(var(--foreground))] mb-2">{{ chr(65 + $index) }}. {{ $soal['jawaban'] ?? '' }}</p>
                        @endforeach
                    </div>
                </div>
            </x-ui.card>
            @endif

            <!-- Soal Uraian -->
            @if(!empty($content['soal_uraian']))
            <x-ui.card class="mb-6">
                <h3 class="text-lg font-semibold text-[hsl(var(--foreground))] mb-4">D. Soal Uraian</h3>
                <p class="text-sm text-[hsl(var(--muted-foreground))] mb-4">Jawablah pertanyaan berikut dengan jelas dan lengkap!</p>
                <div class="space-y-4">
                    @foreach($content['soal_uraian'] as $index => $soal)
                    <div class="p-4 bg-[hsl(var(--accent))] rounded-lg">
                        <p class="font-medium text-[hsl(var(--foreground))]">{{ $index + 1 }}. {{ $soal['pertanyaan'] ?? '' }}</p>
                    </div>
                    @endforeach
                </div>
            </x-ui.card>
            @endif

            <!-- Kunci Jawaban -->
            @if(!empty($content['kunci_jawaban']))
            <x-ui.card class="mb-6">
                <h3 class="text-lg font-semibold text-[hsl(var(--foreground))] mb-4">🔑 Kunci Jawaban</h3>
                
                @if(!empty($content['kunci_jawaban']['pilihan_ganda']))
                <div class="mb-4">
                    <p class="font-medium text-[hsl(var(--muted-foreground))] mb-2">A. Pilihan Ganda:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($content['kunci_jawaban']['pilihan_ganda'] as $i => $kunci)
                        <x-ui.badge variant="success">{{ $i + 1 }}. {{ $kunci }}</x-ui.badge>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!empty($content['kunci_jawaban']['pg_kompleks']))
                <div class="mb-4">
                    <p class="font-medium text-[hsl(var(--muted-foreground))] mb-2">B. PG Kompleks:</p>
                    <div class="space-y-1">
                        @foreach($content['kunci_jawaban']['pg_kompleks'] as $item)
                        <p class="text-sm text-[hsl(var(--foreground))]">{{ $item['nomor'] ?? '' }}. {{ $item['jawaban'] ?? '' }}</p>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!empty($content['kunci_jawaban']['menjodohkan']))
                <div class="mb-4">
                    <p class="font-medium text-[hsl(var(--muted-foreground))] mb-2">C. Menjodohkan:</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($content['kunci_jawaban']['menjodohkan'] as $kunci)
                        <x-ui.badge variant="secondary">{{ $kunci }}</x-ui.badge>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(!empty($content['kunci_jawaban']['uraian']))
                <div>
                    <p class="font-medium text-[hsl(var(--muted-foreground))] mb-2">D. Uraian:</p>
                    <div class="space-y-3">
                        @foreach($content['kunci_jawaban']['uraian'] as $item)
                        <div class="p-3 bg-[hsl(var(--accent))] rounded-lg">
                            <p class="text-sm text-[hsl(var(--foreground))]"><strong>{{ $item['nomor'] ?? '' }}.</strong> {{ $item['jawaban'] ?? '' }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </x-ui.card>
            @endif

            <!-- Rubrik Penilaian -->
            @if(!empty($content['rubrik_penilaian']))
            <x-ui.card class="mb-6">
                <h3 class="text-lg font-semibold text-[hsl(var(--foreground))] mb-4">📊 Rubrik Penilaian Uraian</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-[hsl(var(--border))]">
                                <th class="py-2 px-3 text-left font-medium text-[hsl(var(--muted-foreground))]">No Soal</th>
                                <th class="py-2 px-3 text-left font-medium text-[hsl(var(--muted-foreground))]">Kriteria</th>
                                <th class="py-2 px-3 text-center font-medium text-[hsl(var(--muted-foreground))]">Skor</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($content['rubrik_penilaian'] as $rubrik)
                                @if(!empty($rubrik['kriteria']))
                                    @foreach($rubrik['kriteria'] as $kIndex => $k)
                                    <tr class="border-b border-[hsl(var(--border))]">
                                        @if($kIndex === 0)
                                        <td class="py-2 px-3 text-[hsl(var(--foreground))]" rowspan="{{ count($rubrik['kriteria']) }}">{{ $rubrik['nomor_soal'] ?? '' }}</td>
                                        @endif
                                        <td class="py-2 px-3 text-[hsl(var(--foreground))]">{{ $k['deskripsi'] ?? '' }}</td>
                                        <td class="py-2 px-3 text-center"><x-ui.badge variant="secondary">{{ $k['skor'] ?? '' }}</x-ui.badge></td>
                                    </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-ui.card>
            @endif
        @endif
    </div>
</x-app-layout>
