<x-app-layout>
    <x-slot name="header">Buat Kunci Jawaban</x-slot>

    <div class="max-w-4xl mx-auto">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Input Kunci Jawaban</x-ui.card-title>
                <x-ui.card-description>Buat kunci jawaban baru untuk koreksi LJK otomatis.</x-ui.card-description>
            </x-ui.card-header>

            <x-ui.card-content>
                <form action="{{ route('ljk-answer-keys.store') }}" method="POST" class="space-y-6" id="answerKeyForm">
                    @csrf

                    <!-- Basic Info -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nama" class="label">Nama Kunci Jawaban <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="nama" id="nama" value="{{ old('nama') }}"
                                class="input w-full" placeholder="Contoh: STS Matematika Kelas 7A" required>
                            @error('nama')
                                <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="ljk_template_id" class="label">Template LJK (Opsional)</label>
                            <select name="ljk_template_id" id="ljk_template_id" class="input w-full">
                                <option value="">-- Pilih Template --</option>
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}" data-soal="{{ $template->jumlah_soal }}"
                                        data-pilihan="{{ $template->jumlah_pilihan }}"
                                        {{ old('ljk_template_id', $selectedTemplate?->id ?? '') == $template->id ? 'selected' : '' }}>
                                        {{ $template->nama_template }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="mata_pelajaran" class="label">Mata Pelajaran <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="mata_pelajaran" id="mata_pelajaran"
                                value="{{ old('mata_pelajaran') }}" class="input w-full"
                                placeholder="Contoh: Matematika" required>
                        </div>

                        <div>
                            <label for="kelas" class="label">Kelas</label>
                            <input type="text" name="kelas" id="kelas" value="{{ old('kelas') }}"
                                class="input w-full" placeholder="Contoh: VII-A">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="jumlah_soal" class="label">Jumlah Soal <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="jumlah_soal" id="jumlah_soal"
                                value="{{ old('jumlah_soal', $selectedTemplate?->jumlah_soal ?? 40) }}" min="5"
                                max="100" class="input w-full" required>
                        </div>

                        <div>
                            <label for="jumlah_pilihan" class="label">Jumlah Pilihan <span
                                    class="text-red-500">*</span></label>
                            <select name="jumlah_pilihan" id="jumlah_pilihan" class="input w-full" required>
                                <option value="4"
                                    {{ old('jumlah_pilihan', $selectedTemplate?->jumlah_pilihan ?? 4) == 4 ? 'selected' : '' }}>
                                    4 Pilihan (A, B, C, D)</option>
                                <option value="5"
                                    {{ old('jumlah_pilihan', $selectedTemplate?->jumlah_pilihan ?? 4) == 5 ? 'selected' : '' }}>
                                    5 Pilihan (A, B, C, D, E)</option>
                                <option value="3"
                                    {{ old('jumlah_pilihan', $selectedTemplate?->jumlah_pilihan ?? 4) == 3 ? 'selected' : '' }}>
                                    3 Pilihan (A, B, C)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Answer Key Grid -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <label class="label">Kunci Jawaban <span class="text-red-500">*</span></label>
                            <button type="button" id="btnGenerateGrid" class="btn btn-secondary btn-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Generate Grid
                            </button>
                        </div>

                        <div class="bg-[hsl(var(--muted)/.3)] border rounded-lg p-4">
                            <p class="text-sm text-[hsl(var(--muted-foreground))] mb-4">Klik pada pilihan jawaban untuk
                                setiap nomor soal:</p>

                            <div id="answerGrid" class="grid grid-cols-5 md:grid-cols-10 gap-2">
                                <!-- Grid will be generated by JavaScript -->
                            </div>
                        </div>

                        @error('kunci_jawaban')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                        @error('kunci_jawaban.*')
                            <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center gap-3 pt-4 border-t">
                        <button type="submit" class="btn btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Simpan Kunci Jawaban
                        </button>
                        <a href="{{ route('ljk-answer-keys.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </x-ui.card-content>
        </x-ui.card>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jumlahSoalInput = document.getElementById('jumlah_soal');
            const jumlahPilihanSelect = document.getElementById('jumlah_pilihan');
            const templateSelect = document.getElementById('ljk_template_id');
            const answerGrid = document.getElementById('answerGrid');
            const btnGenerateGrid = document.getElementById('btnGenerateGrid');

            const optionLabels = ['A', 'B', 'C', 'D', 'E'];

            function generateGrid() {
                const jumlahSoal = parseInt(jumlahSoalInput.value) || 40;
                const jumlahPilihan = parseInt(jumlahPilihanSelect.value) || 4;

                answerGrid.innerHTML = '';

                for (let i = 1; i <= jumlahSoal; i++) {
                    const itemDiv = document.createElement('div');
                    itemDiv.className = 'bg-white border rounded p-2 text-center';

                    const numberDiv = document.createElement('div');
                    numberDiv.className = 'font-bold text-sm mb-1';
                    numberDiv.textContent = i;
                    itemDiv.appendChild(numberDiv);

                    const optionsDiv = document.createElement('div');
                    optionsDiv.className = 'flex justify-center gap-1 flex-wrap';

                    for (let j = 0; j < jumlahPilihan; j++) {
                        const optionBtn = document.createElement('button');
                        optionBtn.type = 'button';
                        optionBtn.className =
                            'w-6 h-6 text-xs border rounded hover:bg-primary hover:text-white transition-colors answer-option';
                        optionBtn.textContent = optionLabels[j];
                        optionBtn.dataset.number = i;
                        optionBtn.dataset.option = optionLabels[j];

                        optionBtn.addEventListener('click', function() {
                            // Deselect other options in same number
                            itemDiv.querySelectorAll('.answer-option').forEach(btn => {
                                btn.classList.remove('bg-primary', 'text-white');
                            });
                            // Select this option
                            this.classList.add('bg-primary', 'text-white');
                            // Update hidden input
                            updateHiddenInput(i, optionLabels[j]);
                        });

                        optionsDiv.appendChild(optionBtn);
                    }

                    // Hidden input for this answer
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `kunci_jawaban[${i - 1}]`;
                    hiddenInput.id = `answer_${i}`;
                    hiddenInput.required = true;
                    itemDiv.appendChild(hiddenInput);

                    itemDiv.appendChild(optionsDiv);
                    answerGrid.appendChild(itemDiv);
                }
            }

            function updateHiddenInput(number, option) {
                const input = document.getElementById(`answer_${number}`);
                if (input) {
                    input.value = option;
                }
            }

            // Event listeners
            btnGenerateGrid.addEventListener('click', generateGrid);
            jumlahSoalInput.addEventListener('change', generateGrid);
            jumlahPilihanSelect.addEventListener('change', generateGrid);

            templateSelect.addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                if (selected.value) {
                    jumlahSoalInput.value = selected.dataset.soal;
                    jumlahPilihanSelect.value = selected.dataset.pilihan;
                    generateGrid();
                }
            });

            // Generate grid on page load
            generateGrid();
        });
    </script>
</x-app-layout>
