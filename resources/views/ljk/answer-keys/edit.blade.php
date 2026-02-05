<x-app-layout>
    <x-slot name="header">Edit Kunci Jawaban</x-slot>

    <div class="max-w-4xl mx-auto">
        <x-ui.card>
            <x-ui.card-header>
                <x-ui.card-title>Edit: {{ $ljkAnswerKey->nama }}</x-ui.card-title>
            </x-ui.card-header>

            <x-ui.card-content>
                <form action="{{ route('ljk-answer-keys.update', $ljkAnswerKey) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="nama" class="label">Nama Kunci Jawaban <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="nama" id="nama"
                                value="{{ old('nama', $ljkAnswerKey->nama) }}" class="input w-full" required>
                        </div>

                        <div>
                            <label for="ljk_template_id" class="label">Template LJK</label>
                            <select name="ljk_template_id" id="ljk_template_id" class="input w-full">
                                <option value="">-- Pilih Template --</option>
                                @foreach ($templates as $template)
                                    <option value="{{ $template->id }}"
                                        {{ old('ljk_template_id', $ljkAnswerKey->ljk_template_id) == $template->id ? 'selected' : '' }}>
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
                                value="{{ old('mata_pelajaran', $ljkAnswerKey->mata_pelajaran) }}" class="input w-full"
                                required>
                        </div>

                        <div>
                            <label for="kelas" class="label">Kelas</label>
                            <input type="text" name="kelas" id="kelas"
                                value="{{ old('kelas', $ljkAnswerKey->kelas) }}" class="input w-full">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="jumlah_soal" class="label">Jumlah Soal <span
                                    class="text-red-500">*</span></label>
                            <input type="number" name="jumlah_soal" id="jumlah_soal"
                                value="{{ old('jumlah_soal', $ljkAnswerKey->jumlah_soal) }}" min="5"
                                max="100" class="input w-full" required>
                        </div>

                        <div>
                            <label for="jumlah_pilihan" class="label">Jumlah Pilihan <span
                                    class="text-red-500">*</span></label>
                            <select name="jumlah_pilihan" id="jumlah_pilihan" class="input w-full" required>
                                @foreach ([3, 4, 5] as $n)
                                    <option value="{{ $n }}"
                                        {{ old('jumlah_pilihan', $ljkAnswerKey->jumlah_pilihan) == $n ? 'selected' : '' }}>
                                        {{ $n }} Pilihan</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Answer Key Grid -->
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <label class="label">Kunci Jawaban <span class="text-red-500">*</span></label>
                            <button type="button" id="btnRegenerate" class="btn btn-secondary btn-sm">
                                Regenerate Grid
                            </button>
                        </div>

                        <div class="bg-[hsl(var(--muted)/.3)] border rounded-lg p-4">
                            <div id="answerGrid" class="grid grid-cols-5 md:grid-cols-10 gap-2">
                                <!-- Will be populated by JS -->
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-4 border-t">
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        <a href="{{ route('ljk-answer-keys.show', $ljkAnswerKey) }}"
                            class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </x-ui.card-content>
        </x-ui.card>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const existingAnswers = @json($ljkAnswerKey->kunci_jawaban);
            const jumlahSoalInput = document.getElementById('jumlah_soal');
            const jumlahPilihanSelect = document.getElementById('jumlah_pilihan');
            const answerGrid = document.getElementById('answerGrid');
            const btnRegenerate = document.getElementById('btnRegenerate');

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

                    const existingAnswer = existingAnswers[i - 1] || '';

                    for (let j = 0; j < jumlahPilihan; j++) {
                        const optionBtn = document.createElement('button');
                        optionBtn.type = 'button';
                        optionBtn.className =
                            'w-6 h-6 text-xs border rounded hover:bg-primary hover:text-white transition-colors answer-option';
                        optionBtn.textContent = optionLabels[j];
                        optionBtn.dataset.number = i;
                        optionBtn.dataset.option = optionLabels[j];

                        // Pre-select existing answer
                        if (existingAnswer.toUpperCase() === optionLabels[j]) {
                            optionBtn.classList.add('bg-primary', 'text-white');
                        }

                        optionBtn.addEventListener('click', function() {
                            itemDiv.querySelectorAll('.answer-option').forEach(btn => {
                                btn.classList.remove('bg-primary', 'text-white');
                            });
                            this.classList.add('bg-primary', 'text-white');
                            document.getElementById(`answer_${i}`).value = optionLabels[j];
                        });

                        optionsDiv.appendChild(optionBtn);
                    }

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `kunci_jawaban[${i - 1}]`;
                    hiddenInput.id = `answer_${i}`;
                    hiddenInput.value = existingAnswer;
                    hiddenInput.required = true;
                    itemDiv.appendChild(hiddenInput);

                    itemDiv.appendChild(optionsDiv);
                    answerGrid.appendChild(itemDiv);
                }
            }

            btnRegenerate.addEventListener('click', generateGrid);
            jumlahSoalInput.addEventListener('change', generateGrid);
            jumlahPilihanSelect.addEventListener('change', generateGrid);

            generateGrid();
        });
    </script>
</x-app-layout>
