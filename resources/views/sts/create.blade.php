<x-app-layout>
    <x-slot name="header">Buat Soal STS AI</x-slot>

    <div class="max-w-3xl mx-auto">
        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">Form Generate Soal STS</h2>
                <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Isi data lengkap untuk menghasilkan Soal Sumatif Tengah Semester dengan AI.</p>
            </x-slot>

            <form id="sts-form" action="{{ route('sts.store') }}" method="POST" class="space-y-6" x-data="{ loading: false }">
                @csrf

                <!-- Informasi Umum -->
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
                            ]"
                            placeholder="Pilih Fase/Jenjang"
                            :value="old('fase')"
                            :error="$errors->first('fase')"
                            required
                        />
                    </div>

                    <x-ui.input
                        name="kelas"
                        label="Kelas"
                        placeholder="Contoh: 7, 8, 10, 11"
                        :value="old('kelas')"
                        :error="$errors->first('kelas')"
                        required
                    />
                </div>

                <!-- Materi -->
                <div class="space-y-4 pb-4 border-b border-[hsl(var(--border))]">
                    <h3 class="text-sm font-semibold text-[hsl(var(--muted-foreground))] uppercase tracking-wide">Materi & Tujuan</h3>

                    <x-ui.textarea
                        name="topik"
                        label="Topik/Materi"
                        placeholder="Contoh: Teks Deskripsi dan Puisi Rakyat, Operasi Bilangan Bulat"
                        rows="2"
                        :error="$errors->first('topik')"
                        required
                    >{{ old('topik') }}</x-ui.textarea>

                    <x-ui.textarea
                        name="tujuan_pembelajaran"
                        label="Tujuan Pembelajaran (opsional)"
                        placeholder="Salin TP dari modul ajar jika ada, atau biarkan AI merumuskan berdasarkan topik"
                        rows="3"
                        :error="$errors->first('tujuan_pembelajaran')"
                    >{{ old('tujuan_pembelajaran') }}</x-ui.textarea>

                    <x-ui.textarea
                        name="materi"
                        label="Materi Tambahan (opsional)"
                        placeholder="Tempelkan materi yang akan dijadikan dasar pembuatan soal. Semakin detail, semakin baik hasilnya."
                        rows="4"
                        :error="$errors->first('materi')"
                    >{{ old('materi') }}</x-ui.textarea>
                </div>

                <!-- Spesifikasi Soal -->
                <div class="space-y-4">
                    <h3 class="text-sm font-semibold text-[hsl(var(--muted-foreground))] uppercase tracking-wide">Spesifikasi Soal</h3>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <x-ui.input
                            type="number"
                            name="jumlah_pg"
                            label="Pilihan Ganda"
                            :value="old('jumlah_pg', 10)"
                            :error="$errors->first('jumlah_pg')"
                            min="0"
                            max="30"
                            required
                        />

                        <x-ui.input
                            type="number"
                            name="jumlah_pg_kompleks"
                            label="PG Kompleks"
                            :value="old('jumlah_pg_kompleks', 3)"
                            :error="$errors->first('jumlah_pg_kompleks')"
                            min="0"
                            max="10"
                            required
                        />

                        <x-ui.input
                            type="number"
                            name="jumlah_menjodohkan"
                            label="Menjodohkan"
                            :value="old('jumlah_menjodohkan', 5)"
                            :error="$errors->first('jumlah_menjodohkan')"
                            min="0"
                            max="10"
                            required
                        />

                        <x-ui.input
                            type="number"
                            name="jumlah_uraian"
                            label="Uraian/Esai"
                            :value="old('jumlah_uraian', 2)"
                            :error="$errors->first('jumlah_uraian')"
                            min="0"
                            max="10"
                            required
                        />
                    </div>

                    <!-- Total Soal Display -->
                    <div class="mt-4 p-4 bg-[hsl(var(--accent))] rounded-lg" x-data="totalSoalCalculator()">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-[hsl(var(--muted-foreground))]">Total Soal:</span>
                            <span class="text-xl font-bold text-[hsl(var(--primary))]" x-text="total + ' soal'"></span>
                        </div>
                    </div>
                </div>

                <script>
                    function totalSoalCalculator() {
                        return {
                            total: 20,
                            init() {
                                this.calculateTotal();
                                const inputs = ['jumlah_pg', 'jumlah_pg_kompleks', 'jumlah_menjodohkan', 'jumlah_uraian'];
                                inputs.forEach(name => {
                                    const input = document.querySelector(`[name="${name}"]`);
                                    if (input) {
                                        input.addEventListener('input', () => this.calculateTotal());
                                    }
                                });
                            },
                            calculateTotal() {
                                const pg = parseInt(document.querySelector('[name="jumlah_pg"]')?.value) || 0;
                                const pgKompleks = parseInt(document.querySelector('[name="jumlah_pg_kompleks"]')?.value) || 0;
                                const menjodohkan = parseInt(document.querySelector('[name="jumlah_menjodohkan"]')?.value) || 0;
                                const uraian = parseInt(document.querySelector('[name="jumlah_uraian"]')?.value) || 0;
                                this.total = pg + pgKompleks + menjodohkan + uraian;
                            }
                        }
                    }
                </script>

                <div class="flex items-center justify-end gap-4 pt-4 border-t border-[hsl(var(--border))]">
                    <a href="{{ route('sts.index') }}" class="btn btn-outline" x-show="!loading">Batal</a>
                    
                    <button type="submit" class="btn btn-primary" x-show="!loading">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Generate Soal STS
                    </button>

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
        <div x-data="progressLoader()" 
             x-show="show" 
             x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm">
            <div class="bg-white rounded-3xl p-8 shadow-2xl max-w-md mx-4 text-center w-full">
                
                <!-- Processing State -->
                <template x-if="!isComplete && !hasError">
                    <div>
                        <div class="mb-6">
                            <img src="{{ asset('refrensi/loading.gif') }}" alt="Loading Animation" class="w-40 h-40 mx-auto object-contain">
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">📝 AI Sedang Membuat Soal</h3>
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
                        <p class="text-gray-500 mb-6">Soal STS berhasil dibuat. Mengalihkan...</p>
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
                
                <!-- Progress Bar -->
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

                <!-- Step indicators -->
                <div class="space-y-2 text-left bg-gray-50 rounded-xl p-4 mt-4" x-show="!hasError">
                    <div class="flex items-center gap-3" :class="progress >= 20 ? 'text-green-600' : 'text-gray-400'">
                        <template x-if="progress >= 20">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                        <template x-if="progress < 20">
                            <div class="w-4 h-4 border-2 border-current rounded-full animate-pulse flex-shrink-0"></div>
                        </template>
                        <span class="text-sm" :class="progress >= 20 ? 'font-medium' : ''">📋 Menganalisis materi</span>
                    </div>
                    
                    <div class="flex items-center gap-3" :class="progress >= 45 ? 'text-green-600' : 'text-gray-400'">
                        <template x-if="progress >= 45">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                        <template x-if="progress < 45">
                            <div class="w-4 h-4 border-2 border-current rounded-full animate-pulse flex-shrink-0"></div>
                        </template>
                        <span class="text-sm" :class="progress >= 45 ? 'font-medium' : ''">📝 Menyusun soal & kisi-kisi</span>
                    </div>
                    
                    <div class="flex items-center gap-3" :class="progress >= 70 ? 'text-green-600' : 'text-gray-400'">
                        <template x-if="progress >= 70">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                        <template x-if="progress < 70">
                            <div class="w-4 h-4 border-2 border-current rounded-full animate-pulse flex-shrink-0"></div>
                        </template>
                        <span class="text-sm" :class="progress >= 70 ? 'font-medium' : ''">✅ Membuat kunci jawaban</span>
                    </div>
                    
                    <div class="flex items-center gap-3" :class="progress >= 100 ? 'text-green-600' : 'text-blue-500'">
                        <template x-if="progress >= 100">
                            <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                        </template>
                        <template x-if="progress < 100">
                            <svg class="w-4 h-4 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                            </svg>
                        </template>
                        <span class="text-sm" :class="progress >= 100 ? 'font-medium text-green-600' : 'font-semibold'">🚀 Menyusun rubrik penilaian</span>
                    </div>
                </div>
                
                <p class="text-xs text-gray-400 mt-4" x-show="!isComplete && !hasError">⏱️ Estimasi waktu: 60-120 detik</p>
            </div>
        </div>

        <script>
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
                        const form = document.getElementById('sts-form');
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
                            { at: 20, text: 'Menganalisis materi...' },
                            { at: 45, text: 'Menyusun soal & kisi-kisi...' },
                            { at: 70, text: 'Membuat kunci jawaban...' },
                            { at: 85, text: 'Menyusun rubrik penilaian...' },
                            { at: 95, text: 'Hampir selesai...' },
                        ];
                        
                        this.interval = setInterval(() => {
                            if (this.progress < 95 && !this.isComplete) {
                                let increment = this.progress < 30 ? 1.5 : 
                                               this.progress < 60 ? 1 : 
                                               this.progress < 85 ? 0.7 : 0.3;
                                
                                this.progress = Math.min(95, this.progress + increment);
                                
                                for (let i = steps.length - 1; i >= 0; i--) {
                                    if (this.progress >= steps[i].at) {
                                        this.currentStep = steps[i].text;
                                        break;
                                    }
                                }
                            }
                        }, 800);
                    },
                    
                    completeProgress(url) {
                        if (this.interval) {
                            clearInterval(this.interval);
                        }
                        
                        this.redirectUrl = url;
                        
                        const completeInterval = setInterval(() => {
                            if (this.progress < 100) {
                                this.progress = Math.min(100, this.progress + 3);
                            } else {
                                clearInterval(completeInterval);
                                this.isComplete = true;
                                this.currentStep = '✅ Selesai! Soal STS berhasil dibuat';
                                
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
                <strong>Tips:</strong> Semakin detail topik dan materi yang Anda masukkan, semakin berkualitas soal yang dihasilkan AI.
            </x-ui.alert>
        </div>
    </div>
</x-app-layout>
