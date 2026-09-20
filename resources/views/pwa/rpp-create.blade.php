<x-pwa-layout title="Buat Modul Ajar" active="rpp" :show-install-banner="false">
    <x-slot name="header">
        <div class="relative z-10 flex items-center gap-3 pt-2">
            <a href="{{ route('pwa.home') }}" class="press flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-white/12 backdrop-blur-md ring-1 ring-white/20 text-white" aria-label="Kembali ke beranda">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <p class="pwa-hero-eyebrow">Generator AI Cerdas</p>
                <h1 class="pwa-display pwa-hero-title">Buat Modul Ajar</h1>
            </div>
        </div>
    </x-slot>

    @if (isset($errors) && $errors->any())
        <div class="pop-in rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-[12.5px] font-semibold text-rose-800 shadow-xs">
            Periksa kembali kolom yang ditandai merah di bawah.
        </div>
    @endif

    <form id="rpp-form" action="{{ route('rpp.store') }}" method="POST" class="space-y-3.5" x-data="{ open: 'identitas' }" novalidate>
        @csrf
        <input type="hidden" name="from" value="pwa">

        @php
            $sections = [
                'identitas' => ['Identitas Penyusun', 'Nama guru, kepala sekolah, unit', '01'],
                'umum' => ['Informasi Umum', 'Mapel, fase, kelas, semester', '02'],
                'inti' => ['Komponen Inti', 'Topik, alokasi waktu, model belajar', '03'],
                'kurikulum' => ['Kurikulum & Integrasi', 'Asesmen, nilai, tema dokumen', '04'],
            ];
        @endphp

        @foreach ($sections as $key => [$judul, $sub, $stepNum])
            <section class="pwa-card pop-in overflow-hidden transition-all duration-200" style="--d: {{ $loop->index * 40 }}ms"
                :class="open === '{{ $key }}' ? 'border-blue-200 ring-2 ring-blue-500/10' : ''">
                <button type="button" @click="open = (open === '{{ $key }}' ? '' : '{{ $key }}')"
                    :aria-expanded="open === '{{ $key }}'" aria-controls="bagian-{{ $key }}"
                    class="press flex w-full items-center justify-between p-4 text-left">
                    <span class="flex items-center gap-3">
                        <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-xl text-[11px] font-extrabold"
                            :class="open === '{{ $key }}' ? 'bg-blue-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600'">
                            {{ $stepNum }}
                        </span>
                        <span>
                            <span class="pwa-display block text-[14px] font-extrabold text-slate-900">{{ $judul }}</span>
                            <span class="pwa-sub block text-[11.5px] font-medium text-slate-500">{{ $sub }}</span>
                        </span>
                    </span>
                    <span class="flex h-7 w-7 items-center justify-center rounded-lg text-slate-400">
                        <svg class="h-4 w-4 transition-transform duration-300" :class="open === '{{ $key }}' && 'rotate-180 text-blue-600'"
                            fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </span>
                </button>

                <div id="bagian-{{ $key }}" data-rpp-section="{{ $key }}" x-show="open === '{{ $key }}'" x-transition.opacity.origin.top
                    class="space-y-4 border-t px-4 py-4" style="border-color: var(--line)">
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
                                    <option value="{{ $j }}" @selected(old('jenjang', \App\Models\SchoolSetting::DEFAULT_JENJANG) === $j)>{{ $j }}</option>
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
                                    'A' => 'Fase A (Kelas 1-2 MI/SD)',
                                    'B' => 'Fase B (Kelas 3-4 MI/SD)',
                                    'C' => 'Fase C (Kelas 5-6 MI/SD)',
                                    'D' => 'Fase D (Kelas 7-9 SMP/MTs)',
                                    'E' => 'Fase E (Kelas 10 SMA/SMK)',
                                    'F' => 'Fase F (Kelas 11-12 SMA/SMK)',
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

        <button id="rpp-submit" type="submit"
            class="pwa-display press sticky w-full rounded-2xl py-4 text-[15px] font-extrabold text-white disabled:cursor-wait disabled:opacity-70 shadow-lg flex items-center justify-center gap-2"
            style="bottom: calc(5.75rem + env(safe-area-inset-bottom, 0px)); background: linear-gradient(135deg, #1D4ED8 0%, #2563EB 55%, #4F46E5 100%); box-shadow: 0 10px 25px -4px rgba(37, 99, 235, 0.45)"
            aria-describedby="rpp-submit-hint">
            <svg class="h-5 w-5 text-blue-200" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
            </svg>
            <span data-submit-label>Generate Modul Ajar</span>
        </button>
        <p id="rpp-submit-hint" class="sr-only">Proses membutuhkan koneksi internet dan dapat memerlukan beberapa menit.</p>
    </form>

    <!-- Progress Generate Bottom Sheet Modal -->
    <div x-data="pwaGenerate()" x-show="show" x-cloak @keydown.escape.window="if (failed) reset()"
        role="dialog" aria-modal="true" aria-labelledby="generate-title"
        class="fixed inset-0 z-50 flex items-end bg-slate-950/60 backdrop-blur-md sm:items-center sm:justify-center">
        <div class="pop-in w-full rounded-t-[32px] bg-white px-6 pb-6 pt-3 text-center shadow-2xl sm:max-w-sm sm:rounded-[28px] sm:pt-6"
            style="padding-bottom: calc(1.75rem + env(safe-area-inset-bottom, 0px))">
            <div class="mx-auto mb-5 h-1.5 w-12 rounded-full bg-slate-200 sm:hidden" aria-hidden="true"></div>
            <template x-if="!done && !failed">
                <div>
                    <div class="relative mx-auto mb-4 flex h-18 w-18 items-center justify-center rounded-3xl bg-blue-50/80 border border-blue-100 p-3 shadow-inner">
                        <img src="{{ asset('logo.png') }}" alt="" class="h-10 w-10 object-contain pulse-glow">
                    </div>
                    <h2 id="generate-title" class="pwa-display text-[17px] font-extrabold text-slate-900">AI Sedang Menyusun Modul</h2>
                    <p class="pwa-sub mt-1 text-[12.5px] font-medium text-slate-500" x-text="step" aria-live="polite"></p>
                </div>
            </template>

            <template x-if="done">
                <div>
                    <div class="pop-in mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 border border-emerald-100">
                        <svg class="h-8 w-8 text-emerald-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h2 id="generate-title" class="pwa-display text-[17px] font-extrabold text-emerald-700">Modul Ajar Siap!</h2>
                    <p class="pwa-sub mt-1 text-[12.5px] font-medium text-slate-500">Membuka dokumen lengkap…</p>
                </div>
            </template>

            <template x-if="failed">
                <div>
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-rose-50 border border-rose-100">
                        <svg class="h-8 w-8 text-rose-600" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <h2 id="generate-title" class="pwa-display text-[17px] font-extrabold text-rose-700">Generate Gagal</h2>
                    <p class="pwa-sub mt-1 text-[12.5px] font-medium text-slate-500" x-text="message" role="alert"></p>
                    <button type="button" @click="reset()" class="press mt-5 min-h-12 w-full rounded-2xl py-3 text-[13.5px] font-bold text-white shadow-md"
                        style="background: linear-gradient(135deg, #1D4ED8, #2563EB); box-shadow: var(--sh-brand)">Tutup</button>
                </div>
            </template>

            <div class="mt-5" x-show="!failed">
                <div class="h-2 w-full overflow-hidden rounded-full bg-slate-100" role="progressbar"
                    aria-label="Progres penyusunan modul" aria-valuemin="0" aria-valuemax="100" :aria-valuenow="Math.round(progress)">
                    <div class="h-full rounded-full transition-[width] duration-300" style="background: linear-gradient(90deg, #1D4ED8, #3B82F6)" :style="'width: ' + progress + '%'"></div>
                </div>
                <p class="mt-2 text-[12px] font-extrabold text-blue-600" x-text="Math.round(progress) + '%'"></p>
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
                submitButton: null,

                init() {
                    this.submitButton = document.getElementById('rpp-submit');
                    document.getElementById('rpp-form').addEventListener('submit', (event) => {
                        event.preventDefault();
                        if (!event.target.checkValidity()) {
                            this.revealInvalidField(event.target);
                            return;
                        }
                        this.submit(event.target);
                    });
                },

                revealInvalidField(form) {
                    const invalid = form.querySelector(':invalid');
                    if (!invalid) return;

                    const section = invalid.closest('[data-rpp-section]');
                    const trigger = section ? form.querySelector(`[aria-controls="${section.id}"]`) : null;
                    if (trigger?.getAttribute('aria-expanded') !== 'true') trigger?.click();

                    queueMicrotask(() => requestAnimationFrame(() => {
                        invalid.focus({ preventScroll: true });
                        invalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        invalid.reportValidity();
                    }));
                },

                async submit(form) {
                    if (this.submitButton?.disabled) return;

                    this.setSubmitting(true);
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
                        const contentType = response.headers.get('content-type') || '';
                        const data = contentType.includes('application/json') ? await response.json() : {};

                        if (response.ok && data.success && data.status === 'completed') {
                            this.finish(data.redirect_url);
                        } else {
                            const validationError = Object.values(data.errors || {}).flat()[0];
                            this.fail(validationError || data.error || data.message || 'Periksa kembali data yang diisi.');
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
                    navigator.vibrate?.(12);
                    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
                    setTimeout(() => window.location.assign(url), reducedMotion ? 0 : 400);
                },

                fail(message) {
                    clearInterval(this.timer);
                    this.failed = true;
                    this.message = message;
                    navigator.vibrate?.([18, 50, 18]);
                    this.setSubmitting(false);
                },

                reset() {
                    this.show = false;
                    this.progress = 0;
                    this.failed = false;
                    this.setSubmitting(false);
                },

                setSubmitting(submitting) {
                    if (!this.submitButton) return;
                    this.submitButton.disabled = submitting;
                    this.submitButton.setAttribute('aria-busy', String(submitting));
                    const label = this.submitButton.querySelector('[data-submit-label]');
                    if (label) label.textContent = submitting ? 'Sedang menyusun…' : 'Generate Modul Ajar';
                },
            };
        }
    </script>
</x-pwa-layout>
