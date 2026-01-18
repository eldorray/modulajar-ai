<x-app-layout>
    <x-slot name="header">Detail Modul Ajar</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[hsl(var(--foreground))]">{{ $rpp->topik }}</h2>
                <div class="flex items-center gap-2 mt-2">
                    <x-ui.badge variant="secondary">{{ $rpp->mata_pelajaran }}</x-ui.badge>
                    <x-ui.badge variant="outline">Fase {{ $rpp->fase }}</x-ui.badge>
                    @if($rpp->kelas)
                    <x-ui.badge variant="outline">Kelas {{ $rpp->kelas }}</x-ui.badge>
                    @endif
                    @if($rpp->status === 'completed')
                        <x-ui.badge variant="success">Selesai</x-ui.badge>
                    @elseif($rpp->status === 'failed')
                        <x-ui.badge variant="destructive">Gagal</x-ui.badge>
                    @endif
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('rpp.index') }}" class="btn btn-outline btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
                @if($rpp->status === 'completed')
                <a href="{{ route('rpp.pdf', $rpp) }}" class="btn btn-primary btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Download PDF
                </a>
                @endif
            </div>
        </div>

        @if($rpp->status === 'completed' && $rpp->content_result)
        @php $content = $rpp->content_result; @endphp

        <!-- Informasi Umum -->
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">Informasi Umum</h3>
            </x-slot>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Nama Penyusun</p>
                    <p class="font-medium">{{ $rpp->nama_guru }}</p>
                </div>
                @if($rpp->kepala_sekolah)
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Kepala Sekolah</p>
                    <p class="font-medium">{{ $rpp->kepala_sekolah }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Mata Pelajaran</p>
                    <p class="font-medium">{{ $rpp->mata_pelajaran }}</p>
                </div>
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Fase / Kelas</p>
                    <p class="font-medium">{{ $rpp->fase }}{{ $rpp->kelas ? ' / Kelas ' . $rpp->kelas : '' }}</p>
                </div>
                @if($rpp->semester)
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Semester</p>
                    <p class="font-medium">{{ $rpp->semester }}</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Alokasi Waktu</p>
                    <p class="font-medium">{{ $rpp->alokasi_waktu }}</p>
                </div>
                @if($rpp->jumlah_pertemuan)
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Jumlah Pertemuan</p>
                    <p class="font-medium">{{ $rpp->jumlah_pertemuan }} Pertemuan</p>
                </div>
                @endif
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Model Pembelajaran</p>
                    <p class="font-medium">{{ $rpp->model_pembelajaran }}</p>
                </div>
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Kurikulum</p>
                    <p class="font-medium">{{ $rpp->kurikulum ?? 'Kurikulum Merdeka' }}</p>
                </div>
                @if($rpp->target_peserta_didik)
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Target Peserta Didik</p>
                    <p class="font-medium">{{ $rpp->target_peserta_didik }}</p>
                </div>
                @endif
            </div>
        </x-ui.card>

        <!-- Kompetensi Awal -->
        @if(isset($content['kompetensi_awal']) && $content['kompetensi_awal'])
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">Kompetensi Awal</h3>
            </x-slot>
            <p class="text-[hsl(var(--foreground))]">{{ $content['kompetensi_awal'] }}</p>
        </x-ui.card>
        @endif

        <!-- Nilai-Nilai Cinta (Kurikulum Berbasis Cinta Kemenag) -->
        @if(isset($content['nilai_nilai_cinta']))
        <x-ui.card>
            <x-slot name="header">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">💕</span>
                    <h3 class="text-lg font-semibold">Nilai-Nilai Cinta</h3>
                </div>
                <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Kurikulum Berbasis Cinta - Kemenag</p>
            </x-slot>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach($content['nilai_nilai_cinta'] as $nilai)
                    @if(is_array($nilai))
                    <div class="p-4 bg-gradient-to-br from-pink-50 to-rose-50 rounded-lg border border-pink-100">
                        <p class="font-semibold text-rose-700">{{ $nilai['dimensi'] ?? '' }}</p>
                        <p class="text-sm text-rose-600 mt-2">{{ $nilai['deskripsi'] ?? '' }}</p>
                    </div>
                    @else
                    <x-ui.badge variant="secondary">{{ $nilai }}</x-ui.badge>
                    @endif
                @endforeach
            </div>
        </x-ui.card>
        @endif

        <!-- Profil Lulusan Madrasah (Kurikulum Berbasis Cinta Kemenag) -->
        @if(isset($content['profil_lulusan_madrasah']))
        <x-ui.card>
            <x-slot name="header">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">🎓</span>
                    <h3 class="text-lg font-semibold">Profil Lulusan Madrasah</h3>
                </div>
            </x-slot>
            <div class="space-y-3">
                @foreach($content['profil_lulusan_madrasah'] as $profil)
                    @if(is_array($profil))
                    <div class="p-3 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg border border-emerald-100">
                        <p class="font-semibold text-emerald-700">{{ $profil['dimensi'] ?? '' }}</p>
                        <p class="text-sm text-emerald-600 mt-1">{{ $profil['deskripsi'] ?? '' }}</p>
                    </div>
                    @else
                    <x-ui.badge variant="secondary">{{ $profil }}</x-ui.badge>
                    @endif
                @endforeach
            </div>
        </x-ui.card>
        @endif

        <!-- Moderasi Beragama (Kurikulum Berbasis Cinta Kemenag) -->
        @if(isset($content['moderasi_beragama']))
        <x-ui.card>
            <x-slot name="header">
                <div class="flex items-center gap-2">
                    <span class="text-2xl">☪️</span>
                    <h3 class="text-lg font-semibold">Moderasi Beragama (Wasathiyah)</h3>
                </div>
            </x-slot>
            <div class="space-y-4">
                @if(isset($content['moderasi_beragama']['nilai_wasathiyah']))
                <div class="p-4 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-lg border border-amber-100">
                    <p class="font-medium text-amber-800">Nilai Wasathiyah</p>
                    <p class="text-sm text-amber-700 mt-2">{{ $content['moderasi_beragama']['nilai_wasathiyah'] }}</p>
                </div>
                @endif
                @if(isset($content['moderasi_beragama']['implementasi']))
                <div>
                    <p class="text-sm font-medium text-[hsl(var(--muted-foreground))] mb-2">Implementasi</p>
                    <ul class="list-disc list-inside space-y-1 text-[hsl(var(--foreground))]">
                        @foreach($content['moderasi_beragama']['implementasi'] as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </x-ui.card>
        @endif

        <!-- Profil Pelajar Pancasila -->
        @if(isset($content['profil_pelajar_pancasila']))
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">Profil Pelajar Pancasila</h3>
            </x-slot>
            <div class="space-y-3">
                @foreach($content['profil_pelajar_pancasila'] as $profil)
                    @if(is_array($profil))
                    <div class="p-3 bg-[hsl(var(--muted))] rounded-lg">
                        <p class="font-semibold text-[hsl(var(--foreground))]">{{ $profil['dimensi'] ?? '' }}</p>
                        <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">{{ $profil['deskripsi'] ?? '' }}</p>
                    </div>
                    @else
                    <x-ui.badge variant="secondary">{{ $profil }}</x-ui.badge>
                    @endif
                @endforeach
            </div>
        </x-ui.card>
        @endif


        <!-- Sarana Prasarana -->
        @if(isset($content['sarana_prasarana']))
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">Sarana dan Prasarana</h3>
            </x-slot>
            @if(is_array($content['sarana_prasarana']) && isset($content['sarana_prasarana']['alat']))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if(isset($content['sarana_prasarana']['alat']))
                <div>
                    <p class="text-sm font-medium text-[hsl(var(--muted-foreground))] mb-2">Alat</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($content['sarana_prasarana']['alat'] as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(isset($content['sarana_prasarana']['bahan']))
                <div>
                    <p class="text-sm font-medium text-[hsl(var(--muted-foreground))] mb-2">Bahan</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($content['sarana_prasarana']['bahan'] as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(isset($content['sarana_prasarana']['media']))
                <div>
                    <p class="text-sm font-medium text-[hsl(var(--muted-foreground))] mb-2">Media</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($content['sarana_prasarana']['media'] as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(isset($content['sarana_prasarana']['sumber_belajar']))
                <div>
                    <p class="text-sm font-medium text-[hsl(var(--muted-foreground))] mb-2">Sumber Belajar</p>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($content['sarana_prasarana']['sumber_belajar'] as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
            @else
            <ul class="list-disc list-inside space-y-1 text-[hsl(var(--foreground))]">
                @foreach($content['sarana_prasarana'] as $sarana)
                <li>{{ is_array($sarana) ? json_encode($sarana) : $sarana }}</li>
                @endforeach
            </ul>
            @endif
        </x-ui.card>
        @endif

        <!-- Tujuan Pembelajaran -->
        @if(isset($content['tujuan_pembelajaran']))
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">Tujuan Pembelajaran</h3>
            </x-slot>
            <ul class="list-disc list-inside space-y-2 text-[hsl(var(--foreground))]">
                @foreach($content['tujuan_pembelajaran'] as $tujuan)
                <li>{{ $tujuan }}</li>
                @endforeach
            </ul>
        </x-ui.card>
        @endif

        <!-- Pemahaman Bermakna -->
        @if(isset($content['pemahaman_bermakna']) && $content['pemahaman_bermakna'])
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">Pemahaman Bermakna</h3>
            </x-slot>
            <p class="text-[hsl(var(--foreground))]">{{ $content['pemahaman_bermakna'] }}</p>
        </x-ui.card>
        @endif

        <!-- Pertanyaan Pemantik -->
        @if(isset($content['pertanyaan_pemantik']))
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">Pertanyaan Pemantik</h3>
            </x-slot>
            <ul class="list-decimal list-inside space-y-2 text-[hsl(var(--foreground))]">
                @foreach($content['pertanyaan_pemantik'] as $pertanyaan)
                <li>{{ $pertanyaan }}</li>
                @endforeach
            </ul>
        </x-ui.card>
        @endif

        <!-- Kegiatan Pembelajaran -->
        @if(isset($content['kegiatan_pembelajaran']))
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">Kegiatan Pembelajaran</h3>
            </x-slot>
            <div class="space-y-6">
                @if(isset($content['kegiatan_pembelajaran']['pendahuluan']))
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                            <span class="text-blue-600 font-semibold text-sm">1</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-[hsl(var(--foreground))]">Pendahuluan</h4>
                            <p class="text-sm text-[hsl(var(--muted-foreground))]">{{ $content['kegiatan_pembelajaran']['pendahuluan']['durasi'] ?? '' }}</p>
                        </div>
                    </div>
                    @php $aktivitasPendahuluan = $content['kegiatan_pembelajaran']['pendahuluan']['aktivitas'] ?? []; @endphp
                    @if(count($aktivitasPendahuluan) > 0 && is_array($aktivitasPendahuluan[0] ?? null))
                    <div class="ml-10 space-y-2">
                        @foreach($aktivitasPendahuluan as $akt)
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <p class="text-sm"><strong>Guru:</strong> {{ $akt['kegiatan_guru'] ?? '' }}</p>
                            <p class="text-sm"><strong>Siswa:</strong> {{ $akt['kegiatan_siswa'] ?? '' }}</p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <ul class="list-disc list-inside space-y-1 ml-10 text-[hsl(var(--foreground))]">
                        @foreach($aktivitasPendahuluan as $aktivitas)
                        <li>{{ is_array($aktivitas) ? ($aktivitas['kegiatan_guru'] ?? json_encode($aktivitas)) : $aktivitas }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                @endif

                @if(isset($content['kegiatan_pembelajaran']['inti']))
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                            <span class="text-green-600 font-semibold text-sm">2</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-[hsl(var(--foreground))]">Kegiatan Inti</h4>
                            <p class="text-sm text-[hsl(var(--muted-foreground))]">{{ $content['kegiatan_pembelajaran']['inti']['durasi'] ?? '' }}</p>
                        </div>
                    </div>
                    @php $aktivitasInti = $content['kegiatan_pembelajaran']['inti']['aktivitas'] ?? []; @endphp
                    @if(count($aktivitasInti) > 0 && is_array($aktivitasInti[0] ?? null))
                    <div class="ml-10 space-y-2">
                        @foreach($aktivitasInti as $akt)
                        <div class="p-3 bg-green-50 rounded-lg">
                            @if(isset($akt['fase_sintaks']))
                            <p class="text-xs font-semibold text-green-700 mb-1">{{ $akt['fase_sintaks'] }} {{ isset($akt['durasi']) ? '(' . $akt['durasi'] . ')' : '' }}</p>
                            @endif
                            <p class="text-sm"><strong>Guru:</strong> {{ $akt['kegiatan_guru'] ?? '' }}</p>
                            <p class="text-sm"><strong>Siswa:</strong> {{ $akt['kegiatan_siswa'] ?? '' }}</p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <ul class="list-disc list-inside space-y-1 ml-10 text-[hsl(var(--foreground))]">
                        @foreach($aktivitasInti as $aktivitas)
                        <li>{{ is_array($aktivitas) ? ($aktivitas['kegiatan_guru'] ?? json_encode($aktivitas)) : $aktivitas }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                @endif

                @if(isset($content['kegiatan_pembelajaran']['penutup']))
                <div>
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                            <span class="text-purple-600 font-semibold text-sm">3</span>
                        </div>
                        <div>
                            <h4 class="font-semibold text-[hsl(var(--foreground))]">Penutup</h4>
                            <p class="text-sm text-[hsl(var(--muted-foreground))]">{{ $content['kegiatan_pembelajaran']['penutup']['durasi'] ?? '' }}</p>
                        </div>
                    </div>
                    @php $aktivitasPenutup = $content['kegiatan_pembelajaran']['penutup']['aktivitas'] ?? []; @endphp
                    @if(count($aktivitasPenutup) > 0 && is_array($aktivitasPenutup[0] ?? null))
                    <div class="ml-10 space-y-2">
                        @foreach($aktivitasPenutup as $akt)
                        <div class="p-3 bg-purple-50 rounded-lg">
                            <p class="text-sm"><strong>Guru:</strong> {{ $akt['kegiatan_guru'] ?? '' }}</p>
                            <p class="text-sm"><strong>Siswa:</strong> {{ $akt['kegiatan_siswa'] ?? '' }}</p>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <ul class="list-disc list-inside space-y-1 ml-10 text-[hsl(var(--foreground))]">
                        @foreach($aktivitasPenutup as $aktivitas)
                        <li>{{ is_array($aktivitas) ? ($aktivitas['kegiatan_guru'] ?? json_encode($aktivitas)) : $aktivitas }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                @endif
            </div>
        </x-ui.card>
        @endif

        <!-- Asesmen -->
        @if(isset($content['asesmen']))
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">Asesmen</h3>
            </x-slot>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Jenis Asesmen</p>
                        <p class="font-medium">{{ $content['asesmen']['jenis'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Teknik Asesmen</p>
                        <p class="font-medium">
                            @if(is_array($content['asesmen']['teknik'] ?? null))
                                {{ implode(', ', $content['asesmen']['teknik']) }}
                            @else
                                {{ $content['asesmen']['teknik'] ?? '-' }}
                            @endif
                        </p>
                    </div>
                    @if(isset($content['asesmen']['bentuk']))
                    <div>
                        <p class="text-sm text-[hsl(var(--muted-foreground))]">Bentuk Asesmen</p>
                        <p class="font-medium">{{ $content['asesmen']['bentuk'] }}</p>
                    </div>
                    @endif
                </div>

                @if(isset($content['asesmen']['instrumen']))
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))] mb-2">Instrumen</p>
                    @foreach($content['asesmen']['instrumen'] as $instrumen)
                        @if(is_array($instrumen))
                        <div class="p-3 bg-[hsl(var(--muted))] rounded-lg mb-2">
                            <p class="font-medium">{{ $instrumen['jenis'] ?? '' }}</p>
                            <p class="text-sm">{{ $instrumen['deskripsi'] ?? '' }}</p>
                            @if(isset($instrumen['contoh_soal']))
                            <div class="mt-2">
                                <p class="text-xs font-medium">Contoh Soal:</p>
                                <ul class="list-disc list-inside text-sm">
                                    @foreach($instrumen['contoh_soal'] as $soal)
                                    <li>{{ $soal }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif
                        </div>
                        @else
                        <li>{{ $instrumen }}</li>
                        @endif
                    @endforeach
                </div>
                @endif

                @if(isset($content['asesmen']['rubrik_penilaian']) || isset($content['asesmen']['rubrik']))
                @php $rubrikData = $content['asesmen']['rubrik_penilaian'] ?? $content['asesmen']['rubrik'] ?? []; @endphp
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))] mb-2">Rubrik Penilaian</p>
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Kriteria</th>
                                    <th>4 (Sangat Baik)</th>
                                    <th>3 (Baik)</th>
                                    <th>2 (Cukup)</th>
                                    <th>1 (Perlu Perbaikan)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rubrikData as $rubrik)
                                <tr>
                                    <td class="font-medium">{{ $rubrik['kriteria'] ?? '-' }}</td>
                                    <td>{{ $rubrik['skor_4'] ?? '-' }}</td>
                                    <td>{{ $rubrik['skor_3'] ?? '-' }}</td>
                                    <td>{{ $rubrik['skor_2'] ?? '-' }}</td>
                                    <td>{{ $rubrik['skor_1'] ?? '-' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </x-ui.card>
        @endif

        <!-- Pengayaan & Remedial -->
        @if(isset($content['pengayaan_remedial']))
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">Pengayaan & Remedial</h3>
            </x-slot>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(isset($content['pengayaan_remedial']['pengayaan']))
                <div class="p-4 bg-green-50 rounded-lg">
                    <h4 class="font-semibold text-green-700 mb-2">Pengayaan</h4>
                    <p class="text-sm text-green-600 mb-2">{{ $content['pengayaan_remedial']['pengayaan']['sasaran'] ?? '' }}</p>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach($content['pengayaan_remedial']['pengayaan']['kegiatan'] ?? [] as $kegiatan)
                        <li>{{ $kegiatan }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(isset($content['pengayaan_remedial']['remedial']))
                <div class="p-4 bg-orange-50 rounded-lg">
                    <h4 class="font-semibold text-orange-700 mb-2">Remedial</h4>
                    <p class="text-sm text-orange-600 mb-2">{{ $content['pengayaan_remedial']['remedial']['sasaran'] ?? '' }}</p>
                    <ul class="list-disc list-inside text-sm space-y-1">
                        @foreach($content['pengayaan_remedial']['remedial']['kegiatan'] ?? [] as $kegiatan)
                        <li>{{ $kegiatan }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </x-ui.card>
        @endif

        <!-- Refleksi -->
        @if(isset($content['refleksi']))
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">Refleksi</h3>
            </x-slot>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(isset($content['refleksi']['refleksi_siswa']))
                <div>
                    <h4 class="font-medium mb-2 text-[hsl(var(--foreground))]">Refleksi Siswa</h4>
                    <ul class="list-disc list-inside space-y-1 text-[hsl(var(--foreground))]">
                        @foreach($content['refleksi']['refleksi_siswa'] as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                @if(isset($content['refleksi']['refleksi_guru']))
                <div>
                    <h4 class="font-medium mb-2 text-[hsl(var(--foreground))]">Refleksi Guru</h4>
                    <ul class="list-disc list-inside space-y-1 text-[hsl(var(--foreground))]">
                        @foreach($content['refleksi']['refleksi_guru'] as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
            </div>
        </x-ui.card>
        @elseif(isset($content['refleksi_guru']))
        <!-- Fallback for old format -->
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">Refleksi Guru</h3>
            </x-slot>
            <ul class="list-disc list-inside space-y-2 text-[hsl(var(--foreground))]">
                @foreach($content['refleksi_guru'] as $refleksi)
                <li>{{ $refleksi }}</li>
                @endforeach
            </ul>
        </x-ui.card>
        @endif

        <!-- LKPD -->
        @if(isset($content['lkpd']))
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">Lembar Kerja Peserta Didik (LKPD)</h3>
            </x-slot>
            <div class="space-y-4">
                @if(isset($content['lkpd']['judul']))
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Judul</p>
                    <p class="font-medium text-[hsl(var(--foreground))]">{{ $content['lkpd']['judul'] }}</p>
                </div>
                @endif

                @if(isset($content['lkpd']['tujuan']))
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Tujuan</p>
                    <p class="text-[hsl(var(--foreground))]">{{ $content['lkpd']['tujuan'] }}</p>
                </div>
                @endif

                @php $petunjuk = $content['lkpd']['petunjuk_umum'] ?? $content['lkpd']['petunjuk_pengerjaan'] ?? []; @endphp
                @if(count($petunjuk) > 0)
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))] mb-2">Petunjuk Pengerjaan</p>
                    <ul class="list-disc list-inside space-y-1 text-[hsl(var(--foreground))]">
                        @foreach($petunjuk as $p)
                        <li>{{ $p }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if(isset($content['lkpd']['kegiatan']))
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))] mb-2">Kegiatan</p>
                    <div class="space-y-3">
                        @foreach($content['lkpd']['kegiatan'] as $kegiatan)
                        <div class="p-3 bg-[hsl(var(--muted))] rounded-lg">
                            @if(isset($kegiatan['judul_kegiatan']))
                            <p class="font-medium text-[hsl(var(--foreground))]">
                                {{ $kegiatan['nomor'] ?? $loop->iteration }}. {{ $kegiatan['judul_kegiatan'] }}
                            </p>
                            @if(isset($kegiatan['petunjuk']))
                            <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">{{ $kegiatan['petunjuk'] }}</p>
                            @endif
                            @if(isset($kegiatan['soal_tugas']))
                            <div class="mt-2 space-y-2">
                                @foreach($kegiatan['soal_tugas'] as $soal)
                                <div class="p-2 bg-white rounded border">
                                    <p class="text-sm">{{ $soal['nomor'] ?? '' }}. {{ $soal['pertanyaan'] ?? '' }}</p>
                                    @if(isset($soal['tipe']))
                                    <span class="text-xs text-[hsl(var(--muted-foreground))]">({{ $soal['tipe'] }})</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                            @endif
                            @else
                            <p class="font-medium text-[hsl(var(--foreground))]">
                                {{ $kegiatan['nomor'] ?? $loop->iteration }}. {{ $kegiatan['pertanyaan'] ?? '' }}
                            </p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if(isset($content['lkpd']['kesimpulan']))
                <div>
                    <p class="text-sm text-[hsl(var(--muted-foreground))]">Kesimpulan</p>
                    <p class="text-[hsl(var(--foreground))]">{{ $content['lkpd']['kesimpulan'] }}</p>
                </div>
                @endif
            </div>
        </x-ui.card>
        @endif

        <!-- Glosarium -->
        @if(isset($content['glosarium']) && count($content['glosarium']) > 0)
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">Glosarium</h3>
            </x-slot>
            <div class="space-y-2">
                @foreach($content['glosarium'] as $item)
                <div class="flex gap-2">
                    <span class="font-semibold text-[hsl(var(--foreground))]">{{ $item['istilah'] ?? '' }}:</span>
                    <span class="text-[hsl(var(--muted-foreground))]">{{ $item['definisi'] ?? '' }}</span>
                </div>
                @endforeach
            </div>
        </x-ui.card>
        @endif

        <!-- Daftar Pustaka -->
        @if(isset($content['daftar_pustaka']) && count($content['daftar_pustaka']) > 0)
        <x-ui.card>
            <x-slot name="header">
                <h3 class="text-lg font-semibold">Daftar Pustaka</h3>
            </x-slot>
            <ul class="list-disc list-inside space-y-1 text-[hsl(var(--foreground))]">
                @foreach($content['daftar_pustaka'] as $pustaka)
                <li>{{ $pustaka }}</li>
                @endforeach
            </ul>
        </x-ui.card>
        @endif

        @elseif($rpp->status === 'failed')
        <x-ui.alert type="error">
            <strong>Gagal Generate Modul Ajar</strong><br>
            Terjadi kesalahan saat menghasilkan Modul Ajar. Silakan coba buat ulang.
        </x-ui.alert>
        @else
        <x-ui.alert type="warning">
            Modul Ajar sedang dalam proses pembuatan...
        </x-ui.alert>
        @endif
    </div>
</x-app-layout>
