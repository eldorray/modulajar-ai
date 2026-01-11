<x-app-layout>
    <x-slot name="header">Buat Modul Ajar</x-slot>

    <div class="max-w-3xl mx-auto">
        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">Form Generate Modul Ajar</h2>
                <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Isi data lengkap di bawah ini untuk menghasilkan Modul Ajar sesuai format Kemdikbud.</p>
            </x-slot>

            <form id="rpp-form" action="{{ route('rpp.store') }}" method="POST" class="space-y-6" x-data="{ loading: false }" @submit="loading = true">
                @csrf

                <!-- Identitas Guru & Sekolah -->
                <div class="space-y-4 pb-4 border-b border-[hsl(var(--border))]">
                    <h3 class="text-sm font-semibold text-[hsl(var(--muted-foreground))] uppercase tracking-wide">Identitas Penyusun</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.input
                            name="nama_guru"
                            label="Nama Penyusun/Guru"
                            placeholder="Masukkan nama guru"
                            :value="old('nama_guru', auth()->user()->name)"
                            :error="$errors->first('nama_guru')"
                            required
                        />

                        <x-ui.input
                            name="kepala_sekolah"
                            label="Nama Kepala Sekolah"
                            placeholder="Masukkan nama kepala sekolah"
                            :value="old('kepala_sekolah')"
                            :error="$errors->first('kepala_sekolah')"
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.input
                            name="nip_kepala_sekolah"
                            label="NIP Kepala Sekolah"
                            placeholder="Contoh: 19750101 200003 1 001"
                            :value="old('nip_kepala_sekolah')"
                            :error="$errors->first('nip_kepala_sekolah')"
                        />

                        <x-ui.input
                            name="kota"
                            label="Kota/Kabupaten"
                            placeholder="Contoh: Jakarta, Bandung, Surabaya"
                            :value="old('kota')"
                            :error="$errors->first('kota')"
                        />
                    </div>

                    <x-ui.input
                        type="date"
                        name="tanggal"
                        label="Tanggal Penyusunan"
                        :value="old('tanggal', date('Y-m-d'))"
                        :error="$errors->first('tanggal')"
                    />
                </div>

                <!-- Informasi Umum Modul -->
                <div class="space-y-4 pb-4 border-b border-[hsl(var(--border))]">
                    <h3 class="text-sm font-semibold text-[hsl(var(--muted-foreground))] uppercase tracking-wide">Informasi Umum</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.input
                            name="mata_pelajaran"
                            label="Mata Pelajaran"
                            placeholder="Contoh: Matematika, Bahasa Indonesia, IPA"
                            :value="old('mata_pelajaran')"
                            :error="$errors->first('mata_pelajaran')"
                            required
                        />

                        <x-ui.select
                            name="fase"
                            label="Fase"
                            :options="[
                                'A' => 'Fase A (Kelas 1-2 SD)',
                                'B' => 'Fase B (Kelas 3-4 SD)',
                                'C' => 'Fase C (Kelas 5-6 SD)',
                                'D' => 'Fase D (Kelas 7-9 SMP)',
                                'E' => 'Fase E (Kelas 10 SMA)',
                                'F' => 'Fase F (Kelas 11-12 SMA)',
                            ]"
                            placeholder="Pilih Fase"
                            :value="old('fase')"
                            :error="$errors->first('fase')"
                            required
                        />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <x-ui.input
                            name="kelas"
                            label="Kelas"
                            placeholder="Contoh: 7, 10, 12"
                            :value="old('kelas')"
                            :error="$errors->first('kelas')"
                        />

                        <x-ui.select
                            name="semester"
                            label="Semester"
                            :options="[
                                'Ganjil' => 'Semester Ganjil',
                                'Genap' => 'Semester Genap',
                            ]"
                            placeholder="Pilih Semester"
                            :value="old('semester')"
                            :error="$errors->first('semester')"
                        />

                        <x-ui.select
                            name="target_peserta_didik"
                            label="Target Peserta Didik"
                            :options="[
                                'Reguler' => 'Peserta Didik Reguler',
                                'Kesulitan Belajar' => 'Kesulitan Belajar',
                                'Pencapaian Tinggi' => 'Pencapaian Tinggi',
                            ]"
                            placeholder="Pilih Target"
                            :value="old('target_peserta_didik', 'Reguler')"
                            :error="$errors->first('target_peserta_didik')"
                        />
                    </div>
                </div>

                <!-- Komponen Inti -->
                <div class="space-y-4 pb-4 border-b border-[hsl(var(--border))]">
                    <h3 class="text-sm font-semibold text-[hsl(var(--muted-foreground))] uppercase tracking-wide">Komponen Inti</h3>

                    <x-ui.textarea
                        name="topik"
                        label="Topik/Materi Pembelajaran"
                        placeholder="Jelaskan topik atau materi yang akan diajarkan secara detail. Contoh: Operasi hitung bilangan bulat (penjumlahan, pengurangan, perkalian, pembagian) dan penerapannya dalam kehidupan sehari-hari"
                        rows="3"
                        :error="$errors->first('topik')"
                        required
                    >{{ old('topik') }}</x-ui.textarea>

                    <x-ui.textarea
                        name="kompetensi_awal"
                        label="Kompetensi Awal"
                        placeholder="Tuliskan pengetahuan/keterampilan prasyarat yang harus dimiliki peserta didik. Contoh: Siswa sudah memahami konsep bilangan cacah dan operasi dasarnya"
                        rows="2"
                        :error="$errors->first('kompetensi_awal')"
                    >{{ old('kompetensi_awal') }}</x-ui.textarea>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-ui.input
                            name="alokasi_waktu"
                            label="Alokasi Waktu"
                            placeholder="Contoh: 2 x 45 menit, 3 x 35 menit"
                            :value="old('alokasi_waktu')"
                            :error="$errors->first('alokasi_waktu')"
                            required
                        />

                        <x-ui.input
                            type="number"
                            name="jumlah_pertemuan"
                            label="Jumlah Pertemuan"
                            placeholder="Contoh: 1, 2, 3"
                            :value="old('jumlah_pertemuan', '1')"
                            :error="$errors->first('jumlah_pertemuan')"
                            min="1"
                            max="10"
                        />
                    </div>

                    <x-ui.input
                        name="kata_kunci"
                        label="Kata Kunci Materi"
                        placeholder="Contoh: bilangan bulat, operasi hitung, nilai positif, nilai negatif (pisahkan dengan koma)"
                        :value="old('kata_kunci')"
                        :error="$errors->first('kata_kunci')"
                    />

                    <x-ui.select
                        name="model_pembelajaran"
                        label="Model Pembelajaran"
                        :options="[
                            'Problem Based Learning' => 'Problem Based Learning (PBL)',
                            'Project Based Learning' => 'Project Based Learning (PjBL)',
                            'Discovery Learning' => 'Discovery Learning',
                            'Inquiry Learning' => 'Inquiry Learning',
                            'Cooperative Learning' => 'Cooperative Learning',
                            'Contextual Teaching and Learning' => 'Contextual Teaching and Learning (CTL)',
                            'Diferensiasi' => 'Pembelajaran Diferensiasi',
                        ]"
                        placeholder="Pilih Model Pembelajaran"
                        :value="old('model_pembelajaran')"
                        :error="$errors->first('model_pembelajaran')"
                    />
                </div>

                <!-- Kurikulum & Asesmen -->
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-[hsl(var(--muted-foreground))] uppercase tracking-wide">Kurikulum & Asesmen</h3>

                    <x-ui.select
                        name="kurikulum"
                        label="Jenis Kurikulum"
                        :options="[
                            'Kurikulum Merdeka' => 'Kurikulum Merdeka',
                            'Kurikulum Merdeka Belajar' => 'Kurikulum Merdeka Belajar',
                            'Kurikulum Merdeka Deep Learning' => 'Kurikulum Merdeka Deep Learning',
                        ]"
                        placeholder="Pilih Kurikulum"
                        :value="old('kurikulum', 'Kurikulum Merdeka')"
                        :error="$errors->first('kurikulum')"
                        required
                    />

                    <x-ui.select
                        name="jenis_asesmen"
                        label="Jenis Asesmen"
                        :options="[
                            'Formatif' => 'Asesmen Formatif',
                            'Sumatif' => 'Asesmen Sumatif',
                            'Formatif dan Sumatif' => 'Formatif dan Sumatif',
                            'Diagnostik' => 'Asesmen Diagnostik',
                        ]"
                        placeholder="Pilih Jenis Asesmen"
                        :value="old('jenis_asesmen', 'Formatif dan Sumatif')"
                        :error="$errors->first('jenis_asesmen')"
                    />
                </div>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-[hsl(var(--border))]">
                    <a href="{{ route('rpp.index') }}" class="btn btn-outline" x-show="!loading">Batal</a>
                    
                    <!-- Normal Submit Button -->
                    <button type="submit" class="btn btn-primary" x-show="!loading">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Generate Modul Ajar
                    </button>

                    <!-- Loading State -->
                    <div x-show="loading" class="flex items-center gap-3">
                        <div class="flex items-center gap-2 text-[hsl(var(--muted-foreground))]">
                            <svg class="w-5 h-5 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-sm font-medium">Memproses dengan AI...</span>
                        </div>
                    </div>
                </div>
            </form>
        </x-ui.card>

        <!-- Loading Overlay -->
        <div x-data="{ show: false }" 
             x-init="document.getElementById('rpp-form').addEventListener('submit', () => { show = true })"
             x-show="show" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
            <div class="bg-white rounded-3xl p-8 shadow-2xl max-w-md mx-4 text-center">
                <!-- Loading GIF dari folder refrensi -->
                <div class="mb-6">
                    <img src="{{ asset('refrensi/loading.gif') }}" alt="Loading Animation" class="w-48 h-48 mx-auto object-contain">
                </div>
                
                <h3 class="text-2xl font-bold text-gray-900 mb-2">🎓 AI Sedang Bekerja</h3>
                <p class="text-gray-500 mb-6">Mohon tunggu, AI sedang menyusun Modul Ajar untuk Anda...</p>
                
                <!-- Progress indicator -->
                <div class="space-y-3 text-left bg-gray-50 rounded-xl p-5">
                    <div class="flex items-center gap-3" x-data="{ done: false }" x-init="setTimeout(() => done = true, 2000)">
                        <div :class="done ? 'text-green-500' : 'text-gray-400'">
                            <svg x-show="done" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <div x-show="!done" class="w-5 h-5 border-2 border-gray-300 rounded-full animate-pulse"></div>
                        </div>
                        <span :class="done ? 'text-gray-800 font-medium' : 'text-gray-400'" class="text-sm">📋 Menganalisis informasi umum...</span>
                    </div>
                    
                    <div class="flex items-center gap-3" x-data="{ done: false }" x-init="setTimeout(() => done = true, 5000)">
                        <div :class="done ? 'text-green-500' : 'text-gray-400'">
                            <svg x-show="done" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <div x-show="!done" class="w-5 h-5 border-2 border-gray-300 rounded-full animate-pulse"></div>
                        </div>
                        <span :class="done ? 'text-gray-800 font-medium' : 'text-gray-400'" class="text-sm">📝 Menyusun kegiatan pembelajaran...</span>
                    </div>
                    
                    <div class="flex items-center gap-3" x-data="{ done: false }" x-init="setTimeout(() => done = true, 8000)">
                        <div :class="done ? 'text-green-500' : 'text-gray-400'">
                            <svg x-show="done" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            <div x-show="!done" class="w-5 h-5 border-2 border-gray-300 rounded-full animate-pulse"></div>
                        </div>
                        <span :class="done ? 'text-gray-800 font-medium' : 'text-gray-400'" class="text-sm">✅ Membuat asesmen & rubrik...</span>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="text-blue-500">
                            <svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </div>
                        <span class="text-sm text-blue-600 font-semibold">🚀 Menyelesaikan modul ajar...</span>
                    </div>
                </div>
                
                <p class="text-xs text-gray-400 mt-5">⏱️ Proses ini membutuhkan waktu 30-60 detik</p>
            </div>
        </div>

        <div class="mt-6">
            <x-ui.alert type="info">
                <strong>Tips:</strong> Semakin detail informasi yang Anda masukkan (terutama Topik dan Kompetensi Awal), semakin berkualitas Modul Ajar yang dihasilkan AI.
            </x-ui.alert>
        </div>
    </div>
</x-app-layout>
