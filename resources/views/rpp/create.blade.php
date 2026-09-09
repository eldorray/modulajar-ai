<x-app-layout>
    <x-slot name="header">Buat Modul Ajar</x-slot>

    <div class="max-w-3xl mx-auto">
        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">Form Generate Modul Ajar</h2>
                <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Isi data lengkap di bawah ini untuk menghasilkan Modul Ajar sesuai format Kemdikbud.</p>
            </x-slot>

            <form id="rpp-form" action="{{ route('rpp.store') }}" method="POST" class="space-y-6"
                x-data="rppCoverPreview(@js($previewInit))" x-on:input="syncPreview($event)"
                x-on:change="syncPreview($event)">
                @csrf

                <!-- Identitas Guru & Sekolah -->
                <div class="space-y-4 pb-4 border-b border-[hsl(var(--border))]">
                    <h3 class="text-sm font-semibold text-[hsl(var(--muted-foreground))] uppercase tracking-wide">Identitas Penyusun</h3>

                    <x-ui.select
                        name="jenjang"
                        label="Unit Sekolah"
                        :options="collect(\App\Models\SchoolSetting::JENJANG)->mapWithKeys(fn ($j) => [$j => $j])->all()"
                        :value="old('jenjang', 'MI')"
                        :error="$errors->first('jenjang')"
                        required
                    />
                    <p class="text-xs text-[hsl(var(--muted-foreground))] -mt-2">Menentukan logo, nama sekolah, dan kop surat yang dipakai pada cover PDF/Word. Atur per unit di menu Pengaturan Sekolah.</p>
                    
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
                            label="Fase/Jenjang"
                            :options="[
                                'A' => 'Fase A (Kelas 1-2 SD)',
                                'B' => 'Fase B (Kelas 3-4 SD)',
                                'C' => 'Fase C (Kelas 5-6 SD)',
                                'D' => 'Fase D (Kelas 7-9 SMP)',
                                'E' => 'Fase E (Kelas 10 SMA)',
                                'F' => 'Fase F (Kelas 11-12 SMA)',
                                'RA' => 'RA (Raudhatul Athfal)',
                                'MI Rendah' => 'MI Kelas 1-3 (Madrasah Ibtidaiyah)',
                                'MI Tinggi' => 'MI Kelas 4-6 (Madrasah Ibtidaiyah)',
                                'MTs' => 'MTs Kelas 7-9 (Madrasah Tsanawiyah)',
                                'MA' => 'MA Kelas 10-12 (Madrasah Aliyah)',
                            ]"
                            placeholder="Pilih Fase/Jenjang"
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
                            'Kurikulum Merdeka' => 'Kurikulum Merdeka (Kemdikbud)',
                            'Kurikulum Merdeka Belajar' => 'Kurikulum Merdeka Belajar (Kemdikbud)',
                            'Kurikulum Merdeka Deep Learning' => 'Kurikulum Merdeka Deep Learning (Kemdikbud)',
                            'Kurikulum Berbasis Cinta' => 'Kurikulum Berbasis Cinta (Kemenag - Madrasah)',
                        ]"
                        placeholder="Pilih Kurikulum"
                        :value="old('kurikulum', 'Kurikulum Merdeka')"
                        :error="$errors->first('kurikulum')"
                        required
                    />

                    <div>
                        <label class="block text-sm font-medium text-[hsl(var(--foreground))] mb-2">
                            Jenis Asesmen <span class="text-[hsl(var(--muted-foreground))] text-xs">(pilih satu atau lebih)</span>
                        </label>
                        @php
                            $asesmenOptions = [
                                'Diagnostik Kognitif' => 'Asesmen Diagnostik Kognitif',
                                'Diagnostik Non-Kognitif' => 'Asesmen Diagnostik Non-Kognitif (Gaya Belajar)',
                                'Formatif' => 'Asesmen Formatif',
                                'Sumatif' => 'Asesmen Sumatif',
                            ];
                            $selectedAsesmen = old('jenis_asesmen', ['Diagnostik Kognitif', 'Diagnostik Non-Kognitif', 'Formatif', 'Sumatif']);
                            if (!is_array($selectedAsesmen)) {
                                $selectedAsesmen = array_filter(array_map('trim', explode(',', $selectedAsesmen)));
                            }
                        @endphp
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-3 border border-[hsl(var(--border))] rounded-md bg-[hsl(var(--background))]">
                            @foreach($asesmenOptions as $value => $label)
                            <label class="flex items-start gap-2 cursor-pointer hover:bg-[hsl(var(--muted))] p-2 rounded transition-colors">
                                <input
                                    type="checkbox"
                                    name="jenis_asesmen[]"
                                    value="{{ $value }}"
                                    {{ in_array($value, $selectedAsesmen) ? 'checked' : '' }}
                                    class="mt-1 h-4 w-4 rounded border-[hsl(var(--border))] text-[hsl(var(--primary))] focus:ring-[hsl(var(--ring))]"
                                >
                                <span class="text-sm text-[hsl(var(--foreground))]">{{ $label }}</span>
                            </label>
                            @endforeach
                        </div>
                        @if($errors->first('jenis_asesmen'))
                        <p class="mt-1 text-xs text-[hsl(var(--destructive))]">{{ $errors->first('jenis_asesmen') }}</p>
                        @endif
                    </div>
                </div>

                <!-- Integrasi Nilai & Karakter -->
                <div class="space-y-4 pt-4 border-t border-[hsl(var(--border))]">
                    <h3 class="text-sm font-semibold text-[hsl(var(--muted-foreground))] uppercase tracking-wide">Integrasi Nilai & Karakter</h3>
                    <p class="text-xs text-[hsl(var(--muted-foreground))]">Centang untuk menambahkan bagian khusus integrasi nilai pada modul ajar (berlaku untuk semua kurikulum).</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 p-3 border border-[hsl(var(--border))] rounded-md bg-[hsl(var(--background))]">
                        <label class="flex items-start gap-2 cursor-pointer hover:bg-[hsl(var(--muted))] p-2 rounded transition-colors">
                            <input
                                type="checkbox"
                                name="panca_cinta"
                                value="1"
                                {{ old('panca_cinta') ? 'checked' : '' }}
                                class="mt-1 h-4 w-4 rounded border-[hsl(var(--border))] text-[hsl(var(--primary))] focus:ring-[hsl(var(--ring))]"
                            >
                            <span class="text-sm text-[hsl(var(--foreground))]">
                                <span class="font-medium">💗 Panca Cinta</span>
                                <span class="block text-xs text-[hsl(var(--muted-foreground))] mt-0.5">Cinta Allah &amp; Rasul, Ilmu, Lingkungan, Diri &amp; Sesama, Tanah Air</span>
                            </span>
                        </label>
                        <label class="flex items-start gap-2 cursor-pointer hover:bg-[hsl(var(--muted))] p-2 rounded transition-colors">
                            <input
                                type="checkbox"
                                name="adiwiyata"
                                value="1"
                                {{ old('adiwiyata') ? 'checked' : '' }}
                                class="mt-1 h-4 w-4 rounded border-[hsl(var(--border))] text-[hsl(var(--primary))] focus:ring-[hsl(var(--ring))]"
                            >
                            <span class="text-sm text-[hsl(var(--foreground))]">
                                <span class="font-medium">🌱 Adiwiyata</span>
                                <span class="block text-xs text-[hsl(var(--muted-foreground))] mt-0.5">Peduli lingkungan: sanitasi, sampah 3R, air, energi, penghijauan</span>
                            </span>
                        </label>
                        <label class="flex items-start gap-2 cursor-pointer hover:bg-[hsl(var(--muted))] p-2 rounded transition-colors">
                            <input
                                type="checkbox"
                                name="kka"
                                value="1"
                                {{ old('kka') ? 'checked' : '' }}
                                class="mt-1 h-4 w-4 rounded border-[hsl(var(--border))] text-[hsl(var(--primary))] focus:ring-[hsl(var(--ring))]"
                            >
                            <span class="text-sm text-[hsl(var(--foreground))]">
                                <span class="font-medium">🤖 KKA (Koding &amp; Kecerdasan Artifisial)</span>
                                <span class="block text-xs text-[hsl(var(--muted-foreground))] mt-0.5">Berpikir komputasional, literasi AI, algoritma, etika digital</span>
                            </span>
                        </label>
                    </div>
                </div>

                <!-- Desain & Tema Warna Dokumen + Pratinjau Sampul -->
                @php
                    $temaTerpilih = old('tema', 'merah');
                    $desainTerpilih = old('desain', \App\Support\RppDocumentStyle::DEFAULT_DESIGN);
                @endphp
                <div x-show="!loading" class="flex flex-col gap-6 pt-4 border-t border-[hsl(var(--border))] sm:flex-row sm:items-start">
                    <div class="flex-1 space-y-4">
                        <div class="space-y-2">
                            <span class="block text-sm font-medium text-[hsl(var(--muted-foreground))]">Desain dokumen:</span>
                            <div class="grid grid-cols-1 gap-2 sm:grid-cols-3">
                                @foreach(config('rpp_designs') as $key => $desain)
                                <label class="cursor-pointer">
                                    <input type="radio" name="desain" value="{{ $key }}" class="peer sr-only" {{ $desainTerpilih === $key ? 'checked' : '' }}>
                                    <span class="block rounded-lg border border-[hsl(var(--border))] px-3 py-2 text-left transition peer-checked:border-[hsl(var(--foreground))] peer-checked:ring-1 peer-checked:ring-[hsl(var(--foreground))]">
                                        <span class="block text-sm font-medium text-[hsl(var(--foreground))]">{{ $desain['label'] }}</span>
                                        <span class="block text-xs text-[hsl(var(--muted-foreground))] mt-0.5">{{ $desain['deskripsi'] }}</span>
                                    </span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <span class="block text-sm font-medium text-[hsl(var(--muted-foreground))]">Tema warna:</span>
                        <div class="flex items-center gap-2">
                            @foreach(config('rpp_themes') as $key => $tema)
                            <label class="cursor-pointer" title="{{ $tema['label'] }}">
                                <input type="radio" name="tema" value="{{ $key }}" class="peer sr-only" {{ $temaTerpilih === $key ? 'checked' : '' }}>
                                <span class="block w-7 h-7 rounded-full border-2 border-transparent ring-1 ring-[hsl(var(--border))] peer-checked:border-[hsl(var(--foreground))] peer-checked:ring-2 transition"
                                      style="background: linear-gradient(135deg, #{{ $tema['primary'] }} 60%, #{{ $tema['accent'] }} 60%);"></span>
                            </label>
                            @endforeach
                        </div>
                        <p class="text-xs text-[hsl(var(--muted-foreground))]">
                            Pratinjau sampul di samping ikut isian form dan berubah saat desain atau
                            tema diganti. Isi dokumen (kata pengantar, tabel, LKPD) baru dibuat setelah
                            Generate, dan desain masih bisa diganti saat mengunduh tanpa generate ulang.
                        </p>
                    </div>

                    {{-- Sampul dirakit pada ukuran A4 asli (794x1123px @96dpi) lalu di-scale,
                         supaya proporsi teks & ornamen sama persis dengan template cetaknya.
                         Identitas sekolah diambil dari Alpine (bukan Blade) karena harus
                         ikut berganti saat guru memilih unit MI/SMP.
                         Tiap varian mencerminkan satu partial di rpp/partials/cover-*.blade.php;
                         kalau partial itu berubah, blok di sini ikut diperbarui. --}}
                    <div class="shrink-0 mx-auto sm:mx-0">
                        <p class="mb-2 text-xs font-medium text-[hsl(var(--muted-foreground))]">Pratinjau sampul</p>
                        <div class="relative overflow-hidden rounded-lg border border-[hsl(var(--border))] shadow-sm bg-white"
                             style="width: 240px; height: 340px;">
                            {{-- A4 @96dpi = 794x1123px. Skala 0.3022 = 240/794, jadi seluruh
                                 halaman muat tepat di kotak 240x340 tanpa terpotong. --}}
                            <div class="absolute top-0 left-0 origin-top-left text-center"
                                 style="width: 794px; height: 1123px; transform: scale(0.3022); color: #1a1a1a;"
                                 x-bind:style="{ fontFamily: design.fontCss }">

                                <template x-if="design.ornamen">
                                    <div>
                                        <img class="absolute" style="top: 34px; left: 34px; width: 48px;" x-bind:src="decor('dots')" alt="">
                                        <img class="absolute" style="top: 0; right: 0; width: 180px;" x-bind:src="decor('tr')" alt="">
                                        <img class="absolute" style="bottom: 0; left: 0; width: 160px;" x-bind:src="decor('bl')" alt="">
                                        <img class="absolute" style="bottom: 0; right: 0; width: 115px;" x-bind:src="decor('br')" alt="">
                                    </div>
                                </template>

                                {{-- Kotak isi = area cetak sesungguhnya: A4 dikurangi margin
                                     2.5cm dari @page (94.5px @96dpi) di keempat sisi. Tanpa ini
                                     teks melebar ke seluruh lebar kertas dan tampak lebih besar
                                     daripada hasil cetaknya. Ornamen sengaja di luar kotak ini
                                     karena posisinya relatif ke tepi kertas, bukan ke area cetak. --}}
                                <div class="absolute" style="top: 94.5px; left: 94.5px; width: 605px;">

                                {{-- ---------- Klasik ---------- --}}
                                <template x-if="design.cover === 'klasik'">
                                    <div class="relative" style="z-index: 2; padding: 30px 20px 40px;">
                                        <template x-if="school.logo">
                                            <div style="margin-top: 40px; margin-bottom: 8px;">
                                                <img x-bind:src="school.logo" alt="" style="max-height: 85px; max-width: 85px; display: inline-block;">
                                            </div>
                                        </template>
                                        <template x-if="!school.logo">
                                            <div style="margin-top: 40px; height: 85px;"></div>
                                        </template>
                                        <div style="font-size: 16px; font-weight: bold; letter-spacing: 1px;" x-text="school.name"></div>
                                        <div style="font-size: 32px; font-weight: bold; line-height: 1.15; margin-top: 20px; letter-spacing: 1px; color: #4b5563;">
                                            RENCANA PELAKSANAAN<br>PEMBELAJARAN MENDALAM<br>
                                            <span style="font-size: 17px;" x-text="upper(f.kurikulum) || 'KURIKULUM MERDEKA'"></span>
                                        </div>
                                        <div style="font-size: 43px; font-weight: bold; letter-spacing: 1px; line-height: 1.15;"
                                             x-bind:style="{ color: '#' + theme.accent }"
                                             x-text="upper(f.mata_pelajaran) || 'MATA PELAJARAN'"></div>
                                        <div style="font-size: 17px; color: #6b7280; margin-bottom: 20px;">
                                            Semester <span x-text="f.semester || 'Ganjil'"></span> : Tahun Ajaran <span x-text="tahunAjaran"></span>
                                        </div>
                                        <div style="margin: 15px auto; width: 210px;"><img x-bind:src="garuda" alt="" style="width: 100%;"></div>
                                        <div style="font-size: 16px; color: #374151; margin-top: 20px; margin-bottom: 5px;">Disusun oleh:</div>
                                        <div style="font-size: 23px; font-weight: bold;"
                                             x-bind:style="{ color: '#' + theme.primary }"
                                             x-text="f.nama_guru || 'Nama Guru'"></div>
                                    </div>
                                </template>

                                {{-- ---------- Modern ---------- --}}
                                <template x-if="design.cover === 'modern'">
                                    <div class="relative" style="z-index: 2; padding: 30px 0 40px;">
                                        <template x-if="school.logo">
                                            <div style="margin-bottom: 14px;">
                                                <img x-bind:src="school.logo" alt="" style="max-height: 70px; max-width: 70px; display: inline-block;">
                                            </div>
                                        </template>
                                        <div style="padding: 16px 20px; margin-bottom: 28px;" x-bind:style="{ backgroundColor: '#' + theme.primary }">
                                            <div style="font-size: 19px; font-weight: bold; color: #ffffff; letter-spacing: 2px;" x-text="school.name"></div>
                                        </div>
                                        <div style="padding: 0 20px;">
                                            <div style="font-size: 32px; font-weight: bold; line-height: 1.15; letter-spacing: 1px; color: #4b5563;">
                                                RENCANA PELAKSANAAN<br>PEMBELAJARAN MENDALAM<br>
                                                <span style="font-size: 17px;" x-text="upper(f.kurikulum) || 'KURIKULUM MERDEKA'"></span>
                                            </div>
                                            <div style="font-size: 43px; font-weight: bold; letter-spacing: 1px; line-height: 1.15;"
                                                 x-bind:style="{ color: '#' + theme.accent }"
                                                 x-text="upper(f.mata_pelajaran) || 'MATA PELAJARAN'"></div>
                                            <div style="font-size: 17px; color: #6b7280; margin-bottom: 20px;">
                                                Semester <span x-text="f.semester || 'Ganjil'"></span> : Tahun Ajaran <span x-text="tahunAjaran"></span>
                                            </div>
                                            <div style="margin: 15px auto; width: 210px;"><img x-bind:src="garuda" alt="" style="width: 100%;"></div>
                                            <div style="font-size: 16px; color: #374151; margin-top: 20px; margin-bottom: 5px;">Disusun oleh:</div>
                                            <div style="font-size: 23px; font-weight: bold;"
                                                 x-bind:style="{ color: '#' + theme.primary }"
                                                 x-text="f.nama_guru || 'Nama Guru'"></div>
                                        </div>
                                        <div style="padding: 7px 0; margin-top: 34px;" x-bind:style="{ backgroundColor: '#' + theme.dark }"></div>
                                    </div>
                                </template>

                                {{-- ---------- Minimalis ---------- --}}
                                <template x-if="design.cover === 'minimalis'">
                                    <div class="relative" style="z-index: 2; padding: 30px 20px 40px;">
                                        <template x-if="school.logo">
                                            <div style="margin-top: 40px; margin-bottom: 8px;">
                                                <img x-bind:src="school.logo" alt="" style="max-height: 85px; max-width: 85px; display: inline-block;">
                                            </div>
                                        </template>
                                        <template x-if="!school.logo">
                                            <div style="margin-top: 40px; height: 85px;"></div>
                                        </template>
                                        <div style="font-size: 15px; letter-spacing: 3px; color: #374151;" x-text="school.name"></div>
                                        <div style="width: 45%; margin: 26px auto; border-top-width: 1px; border-top-style: solid;"
                                             x-bind:style="{ borderTopColor: '#' + theme.primary }"></div>
                                        <div style="font-size: 24px; line-height: 1.3; letter-spacing: 2px; color: #4b5563;">
                                            RENCANA PELAKSANAAN<br>PEMBELAJARAN MENDALAM<br>
                                            <span style="font-size: 15px; letter-spacing: 1px;" x-text="upper(f.kurikulum) || 'KURIKULUM MERDEKA'"></span>
                                        </div>
                                        <div style="font-size: 37px; font-weight: bold; letter-spacing: 1px; line-height: 1.2; margin-top: 6px;"
                                             x-bind:style="{ color: '#' + theme.primary }"
                                             x-text="upper(f.mata_pelajaran) || 'MATA PELAJARAN'"></div>
                                        <div style="font-size: 17px; color: #6b7280; margin-bottom: 20px;">
                                            Semester <span x-text="f.semester || 'Ganjil'"></span> : Tahun Ajaran <span x-text="tahunAjaran"></span>
                                        </div>
                                        <div style="width: 45%; margin: 22px auto; border-top: 1px solid #d1d5db;"></div>
                                        <div style="margin: 15px auto; width: 210px;"><img x-bind:src="garuda" alt="" style="width: 100%;"></div>
                                        <div style="font-size: 16px; color: #374151; margin-top: 20px; margin-bottom: 5px;">Disusun oleh:</div>
                                        <div style="font-size: 20px; font-weight: bold; color: #1f2937;" x-text="f.nama_guru || 'Nama Guru'"></div>
                                    </div>
                                </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-end gap-4 pt-4 border-t border-[hsl(var(--border))]">
                    <div class="flex items-center gap-4">
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
                </div>
            </form>
        </x-ui.card>

        <!-- Loading Overlay with Progress Bar -->
        <div x-data="progressLoader()" 
             x-show="show" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
            <div class="bg-white rounded-3xl p-8 shadow-2xl max-w-md mx-4 text-center w-full">
                
                <!-- Processing State -->
                <template x-if="!isComplete && !hasError">
                    <div>
                        <!-- Loading GIF -->
                        <div class="mb-6">
                            <img src="{{ asset('refrensi/loading.gif') }}" alt="Loading Animation" class="w-40 h-40 mx-auto object-contain">
                        </div>
                        
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">🎓 AI Sedang Bekerja</h3>
                        <p class="text-gray-500 mb-6" x-text="currentStep"></p>
                    </div>
                </template>
                
                <!-- Completed State -->
                <template x-if="isComplete">
                    <div>
                        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-green-100 flex items-center justify-center">
                            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-green-600 mb-2">✅ Selesai!</h3>
                        <p class="text-gray-500 mb-6">Modul Ajar berhasil dibuat. Mengalihkan...</p>
                    </div>
                </template>
                
                <!-- Error State -->
                <template x-if="hasError">
                    <div>
                        <div class="w-24 h-24 mx-auto mb-6 rounded-full bg-red-100 flex items-center justify-center">
                            <svg class="w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-red-600 mb-2">❌ Gagal</h3>
                        <p class="text-gray-500 mb-6" x-text="errorMessage"></p>
                        <button @click="retry()" class="btn btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            Coba Lagi
                        </button>
                    </div>
                </template>
                
                <!-- Progress Bar (always visible except on error) -->
                <div class="mb-4" x-show="!hasError">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Progress</span>
                        <span class="text-sm font-bold" :class="isComplete ? 'text-green-600' : 'text-blue-600'" x-text="Math.round(progress) + '%'"></span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 overflow-hidden">
                        <div class="h-4 rounded-full transition-all duration-300 ease-out"
                             :class="isComplete ? 'bg-green-500' : 'bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500'"
                             :style="'width: ' + progress + '%'">
                            <div class="h-full w-full bg-white/20" :class="!isComplete ? 'animate-pulse' : ''"></div>
                        </div>
                    </div>
                </div>

                <!-- Step indicators (visible during processing) -->
                <div class="space-y-2 text-left bg-gray-50 rounded-xl p-4 mt-4" x-show="!hasError">
                    <div class="flex items-center gap-3" :class="progress >= 15 ? 'text-green-600' : 'text-gray-400'">
                        <template x-if="progress >= 15">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                        <template x-if="progress < 15">
                            <div class="w-4 h-4 border-2 border-current rounded-full animate-pulse flex-shrink-0"></div>
                        </template>
                        <span class="text-sm" :class="progress >= 15 ? 'font-medium' : ''">📋 Menganalisis informasi umum</span>
                    </div>
                    
                    <div class="flex items-center gap-3" :class="progress >= 35 ? 'text-green-600' : 'text-gray-400'">
                        <template x-if="progress >= 35">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                        <template x-if="progress < 35">
                            <div class="w-4 h-4 border-2 border-current rounded-full animate-pulse flex-shrink-0"></div>
                        </template>
                        <span class="text-sm" :class="progress >= 35 ? 'font-medium' : ''">📝 Menyusun kegiatan pembelajaran</span>
                    </div>
                    
                    <div class="flex items-center gap-3" :class="progress >= 60 ? 'text-green-600' : 'text-gray-400'">
                        <template x-if="progress >= 60">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                        <template x-if="progress < 60">
                            <div class="w-4 h-4 border-2 border-current rounded-full animate-pulse flex-shrink-0"></div>
                        </template>
                        <span class="text-sm" :class="progress >= 60 ? 'font-medium' : ''">✅ Membuat asesmen & rubrik</span>
                    </div>
                    
                    <div class="flex items-center gap-3" :class="progress >= 100 ? 'text-green-600' : (progress >= 85 ? 'text-green-600' : 'text-blue-500')">
                        <template x-if="progress >= 100">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                        <template x-if="progress >= 85 && progress < 100">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                        <template x-if="progress < 85">
                            <svg class="w-4 h-4 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </template>
                        <span class="text-sm" :class="progress >= 85 ? 'font-medium text-green-600' : 'font-semibold'">🚀 Menyelesaikan modul ajar</span>
                    </div>
                </div>
                
                <p class="text-xs text-gray-400 mt-4" x-show="!isComplete && !hasError">⏱️ Estimasi waktu: 30-60 detik</p>
            </div>
        </div>

        <script>
            function rppCoverPreview(init) {
                return {
                    loading: false,
                    themes: init.themes,
                    designs: init.designs,
                    schools: init.schools,
                    decorBase: init.decorBase,
                    garuda: init.garuda,
                    tahunAjaran: init.tahunAjaran,
                    f: init.fields,

                    get school() {
                        return this.schools[this.f.jenjang] || Object.values(this.schools)[0];
                    },

                    get theme() {
                        return this.themes[this.f.tema] || Object.values(this.themes)[0];
                    },

                    get design() {
                        const d = this.designs[this.f.desain] || Object.values(this.designs)[0];
                        // fontCss dipisah namanya supaya tidak tertukar dengan properti
                        // 'font' milik CSS. Semua x-bind:style di pratinjau WAJIB bentuk
                        // objek: bentuk string menimpa seluruh atribut style, sehingga
                        // transform: scale() pada pembungkus ikut terhapus.
                        return { cover: d.cover, ornamen: d.ornamen, fontCss: d.font };
                    },

                    decor(part) {
                        return this.decorBase + 'decor-' + (this.themes[this.f.tema] ? this.f.tema : 'merah') + '-' + part + '.png';
                    },

                    upper(value) {
                        return (value || '').toUpperCase();
                    },

                    syncPreview(event) {
                        const name = event.target.name;
                        if (name in this.f) {
                            this.f[name] = event.target.value;
                        }
                    },
                };
            }

            function progressLoader() {
                return {
                    show: false,
                    progress: 0,
                    currentStep: 'Memulai proses...',
                    interval: null,
                    isComplete: false,
                    redirectUrl: null,
                    hasError: false,
                    errorMessage: '',
                    
                    init() {
                        const form = document.getElementById('rpp-form');
                        form.addEventListener('submit', (e) => {
                            e.preventDefault();
                            this.submitForm(form);
                        });
                    },
                    
                    async submitForm(form) {
                        this.show = true;
                        this.progress = 0;
                        this.isComplete = false;
                        this.hasError = false;
                        this.startProgress();
                        
                        try {
                            const formData = new FormData(form);
                            const response = await fetch(form.action, {
                                method: 'POST',
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                },
                            });
                            
                            const data = await response.json();
                            
                            if (data.success) {
                                this.completeProgress(data.redirect_url);
                            } else {
                                this.showError(data.error || 'Terjadi kesalahan. Silakan coba lagi.');
                            }
                        } catch (error) {
                            console.error('Submit error:', error);
                            this.showError('Terjadi kesalahan koneksi. Silakan coba lagi.');
                        }
                    },
                    
                    startProgress() {
                        const steps = [
                            { at: 5, text: 'Mengirim data ke AI...' },
                            { at: 15, text: 'Menganalisis informasi umum...' },
                            { at: 35, text: 'Menyusun kegiatan pembelajaran...' },
                            { at: 60, text: 'Membuat asesmen & rubrik...' },
                            { at: 85, text: 'Menyelesaikan modul ajar...' },
                            { at: 95, text: 'Hampir selesai...' },
                        ];
                        
                        this.interval = setInterval(() => {
                            if (this.progress < 95 && !this.isComplete) {
                                let increment = this.progress < 30 ? 2 : 
                                               this.progress < 60 ? 1.5 : 
                                               this.progress < 85 ? 1 : 0.5;
                                
                                this.progress = Math.min(95, this.progress + increment);
                                
                                for (let i = steps.length - 1; i >= 0; i--) {
                                    if (this.progress >= steps[i].at) {
                                        this.currentStep = steps[i].text;
                                        break;
                                    }
                                }
                            }
                        }, 500);
                    },
                    
                    completeProgress(url) {
                        if (this.interval) {
                            clearInterval(this.interval);
                        }
                        
                        this.redirectUrl = url;
                        
                        // Animate to 100%
                        const completeInterval = setInterval(() => {
                            if (this.progress < 100) {
                                this.progress = Math.min(100, this.progress + 3);
                            } else {
                                clearInterval(completeInterval);
                                this.isComplete = true;
                                this.currentStep = '✅ Selesai! Modul Ajar berhasil dibuat';
                                
                                // Wait 1.5 seconds then redirect
                                setTimeout(() => {
                                    window.location.href = this.redirectUrl;
                                }, 1500);
                            }
                        }, 50);
                    },
                    
                    showError(message) {
                        if (this.interval) {
                            clearInterval(this.interval);
                        }
                        this.hasError = true;
                        this.errorMessage = message;
                        this.currentStep = '❌ ' + message;
                    },
                    
                    retry() {
                        this.show = false;
                        this.progress = 0;
                        this.hasError = false;
                        this.isComplete = false;
                    }
                }
            }
        </script>


        <div class="mt-6">
            <x-ui.alert type="info">
                <strong>Tips:</strong> Semakin detail informasi yang Anda masukkan (terutama Topik dan Kompetensi Awal), semakin berkualitas Modul Ajar yang dihasilkan AI.
            </x-ui.alert>
        </div>
    </div>
</x-app-layout>
