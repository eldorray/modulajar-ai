<x-app-layout>
    <x-slot name="header">Buat Template LJK</x-slot>

    <div class="max-w-4xl mx-auto">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Buat Template Lembar Jawaban</x-ui.card-title>
                <x-ui.card-description>Buat template LJK baru untuk dicetak dan digunakan ujian.</x-ui.card-description>
            </x-ui.card-header>

            <x-ui.card-content>
                <form action="{{ route('ljk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Nama Template -->
                    <div>
                        <label for="nama_template" class="label">Nama Template <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_template" id="nama_template" value="{{ old('nama_template') }}"
                            class="input w-full" placeholder="Contoh: LJK STS Semester Genap 2025" required>
                        @error('nama_template')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kop Surat / Header Image -->
                    <div>
                        <label for="kop_image" class="label">Gambar Kop Surat</label>
                        <p class="text-sm text-[hsl(var(--muted-foreground))] mb-2">Upload gambar header/kop surat
                            sekolah. Jika tidak diupload, akan menggunakan kop dari pengaturan sekolah.</p>
                        <input type="file" name="kop_image" id="kop_image" accept="image/*"
                            class="input w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary/90">
                        <p class="text-xs text-[hsl(var(--muted-foreground))] mt-1">Format: JPG, PNG. Max: 2MB</p>
                        @error('kop_image')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Jenis Ujian -->
                        <div>
                            <label for="jenis_ujian" class="label">Jenis Ujian <span
                                    class="text-red-500">*</span></label>
                            <select name="jenis_ujian" id="jenis_ujian" class="input w-full" required>
                                <option value="STS" {{ old('jenis_ujian') == 'STS' ? 'selected' : '' }}>Sumatif
                                    Tengah Semester (STS)</option>
                                <option value="SAS" {{ old('jenis_ujian') == 'SAS' ? 'selected' : '' }}>Sumatif Akhir
                                    Semester (SAS)</option>
                                <option value="UTS" {{ old('jenis_ujian') == 'UTS' ? 'selected' : '' }}>Ujian Tengah
                                    Semester (UTS)</option>
                                <option value="UAS" {{ old('jenis_ujian') == 'UAS' ? 'selected' : '' }}>Ujian Akhir
                                    Semester (UAS)</option>
                                <option value="PAS" {{ old('jenis_ujian') == 'PAS' ? 'selected' : '' }}>Penilaian
                                    Akhir Semester (PAS)</option>
                                <option value="PAT" {{ old('jenis_ujian') == 'PAT' ? 'selected' : '' }}>Penilaian
                                    Akhir Tahun (PAT)</option>
                                <option value="ULANGAN" {{ old('jenis_ujian') == 'ULANGAN' ? 'selected' : '' }}>Ulangan
                                    Harian</option>
                            </select>
                            @error('jenis_ujian')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tahun Ajaran -->
                        <div>
                            <label for="tahun_ajaran" class="label">Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" id="tahun_ajaran"
                                value="{{ old('tahun_ajaran', date('Y') . '/' . (date('Y') + 1)) }}"
                                class="input w-full" placeholder="2024/2025">
                            @error('tahun_ajaran')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Jumlah Soal -->
                        <div>
                            <label for="jumlah_soal" class="label">Jumlah Soal <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="jumlah_soal" id="jumlah_soal"
                                value="{{ old('jumlah_soal', 40) }}" min="5" max="100" class="input w-full"
                                required>
                            <p class="text-xs text-[hsl(var(--muted-foreground))] mt-1">Minimal 5, maksimal 100 soal</p>
                            @error('jumlah_soal')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jumlah Pilihan -->
                        <div>
                            <label for="jumlah_pilihan" class="label">Jumlah Pilihan Jawaban <span
                                    class="text-red-500">*</span></label>
                            <select name="jumlah_pilihan" id="jumlah_pilihan" class="input w-full" required>
                                <option value="4" {{ old('jumlah_pilihan', 4) == 4 ? 'selected' : '' }}>4 Pilihan
                                    (A, B, C, D)</option>
                                <option value="5" {{ old('jumlah_pilihan') == 5 ? 'selected' : '' }}>5 Pilihan (A,
                                    B, C, D, E)</option>
                                <option value="3" {{ old('jumlah_pilihan') == 3 ? 'selected' : '' }}>3 Pilihan (A,
                                    B, C)</option>
                            </select>
                            @error('jumlah_pilihan')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Mata Pelajaran -->
                    <div>
                        <label class="label">Daftar Mata Pelajaran</label>
                        <p class="text-sm text-[hsl(var(--muted-foreground))] mb-3">Pilih mata pelajaran yang akan
                            ditampilkan sebagai checkbox di LJK.</p>
                        <div
                            class="grid grid-cols-2 md:grid-cols-3 gap-3 p-4 border rounded-lg bg-[hsl(var(--muted)/.3)]">
                            @foreach ($mataPelajaranList as $mapel)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="mata_pelajaran_list[]" value="{{ $mapel }}"
                                        class="checkbox"
                                        {{ in_array($mapel, old('mata_pelajaran_list', $mataPelajaranList)) ? 'checked' : '' }}>
                                    <span class="text-sm">{{ $mapel }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Show Essay Lines -->
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="show_essay_lines" id="show_essay_lines" value="1"
                            class="checkbox" {{ old('show_essay_lines', true) ? 'checked' : '' }}>
                        <label for="show_essay_lines" class="cursor-pointer">
                            <span class="font-medium">Tampilkan area jawaban essay</span>
                            <p class="text-sm text-[hsl(var(--muted-foreground))]">Akan menampilkan garis kosong untuk
                                soal uraian di bawah pilihan ganda.</p>
                        </label>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center gap-3 pt-4 border-t">
                        <button type="submit" class="btn btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Template
                        </button>
                        <a href="{{ route('ljk.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </x-ui.card-content>
        </x-ui.card>
    </div>
</x-app-layout>
