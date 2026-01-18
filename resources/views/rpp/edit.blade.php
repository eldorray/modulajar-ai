<x-app-layout>
    <x-slot name="header">Edit Modul Ajar</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-[hsl(var(--foreground))]">{{ $rpp->topik }}</h2>
                <div class="flex items-center gap-2 mt-2">
                    <x-ui.badge variant="secondary">{{ $rpp->mata_pelajaran }}</x-ui.badge>
                    <x-ui.badge variant="outline">Fase {{ $rpp->fase }}</x-ui.badge>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('rpp.show', $rpp) }}" class="btn btn-outline btn-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Batal
                </a>
            </div>
        </div>

        @php $content = $rpp->content_result ?? []; @endphp

        <form action="{{ route('rpp.update', $rpp) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Kompetensi Awal -->
            <x-ui.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold">Kompetensi Awal</h3>
                </x-slot>
                <textarea name="content_result[kompetensi_awal]" rows="3" 
                    class="form-input w-full rounded-lg border-[hsl(var(--border))] bg-[hsl(var(--background))]">{{ $content['kompetensi_awal'] ?? '' }}</textarea>
            </x-ui.card>

            <!-- Tujuan Pembelajaran -->
            <x-ui.card>
                <x-slot name="header">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Tujuan Pembelajaran</h3>
                    </div>
                </x-slot>
                <div class="space-y-3" id="tujuan-container">
                    @forelse(($content['tujuan_pembelajaran'] ?? []) as $index => $tujuan)
                    <div class="flex gap-2 items-start tujuan-item">
                        <span class="text-sm text-[hsl(var(--muted-foreground))] mt-2">{{ $index + 1 }}.</span>
                        <textarea name="content_result[tujuan_pembelajaran][]" rows="2" 
                            class="form-input flex-1 rounded-lg border-[hsl(var(--border))] bg-[hsl(var(--background))]">{{ $tujuan }}</textarea>
                    </div>
                    @empty
                    <div class="flex gap-2 items-start tujuan-item">
                        <span class="text-sm text-[hsl(var(--muted-foreground))] mt-2">1.</span>
                        <textarea name="content_result[tujuan_pembelajaran][]" rows="2" 
                            class="form-input flex-1 rounded-lg border-[hsl(var(--border))] bg-[hsl(var(--background))]"></textarea>
                    </div>
                    @endforelse
                </div>
            </x-ui.card>

            <!-- Pemahaman Bermakna -->
            <x-ui.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold">Pemahaman Bermakna</h3>
                </x-slot>
                <textarea name="content_result[pemahaman_bermakna]" rows="3" 
                    class="form-input w-full rounded-lg border-[hsl(var(--border))] bg-[hsl(var(--background))]">{{ $content['pemahaman_bermakna'] ?? '' }}</textarea>
            </x-ui.card>

            <!-- Pertanyaan Pemantik -->
            <x-ui.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold">Pertanyaan Pemantik</h3>
                </x-slot>
                <div class="space-y-3">
                    @forelse(($content['pertanyaan_pemantik'] ?? []) as $index => $pertanyaan)
                    <div class="flex gap-2 items-start">
                        <span class="text-sm text-[hsl(var(--muted-foreground))] mt-2">{{ $index + 1 }}.</span>
                        <textarea name="content_result[pertanyaan_pemantik][]" rows="2" 
                            class="form-input flex-1 rounded-lg border-[hsl(var(--border))] bg-[hsl(var(--background))]">{{ $pertanyaan }}</textarea>
                    </div>
                    @empty
                    <div class="flex gap-2 items-start">
                        <span class="text-sm text-[hsl(var(--muted-foreground))] mt-2">1.</span>
                        <textarea name="content_result[pertanyaan_pemantik][]" rows="2" 
                            class="form-input flex-1 rounded-lg border-[hsl(var(--border))] bg-[hsl(var(--background))]"></textarea>
                    </div>
                    @endforelse
                </div>
            </x-ui.card>

            <!-- Profil Pelajar Pancasila -->
            @if(isset($content['profil_pelajar_pancasila']))
            <x-ui.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold">Profil Pelajar Pancasila</h3>
                </x-slot>
                <div class="space-y-4">
                    @foreach(($content['profil_pelajar_pancasila'] ?? []) as $index => $profil)
                    <div class="p-4 bg-[hsl(var(--muted))] rounded-lg space-y-2">
                        @if(is_array($profil))
                        <input type="text" name="content_result[profil_pelajar_pancasila][{{ $index }}][dimensi]" 
                            value="{{ $profil['dimensi'] ?? '' }}" placeholder="Dimensi"
                            class="form-input w-full rounded-lg border-[hsl(var(--border))] bg-[hsl(var(--background))] font-semibold">
                        <textarea name="content_result[profil_pelajar_pancasila][{{ $index }}][deskripsi]" rows="2" 
                            placeholder="Deskripsi"
                            class="form-input w-full rounded-lg border-[hsl(var(--border))] bg-[hsl(var(--background))]">{{ $profil['deskripsi'] ?? '' }}</textarea>
                        @else
                        <input type="text" name="content_result[profil_pelajar_pancasila][]" 
                            value="{{ $profil }}"
                            class="form-input w-full rounded-lg border-[hsl(var(--border))] bg-[hsl(var(--background))]">
                        @endif
                    </div>
                    @endforeach
                </div>
            </x-ui.card>
            @endif

            <!-- Kegiatan Pembelajaran -->
            <x-ui.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold">Kegiatan Pembelajaran</h3>
                </x-slot>
                <div class="space-y-6">
                    @php $kegiatan = $content['kegiatan_pembelajaran'] ?? []; @endphp
                    
                    <!-- Pendahuluan -->
                    <div class="p-4 bg-blue-50 rounded-lg space-y-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                <span class="text-blue-600 font-semibold text-sm">1</span>
                            </div>
                            <h4 class="font-semibold text-blue-800">Pendahuluan</h4>
                        </div>
                        <input type="text" name="content_result[kegiatan_pembelajaran][pendahuluan][durasi]" 
                            value="{{ $kegiatan['pendahuluan']['durasi'] ?? '' }}" placeholder="Durasi (contoh: 10 menit)"
                            class="form-input w-full rounded-lg border-blue-200 bg-white">
                        
                        @php $aktivitasPendahuluan = $kegiatan['pendahuluan']['aktivitas'] ?? []; @endphp
                        @foreach($aktivitasPendahuluan as $idx => $akt)
                        <div class="space-y-2 p-3 bg-white rounded-lg border border-blue-100">
                            @if(is_array($akt))
                            <textarea name="content_result[kegiatan_pembelajaran][pendahuluan][aktivitas][{{ $idx }}][kegiatan_guru]" 
                                rows="2" placeholder="Kegiatan Guru"
                                class="form-input w-full rounded-lg border-blue-200">{{ $akt['kegiatan_guru'] ?? '' }}</textarea>
                            <textarea name="content_result[kegiatan_pembelajaran][pendahuluan][aktivitas][{{ $idx }}][kegiatan_siswa]" 
                                rows="2" placeholder="Kegiatan Siswa"
                                class="form-input w-full rounded-lg border-blue-200">{{ $akt['kegiatan_siswa'] ?? '' }}</textarea>
                            @else
                            <textarea name="content_result[kegiatan_pembelajaran][pendahuluan][aktivitas][]" 
                                rows="2" class="form-input w-full rounded-lg border-blue-200">{{ $akt }}</textarea>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <!-- Inti -->
                    <div class="p-4 bg-green-50 rounded-lg space-y-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center">
                                <span class="text-green-600 font-semibold text-sm">2</span>
                            </div>
                            <h4 class="font-semibold text-green-800">Kegiatan Inti</h4>
                        </div>
                        <input type="text" name="content_result[kegiatan_pembelajaran][inti][durasi]" 
                            value="{{ $kegiatan['inti']['durasi'] ?? '' }}" placeholder="Durasi (contoh: 60 menit)"
                            class="form-input w-full rounded-lg border-green-200 bg-white">
                        
                        @php $aktivitasInti = $kegiatan['inti']['aktivitas'] ?? []; @endphp
                        @foreach($aktivitasInti as $idx => $akt)
                        <div class="space-y-2 p-3 bg-white rounded-lg border border-green-100">
                            @if(is_array($akt))
                            @if(isset($akt['fase_sintaks']))
                            <input type="text" name="content_result[kegiatan_pembelajaran][inti][aktivitas][{{ $idx }}][fase_sintaks]" 
                                value="{{ $akt['fase_sintaks'] ?? '' }}" placeholder="Fase/Sintaks"
                                class="form-input w-full rounded-lg border-green-200 font-medium">
                            @endif
                            <textarea name="content_result[kegiatan_pembelajaran][inti][aktivitas][{{ $idx }}][kegiatan_guru]" 
                                rows="2" placeholder="Kegiatan Guru"
                                class="form-input w-full rounded-lg border-green-200">{{ $akt['kegiatan_guru'] ?? '' }}</textarea>
                            <textarea name="content_result[kegiatan_pembelajaran][inti][aktivitas][{{ $idx }}][kegiatan_siswa]" 
                                rows="2" placeholder="Kegiatan Siswa"
                                class="form-input w-full rounded-lg border-green-200">{{ $akt['kegiatan_siswa'] ?? '' }}</textarea>
                            @else
                            <textarea name="content_result[kegiatan_pembelajaran][inti][aktivitas][]" 
                                rows="2" class="form-input w-full rounded-lg border-green-200">{{ $akt }}</textarea>
                            @endif
                        </div>
                        @endforeach
                    </div>

                    <!-- Penutup -->
                    <div class="p-4 bg-purple-50 rounded-lg space-y-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center">
                                <span class="text-purple-600 font-semibold text-sm">3</span>
                            </div>
                            <h4 class="font-semibold text-purple-800">Penutup</h4>
                        </div>
                        <input type="text" name="content_result[kegiatan_pembelajaran][penutup][durasi]" 
                            value="{{ $kegiatan['penutup']['durasi'] ?? '' }}" placeholder="Durasi (contoh: 10 menit)"
                            class="form-input w-full rounded-lg border-purple-200 bg-white">
                        
                        @php $aktivitasPenutup = $kegiatan['penutup']['aktivitas'] ?? []; @endphp
                        @foreach($aktivitasPenutup as $idx => $akt)
                        <div class="space-y-2 p-3 bg-white rounded-lg border border-purple-100">
                            @if(is_array($akt))
                            <textarea name="content_result[kegiatan_pembelajaran][penutup][aktivitas][{{ $idx }}][kegiatan_guru]" 
                                rows="2" placeholder="Kegiatan Guru"
                                class="form-input w-full rounded-lg border-purple-200">{{ $akt['kegiatan_guru'] ?? '' }}</textarea>
                            <textarea name="content_result[kegiatan_pembelajaran][penutup][aktivitas][{{ $idx }}][kegiatan_siswa]" 
                                rows="2" placeholder="Kegiatan Siswa"
                                class="form-input w-full rounded-lg border-purple-200">{{ $akt['kegiatan_siswa'] ?? '' }}</textarea>
                            @else
                            <textarea name="content_result[kegiatan_pembelajaran][penutup][aktivitas][]" 
                                rows="2" class="form-input w-full rounded-lg border-purple-200">{{ $akt }}</textarea>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>
            </x-ui.card>

            <!-- Asesmen -->
            @if(isset($content['asesmen']))
            <x-ui.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold">Asesmen</h3>
                </x-slot>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-[hsl(var(--muted-foreground))] mb-1">Jenis</label>
                        <input type="text" name="content_result[asesmen][jenis]" 
                            value="{{ $content['asesmen']['jenis'] ?? '' }}"
                            class="form-input w-full rounded-lg border-[hsl(var(--border))] bg-[hsl(var(--background))]">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-[hsl(var(--muted-foreground))] mb-1">Teknik</label>
                        @php 
                            $teknik = $content['asesmen']['teknik'] ?? '';
                            if (is_array($teknik)) $teknik = implode(', ', $teknik);
                        @endphp
                        <input type="text" name="content_result[asesmen][teknik]" 
                            value="{{ $teknik }}"
                            class="form-input w-full rounded-lg border-[hsl(var(--border))] bg-[hsl(var(--background))]">
                    </div>
                    @if(isset($content['asesmen']['bentuk']))
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-[hsl(var(--muted-foreground))] mb-1">Bentuk</label>
                        <input type="text" name="content_result[asesmen][bentuk]" 
                            value="{{ $content['asesmen']['bentuk'] ?? '' }}"
                            class="form-input w-full rounded-lg border-[hsl(var(--border))] bg-[hsl(var(--background))]">
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
                        <label class="block text-sm font-medium text-[hsl(var(--muted-foreground))] mb-2">Refleksi Siswa</label>
                        @foreach($content['refleksi']['refleksi_siswa'] as $item)
                        <textarea name="content_result[refleksi][refleksi_siswa][]" rows="2" 
                            class="form-input w-full rounded-lg border-[hsl(var(--border))] bg-[hsl(var(--background))] mb-2">{{ $item }}</textarea>
                        @endforeach
                    </div>
                    @endif
                    @if(isset($content['refleksi']['refleksi_guru']))
                    <div>
                        <label class="block text-sm font-medium text-[hsl(var(--muted-foreground))] mb-2">Refleksi Guru</label>
                        @foreach($content['refleksi']['refleksi_guru'] as $item)
                        <textarea name="content_result[refleksi][refleksi_guru][]" rows="2" 
                            class="form-input w-full rounded-lg border-[hsl(var(--border))] bg-[hsl(var(--background))] mb-2">{{ $item }}</textarea>
                        @endforeach
                    </div>
                    @endif
                </div>
            </x-ui.card>
            @endif

            <!-- Daftar Pustaka -->
            @if(isset($content['daftar_pustaka']))
            <x-ui.card>
                <x-slot name="header">
                    <h3 class="text-lg font-semibold">Daftar Pustaka</h3>
                </x-slot>
                <div class="space-y-2">
                    @foreach($content['daftar_pustaka'] as $pustaka)
                    <textarea name="content_result[daftar_pustaka][]" rows="2" 
                        class="form-input w-full rounded-lg border-[hsl(var(--border))] bg-[hsl(var(--background))]">{{ $pustaka }}</textarea>
                    @endforeach
                </div>
            </x-ui.card>
            @endif

            <!-- Submit Button -->
            <div class="flex justify-end gap-3">
                <a href="{{ route('rpp.show', $rpp) }}" class="btn btn-outline">Batal</a>
                <button type="submit" class="btn btn-primary">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
