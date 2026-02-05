<x-app-layout>
    <x-slot name="header">Edit Template LJK</x-slot>

    <div class="max-w-4xl mx-auto">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Edit Template: {{ $ljk->nama_template }}</x-ui.card-title>
            </x-ui.card-header>

            <x-ui.card-content>
                <form action="{{ route('ljk.update', $ljk) }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nama Template -->
                    <div>
                        <label for="nama_template" class="label">Nama Template <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_template" id="nama_template"
                            value="{{ old('nama_template', $ljk->nama_template) }}" class="input w-full" required>
                        @error('nama_template')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Kop Surat -->
                    <div>
                        <label for="kop_image" class="label">Gambar Kop Surat</label>
                        @if ($ljk->kop_image)
                            <div class="mb-2 p-2 border rounded bg-gray-50">
                                <p class="text-sm text-[hsl(var(--muted-foreground))] mb-1">Kop surat saat ini:</p>
                                <img src="{{ $ljk->kop_image_url }}" alt="Kop Surat" class="max-h-16">
                            </div>
                        @endif
                        <input type="file" name="kop_image" id="kop_image" accept="image/*"
                            class="input w-full file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white">
                        <p class="text-xs text-[hsl(var(--muted-foreground))] mt-1">Upload untuk mengganti kop surat</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Jenis Ujian -->
                        <div>
                            <label for="jenis_ujian" class="label">Jenis Ujian <span
                                    class="text-red-500">*</span></label>
                            <select name="jenis_ujian" id="jenis_ujian" class="input w-full" required>
                                @foreach (['STS', 'SAS', 'UTS', 'UAS', 'PAS', 'PAT', 'ULANGAN'] as $jenis)
                                    <option value="{{ $jenis }}"
                                        {{ old('jenis_ujian', $ljk->jenis_ujian) == $jenis ? 'selected' : '' }}>
                                        {{ $jenis }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tahun Ajaran -->
                        <div>
                            <label for="tahun_ajaran" class="label">Tahun Ajaran</label>
                            <input type="text" name="tahun_ajaran" id="tahun_ajaran"
                                value="{{ old('tahun_ajaran', $ljk->tahun_ajaran) }}" class="input w-full">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Jumlah Soal -->
                        <div>
                            <label for="jumlah_soal" class="label">Jumlah Soal <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="jumlah_soal" id="jumlah_soal"
                                value="{{ old('jumlah_soal', $ljk->jumlah_soal) }}" min="5" max="100"
                                class="input w-full" required>
                        </div>

                        <!-- Jumlah Pilihan -->
                        <div>
                            <label for="jumlah_pilihan" class="label">Jumlah Pilihan <span
                                    class="text-red-500">*</span></label>
                            <select name="jumlah_pilihan" id="jumlah_pilihan" class="input w-full" required>
                                @foreach ([3, 4, 5] as $n)
                                    <option value="{{ $n }}"
                                        {{ old('jumlah_pilihan', $ljk->jumlah_pilihan) == $n ? 'selected' : '' }}>
                                        {{ $n }} Pilihan</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Mata Pelajaran -->
                    <div>
                        <label class="label">Daftar Mata Pelajaran</label>
                        <div
                            class="grid grid-cols-2 md:grid-cols-3 gap-3 p-4 border rounded-lg bg-[hsl(var(--muted)/.3)]">
                            @php
                                $currentMapel = old('mata_pelajaran_list', $ljk->mata_pelajaran_list ?? []);
                            @endphp
                            @foreach ($mataPelajaranList as $mapel)
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="mata_pelajaran_list[]" value="{{ $mapel }}"
                                        class="checkbox" {{ in_array($mapel, $currentMapel) ? 'checked' : '' }}>
                                    <span class="text-sm">{{ $mapel }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Show Essay Lines -->
                    <div class="flex items-center gap-3">
                        <input type="checkbox" name="show_essay_lines" id="show_essay_lines" value="1"
                            class="checkbox" {{ old('show_essay_lines', $ljk->show_essay_lines) ? 'checked' : '' }}>
                        <label for="show_essay_lines" class="cursor-pointer">
                            <span class="font-medium">Tampilkan area jawaban essay</span>
                        </label>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center gap-3 pt-4 border-t">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('ljk.show', $ljk) }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </x-ui.card-content>
        </x-ui.card>
    </div>
</x-app-layout>
