<x-app-layout>
    <x-slot name="header">Pengaturan AI</x-slot>

    <div class="max-w-3xl mx-auto space-y-6">
        @if (session('success'))
            <x-ui.alert type="success">{{ session('success') }}</x-ui.alert>
        @endif
        @if (session('error'))
            <x-ui.alert type="error">{{ session('error') }}</x-ui.alert>
        @endif

        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-xl font-semibold text-[hsl(var(--foreground))]">Konfigurasi Model AI</h2>
                <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Dipakai untuk generate Modul Ajar. Kolom yang
                    dikosongkan otomatis memakai nilai dari <code>.env</code>.</p>
            </x-slot>

            <form action="{{ route('admin.ai.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- API Key -->
                <div class="space-y-2 pb-4 border-b border-[hsl(var(--border))]">
                    <label for="api_key" class="block text-sm font-medium text-[hsl(var(--foreground))]">API Key</label>
                    <input type="password" id="api_key" name="api_key" autocomplete="new-password"
                        class="input w-full @error('api_key') border-[hsl(var(--destructive))] @enderror"
                        placeholder="{{ $settings->maskedApiKey() ? 'Tersimpan: '.$settings->maskedApiKey().' — isi untuk mengganti' : ($envKeySet ? 'Memakai DEEPSEEK_API_KEY dari .env' : 'Belum diisi') }}">
                    @error('api_key')
                        <p class="text-sm text-[hsl(var(--destructive))]">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-[hsl(var(--muted-foreground))]">Disimpan terenkripsi dan tidak pernah
                        ditampilkan kembali. Kosongkan untuk mempertahankan key yang sekarang.</p>

                    @if ($settings->api_key)
                        <button type="button" class="btn btn-outline btn-sm"
                            onclick="if (confirm('Hapus API key dari database dan kembali memakai nilai .env?')) document.getElementById('delete-api-key-form').submit();">
                            Hapus API key dari database
                        </button>
                    @endif
                </div>

                <!-- Model & Endpoint -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Daftar model bisa ditarik langsung dari provider. Endpoint & key
                         dibaca dari field di form ini, jadi admin bisa mengecek nilai baru
                         sebelum menyimpannya. --}}
                    <div class="space-y-2" x-data="aiModelList(@js(route('admin.ai.models')), @js(['deepseek-chat', 'deepseek-reasoner', 'deepseek-v4-flash']))">
                        <label for="model" class="block text-sm font-medium text-[hsl(var(--foreground))]">Model</label>
                        <div class="flex items-start gap-2">
                            <input type="text" id="model" name="model" list="model-options"
                                value="{{ old('model', $settings->model) }}"
                                class="input w-full @error('model') border-[hsl(var(--destructive))] @enderror"
                                placeholder="{{ config('deepseek.model', 'deepseek-chat') }}">
                            <button type="button" class="btn btn-outline shrink-0" x-on:click="load()"
                                x-bind:disabled="loading">
                                <span x-show="!loading">Reload Model</span>
                                <span x-show="loading" x-cloak>Memuat…</span>
                            </button>
                        </div>
                        <datalist id="model-options">
                            <template x-for="m in models" :key="m">
                                <option x-bind:value="m"></option>
                            </template>
                        </datalist>
                        <p x-show="message" x-cloak x-text="message"
                            x-bind:class="error ? 'text-sm text-[hsl(var(--destructive))]' : 'text-sm text-[hsl(var(--muted-foreground))]'">
                        </p>
                        @error('model')
                            <p class="text-sm text-[hsl(var(--destructive))]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="endpoint"
                            class="block text-sm font-medium text-[hsl(var(--foreground))]">Endpoint</label>
                        <input type="url" id="endpoint" name="endpoint" value="{{ old('endpoint', $settings->endpoint) }}"
                            class="input w-full @error('endpoint') border-[hsl(var(--destructive))] @enderror"
                            placeholder="{{ config('deepseek.endpoint') }}">
                        @error('endpoint')
                            <p class="text-sm text-[hsl(var(--destructive))]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Parameter generate -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-2">
                        <label for="temperature" class="block text-sm font-medium text-[hsl(var(--foreground))]">
                            Temperature
                        </label>
                        <input type="number" id="temperature" name="temperature" step="0.1" min="0" max="2"
                            value="{{ old('temperature', $settings->temperature) }}"
                            class="input w-full @error('temperature') border-[hsl(var(--destructive))] @enderror"
                            placeholder="0.7">
                        @error('temperature')
                            <p class="text-sm text-[hsl(var(--destructive))]">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-[hsl(var(--muted-foreground))]">Rendah = konsisten, tinggi = variatif.
                            Rentang 0&ndash;2.</p>
                    </div>

                    <div class="space-y-2">
                        <label for="max_tokens" class="block text-sm font-medium text-[hsl(var(--foreground))]">
                            Max Tokens
                        </label>
                        <input type="number" id="max_tokens" name="max_tokens" min="256" max="32768" step="256"
                            value="{{ old('max_tokens', $settings->max_tokens) }}"
                            class="input w-full @error('max_tokens') border-[hsl(var(--destructive))] @enderror"
                            placeholder="8192">
                        @error('max_tokens')
                            <p class="text-sm text-[hsl(var(--destructive))]">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-[hsl(var(--muted-foreground))]">Batas panjang keluaran per permintaan.
                            Terlalu kecil membuat modul terpotong.</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-[hsl(var(--border))]">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline">Batal</a>
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                </div>
            </form>
        </x-ui.card>

        <!-- Status aktif + tes koneksi -->
        <x-ui.card>
            <x-slot name="header">
                <h2 class="text-lg font-semibold text-[hsl(var(--foreground))]">Konfigurasi Aktif</h2>
                <p class="text-sm text-[hsl(var(--muted-foreground))] mt-1">Nilai yang benar-benar dipakai saat generate
                    (database jika ada, jika tidak dari .env).</p>
            </x-slot>

            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-[hsl(var(--muted-foreground))]">Model</dt>
                    <dd class="font-medium">{{ $effective['model'] }}</dd>
                </div>
                <div>
                    <dt class="text-[hsl(var(--muted-foreground))]">API Key</dt>
                    <dd class="font-medium">
                        {{ filled($effective['api_key']) ? 'Terpasang' : 'Belum diisi' }}
                        <span class="text-[hsl(var(--muted-foreground))]">
                            ({{ $settings->api_key ? 'database' : ($envKeySet ? '.env' : '-') }})
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-[hsl(var(--muted-foreground))]">Temperature</dt>
                    <dd class="font-medium">{{ $effective['temperature'] }}</dd>
                </div>
                <div>
                    <dt class="text-[hsl(var(--muted-foreground))]">Max Tokens</dt>
                    <dd class="font-medium">{{ $effective['max_tokens'] }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-[hsl(var(--muted-foreground))]">Endpoint</dt>
                    <dd class="font-medium break-all">{{ $effective['endpoint'] }}</dd>
                </div>
            </dl>

            <form action="{{ route('admin.ai.test') }}" method="POST" class="mt-6">
                @csrf
                <button type="submit" class="btn btn-outline">Tes Koneksi</button>
            </form>
        </x-ui.card>
    </div>

    <form id="delete-api-key-form" action="{{ route('admin.ai.delete-key') }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function aiModelList(url, fallback) {
            return {
                // Daftar awal = tebakan statis; diganti isi asli setelah Reload Model.
                models: fallback,
                loading: false,
                message: '',
                error: false,

                async load() {
                    this.loading = true;
                    this.message = '';
                    this.error = false;

                    try {
                        const res = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({
                                endpoint: document.getElementById('endpoint')?.value || '',
                                api_key: document.getElementById('api_key')?.value || '',
                            }),
                        });
                        const data = await res.json();

                        if (!res.ok) {
                            throw new Error(data.message || Object.values(data.errors || {}).flat().join(' ') || 'Gagal memuat model.');
                        }

                        this.models = data.models;
                        this.message = `${data.models.length} model tersedia.`;
                    } catch (e) {
                        this.error = true;
                        this.message = e.message;
                    } finally {
                        this.loading = false;
                    }
                },
            };
        }
    </script>
</x-app-layout>
