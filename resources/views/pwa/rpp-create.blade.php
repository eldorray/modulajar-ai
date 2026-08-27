<x-pwa-layout title="Buat Modul Ajar" active="rpp">
    <x-slot name="header">
        <div class="flex items-center gap-3 pt-3">
            <a href="{{ route('pwa.home') }}" class="press flex h-10 w-10 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/25">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <p class="pwa-hero-eyebrow">Generate dengan AI</p>
                <h1 class="pwa-display pwa-hero-title">Buat Modul Ajar</h1>
            </div>
        </div>
    </x-slot>

    @if ($errors->any())
        <div class="pop-in rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-[12.5px] font-semibold text-rose-700">
            Periksa kembali kolom yang ditandai.
        </div>
    @endif

    <form id="rpp-form" action="{{ route('rpp.store') }}" method="POST" class="space-y-4" x-data="{ open: 'identitas' }">
        @csrf
        <input type="hidden" name="from" value="pwa">

        @php
            $sections = [
                'identitas' => ['Identitas penyusun', 'Nama, kepala sekolah, unit'],
                'umum' => ['Informasi umum', 'Mapel, fase, kelas, semester'],
                'inti' => ['Komponen inti', 'Topik, alokasi, model'],
                'kurikulum' => ['Kurikulum & integrasi', 'Asesmen, nilai, tema warna'],
            ];
        @endphp

        @foreach ($sections as $key => [$judul, $sub])
            <section class="pwa-card pop-in overflow-hidden" style="--d: {{ $loop->index * 70 }}ms">
                <button type="button" @click="open = (open === '{{ $key }}' ? '' : '{{ $key }}')"
                    class="press flex w-full items-center justify-between px-4 py-4 text-left">
                    <span>
                        <span class="pwa-display block text-[14px] font-extrabold">{{ $judul }}</span>
                        <span class="pwa-sub block text-[11.5px] font-medium">{{ $sub }}</span>
                    </span>
                    <svg class="h-5 w-5 transition-transform duration-300" style="color: #A9BBD6" :class="open === '{{ $key }}' && 'rotate-180'"
                        fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                    </svg>
                </button>

                <div x-show="open === '{{ $key }}'" x-transition.origin.top class="space-y-4 border-t px-4 py-4" style="border-color: var(--line)">
                    @if ($key === 'identitas')
                        <div>
                            <label class="pwa-label" for="nama_guru">Nama penyusun</label>
                            <input id="nama_guru" name="nama_guru" class="pwa-field" required
                                value="{{ old('nama_guru', auth()->user()->name) }}">
                        </div>
                        <div>
                            <label class="pwa-label" for="jenjang">Unit sekolah</label>
                            <select id="jenjang" name="jenjang" class="pwa-field" required>
                                @foreach (\App\Models\SchoolSetting::JENJANG as $j)
                                    <option value="{{ $j }}" @selected(old('jenjang', 'MI') === $j)>{{ $j }}</option>
                                @endforeach
                            </select>
                            <p class="pwa-sub mt-1 text-[11px]">Menentukan logo dan nama sekolah di cover dokumen.</p>
                        </div>
                        <div>
                            <label class="pwa-label" for="kepala_sekolah">Kepala sekolah</label>
                            <input id="kepala_sekolah" name="kepala_sekolah" class="pwa-field" value="{{ old('kepala_sekolah') }}">
                        </div>
                        <div>
                            <label class="pwa-label" for="nip_kepala_sekolah">NIP kepala sekolah</label>
                            <input id="nip_kepala_sekolah" name="nip_kepala_sekolah" class="pwa-field" value="{{ old('nip_kepala_sekolah') }}">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="pwa-label" for="kota">Kota</label>
                                <input id="kota" name="kota" class="pwa-field" value="{{ old('kota') }}">
                            </div>
                            <div>
                                <label class="pwa-label" for="tanggal">Tanggal</label>
                                <input id="tanggal" type="date" name="tanggal" class="pwa-field" value="{{ old('tanggal', date('Y-m-d')) }}">
                            </div>
                        </div>
                    @endif

                    @if ($key === 'umum')
                        <div>
                            <label class="pwa-label" for="mata_pelajaran">Mata pelajaran</label>
                            <input id="mata_pelajaran" name="mata_pelajaran" class="pwa-field" required
                                placeholder="Matematika" value="{{ old('mata_pelajaran') }}">
                        </div>
                        <div>
                            <label class="pwa-label" for="fase">Fase / jenjang</label>
                            <select id="fase" name="fase" class="pwa-field" required>
                                <option value="">Pilih fase</option>
                                @foreach ([
                                    'A' => 'Fase A (Kelas 1-2 SD)',
                                    'B' => 'Fase B (Kelas 3-4 SD)',
                                    'C' => 'Fase C (Kelas 5-6 SD)',
                                    'D' => 'Fase D (Kelas 7-9 SMP)',
                                    'E' => 'Fase E (Kelas 10 SMA)',
                                    'F' => 'Fase F (Kelas 11-12 SMA)',
                                    'RA' => 'RA (Raudhatul Athfal)',
                                    'MI Rendah' => 'MI Kelas 1-3',
                                    'MI Tinggi' => 'MI Kelas 4-6',
                                    'MTs' => 'MTs Kelas 7-9',
                                    'MA' => 'MA Kelas 10-12',
                                ] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('fase') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="pwa-label" for="kelas">Kelas</label>
                                <input id="kelas" name="kelas" class="pwa-field" placeholder="7" value="{{ old('kelas') }}">
                            </div>
                            <div>
                                <label class="pwa-label" for="semester">Semester</label>
                                <select id="semester" name="semester" class="pwa-field">
                                    <option value="Ganjil" @selected(old('semester') === 'Ganjil')>Ganjil</option>
                                    <option value="Genap" @selected(old('semester') === 'Genap')>Genap</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="pwa-label" for="target_peserta_didik">Target peserta didik</label>
                            <select id="target_peserta_didik" name="target_peserta_didik" class="pwa-field">
                                @foreach (['Reguler', 'Kesulitan Belajar', 'Pencapaian Tinggi'] as $target)
                                    <option value="{{ $target }}" @selected(old('target_peserta_didik', 'Reguler') === $target)>{{ $target }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if ($key === 'inti')
                        <div>
                            <label class="pwa-label" for="topik">Topik / materi</label>
                            <textarea id="topik" name="topik" rows="3" class="pwa-field" required
                                placeholder="Operasi hitung bilangan bulat dan penerapannya">{{ old('topik') }}</textarea>
                        </div>
                        <div>
                            <label class="pwa-label" for="kompetensi_awal">Kompetensi awal</label>
                            <textarea id="kompetensi_awal" name="kompetensi_awal" rows="2" class="pwa-field">{{ old('kompetensi_awal') }}</textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="pwa-label" for="alokasi_waktu">Alokasi waktu</label>
                                <input id="alokasi_waktu" name="alokasi_waktu" class="pwa-field" required
                                    placeholder="2 x 35 menit" value="{{ old('alokasi_waktu') }}">
                            </div>
                            <div>
                                <label class="pwa-label" for="jumlah_pertemuan">Pertemuan</label>
                                <input id="jumlah_pertemuan" type="number" min="1" max="10" name="jumlah_pertemuan"
                                    class="pwa-field" value="{{ old('jumlah_pertemuan', 1) }}">
                            </div>
                        </div>
                        <div>
                            <label class="pwa-label" for="kata_kunci">Kata kunci</label>
                            <input id="kata_kunci" name="kata_kunci" class="pwa-field"
                                placeholder="bilangan bulat, operasi hitung" value="{{ old('kata_kunci') }}">
                        </div>
                        <div>
                            <label class="pwa-label" for="model_pembelajaran">Model pembelajaran</label>
                            <select id="model_pembelajaran" name="model_pembelajaran" class="pwa-field">
                                @foreach ([
                                    'Problem Based Learning' => 'Problem Based Learning (PBL)',
                                    'Project Based Learning' => 'Project Based Learning (PjBL)',
                                    'Discovery Learning' => 'Discovery Learning',
                                    'Inquiry Learning' => 'Inquiry Learning',
                                    'Cooperative Learning' => 'Cooperative Learning',
                                    'Contextual Teaching and Learning' => 'Contextual Teaching and Learning (CTL)',
                                    'Diferensiasi' => 'Pembelajaran Diferensiasi',
                                ] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('model_pembelajaran') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    @if ($key === 'kurikulum')
                        <div>
                            <label class="pwa-label" for="kurikulum">Kurikulum</label>
                            <select id="kurikulum" name="kurikulum" class="pwa-field" required>
                                @foreach ([
                                    'Kurikulum Merdeka' => 'Kurikulum Merdeka',
                                    'Kurikulum Merdeka Belajar' => 'Kurikulum Merdeka Belajar',
                                    'Kurikulum Merdeka Deep Learning' => 'Kurikulum Merdeka Deep Learning',
                                    'Kurikulum Berbasis Cinta' => 'Kurikulum Berbasis Cinta (Kemenag)',
                                ] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('kurikulum', 'Kurikulum Merdeka') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        @php
                            $selectedAsesmen = old('jenis_asesmen', ['Diagnostik Kognitif', 'Diagnostik Non-Kognitif', 'Formatif', 'Sumatif']);
                            $selectedAsesmen = is_array($selectedAsesmen) ? $selectedAsesmen : array_filter(array_map('trim', explode(',', $selectedAsesmen)));
                        @endphp
                        <div>
                            <span class="pwa-label">Jenis asesmen</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach (['Diagnostik Kognitif', 'Diagnostik Non-Kognitif', 'Formatif', 'Sumatif'] as $asesmen)
                                    <label class="pwa-chip press cursor-pointer">
                                        <input type="checkbox" name="jenis_asesmen[]" value="{{ $asesmen }}" class="sr-only"
                                            @checked(in_array($asesmen, $selectedAsesmen))>
                                        {{ $asesmen }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <span class="pwa-label">Integrasi nilai</span>
                            <div class="flex flex-wrap gap-2">
                                @foreach (['panca_cinta' => '💗 Panca Cinta', 'adiwiyata' => '🌱 Adiwiyata', 'kka' => '🤖 KKA'] as $name => $label)
                                    <label class="pwa-chip press cursor-pointer">
                                        <input type="checkbox" name="{{ $name }}" value="1" class="sr-only" @checked(old($name))>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <span class="pwa-label">Tema warna dokumen</span>
                            <div class="flex items-center gap-3">
                                @foreach (config('rpp_themes') as $key => $tema)
                                    <label class="press cursor-pointer" title="{{ $tema['label'] }}">
                                        <input type="radio" name="tema" value="{{ $key }}" class="peer sr-only"
                                            @checked(old('tema', 'merah') === $key)>
                                        <span class="block h-9 w-9 rounded-full ring-1 ring-[#E9EFFA] transition peer-checked:ring-[3px] peer-checked:ring-[#1552F0]"
                                            style="background: linear-gradient(135deg, #{{ $tema['primary'] }} 60%, #{{ $tema['accent'] }} 60%); box-shadow: var(--sh-soft)"></span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        @endforeach

        <button type="submit" class="pwa-display press sticky bottom-[92px] w-full rounded-2xl py-4 text-[15px] font-extrabold text-white"
            style="background: linear-gradient(150deg, var(--brand-700), var(--brand-500)); box-shadow: var(--sh-brand)">
            Generate Modul Ajar
        </button>
    </form>

    <!-- Progress generate -->
    <div x-data="pwaGenerate()" x-show="show" x-cloak
        class="fixed inset-0 z-50 flex items-end bg-[#08183A]/60 backdrop-blur-sm sm:items-center sm:justify-center">
        <div class="slide-up w-full rounded-t-[26px] bg-white p-6 text-center sm:max-w-sm sm:rounded-[26px]">
            <template x-if="!done && !failed">
                <div>
                    <img src="{{ asset('logo.png') }}" alt="" class="float mx-auto mb-4 h-16 w-16 object-contain">
                    <h2 class="pwa-display text-[17px] font-extrabold">AI sedang menyusun</h2>
                    <p class="pwa-sub mt-1 text-[12.5px] font-medium" x-text="step"></p>
                </div>
            </template>

            <template x-if="done">
                <div>
                    <div class="pop-in mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50">
                        <svg class="h-8 w-8 text-emerald-500" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 class="pwa-display text-[17px] font-extrabold" style="color: var(--mint)">Modul ajar siap</h2>
                    <p class="pwa-sub mt-1 text-[12.5px] font-medium">Membuka dokumen…</p>
                </div>
            </template>

            <template x-if="failed">
                <div>
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-rose-50">
                        <svg class="h-8 w-8 text-rose-500" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h2 class="pwa-display text-[17px] font-extrabold" style="color: var(--rose)">Generate gagal</h2>
                    <p class="pwa-sub mt-1 text-[12.5px] font-medium" x-text="message"></p>
                    <button @click="reset()" class="press mt-5 w-full rounded-2xl py-3.5 text-[14px] font-bold text-white" style="background: linear-gradient(150deg, var(--brand-700), var(--brand-500))">Tutup</button>
                </div>
            </template>

            <div class="mt-5" x-show="!failed">
                <div class="h-2.5 w-full overflow-hidden rounded-full" style="background: #F1F5FD">
                    <div class="h-full rounded-full transition-all duration-300" style="background: linear-gradient(90deg, var(--brand-700), var(--brand-500))"
                        :style="'width: ' + progress + '%'"></div>
                </div>
                <p class="mt-2 text-[11.5px] font-extrabold" style="color: var(--brand-700)" x-text="Math.round(progress) + '%'"></p>
            </div>
        </div>
    </div>

    <script>
        function pwaGenerate() {
            return {
                show: false,
                progress: 0,
                step: 'Mengirim data ke AI…',
                done: false,
                failed: false,
                message: '',
                timer: null,

                init() {
                    document.getElementById('rpp-form').addEventListener('submit', (event) => {
                        event.preventDefault();
                        this.submit(event.target);
                    });
                },

                async submit(form) {
                    this.show = true;
                    this.done = false;
                    this.failed = false;
                    this.progress = 0;
                    this.tick();

                    try {
                        const response = await fetch(form.action, {
                            method: 'POST',
                            body: new FormData(form),
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                        });
                        const data = await response.json();

                        if (data.success) {
                            this.finish(data.redirect_url);
                        } else {
                            this.fail(data.error || 'Periksa kembali data yang diisi.');
                        }
                    } catch (error) {
                        this.fail('Koneksi terputus. Coba lagi setelah jaringan stabil.');
                    }
                },

                tick() {
                    const steps = [
                        [10, 'Menganalisis informasi umum…'],
                        [35, 'Menyusun kegiatan pembelajaran…'],
                        [60, 'Membuat asesmen dan rubrik…'],
                        [85, 'Merapikan modul ajar…'],
                    ];

                    this.timer = setInterval(() => {
                        if (this.progress >= 95 || this.done) return;
                        this.progress = Math.min(95, this.progress + (this.progress < 60 ? 1.6 : 0.8));
                        const current = steps.filter(([at]) => this.progress >= at).pop();
                        if (current) this.step = current[1];
                    }, 500);
                },

                finish(url) {
                    clearInterval(this.timer);
                    this.progress = 100;
                    this.done = true;
                    setTimeout(() => window.location.href = url, 1200);
                },

                fail(message) {
                    clearInterval(this.timer);
                    this.failed = true;
                    this.message = message;
                },

                reset() {
                    this.show = false;
                    this.progress = 0;
                    this.failed = false;
                },
            };
        }
    </script>
</x-pwa-layout>
