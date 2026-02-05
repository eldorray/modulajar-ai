<x-app-layout>
    <x-slot name="header">Scan LJK</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Info -->
        <x-ui.card>
            <x-ui.card-header>
                <div class="flex items-center justify-between">
                    <div>
                        <x-ui.card-title>{{ $answerKey->nama }}</x-ui.card-title>
                        <x-ui.card-description>{{ $answerKey->mata_pelajaran }} • {{ $answerKey->jumlah_soal }}
                            soal</x-ui.card-description>
                    </div>
                    <a href="{{ route('ljk.correction.manual', $answerKey) }}" class="btn btn-secondary btn-sm">
                        Input Manual
                    </a>
                </div>
            </x-ui.card-header>
        </x-ui.card>

        <!-- Step Indicator (Simplified: 2 steps only) -->
        <div class="flex items-center justify-center gap-4 text-sm">
            <div id="step1Indicator" class="flex items-center gap-2 text-primary font-medium">
                <span
                    class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-xs">1</span>
                Foto/Upload
            </div>
            <div class="w-8 h-px bg-gray-300"></div>
            <div id="step2Indicator" class="flex items-center gap-2 text-gray-400">
                <span
                    class="w-6 h-6 rounded-full bg-gray-300 text-white flex items-center justify-center text-xs">2</span>
                Koreksi
            </div>
        </div>

        <!-- Step 1: Camera/Upload Section -->
        <x-ui.card id="step1Card">
            <x-ui.card-header>
                <x-ui.card-title>Langkah 1: Foto/Upload LJK</x-ui.card-title>
                <x-ui.card-description>Arahkan kamera ke LJK atau upload foto. Pastikan bagian "JAWABAN" terlihat
                    jelas.</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="space-y-4">
                    <!-- Camera Controls -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <button type="button" id="btnStartCamera" class="btn btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Aktifkan Kamera
                        </button>
                        <button type="button" id="btnCapture" class="btn btn-success hidden">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z">
                                </path>
                            </svg>
                            Capture &amp; Analisis
                        </button>
                        <button type="button" id="btnSwitchCamera" class="btn btn-ghost hidden">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                </path>
                            </svg>
                            Ganti Kamera
                        </button>
                        <label class="btn btn-secondary cursor-pointer">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            Upload Foto
                            <input type="file" id="fileInput" accept="image/*" class="hidden" capture="environment">
                        </label>
                    </div>

                    <!-- Camera Preview -->
                    <div id="cameraContainer" class="relative hidden">
                        <video id="videoPreview"
                            class="w-full max-w-2xl mx-auto rounded-lg border-2 border-dashed border-gray-300" autoplay
                            playsinline></video>
                    </div>

                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" class="hidden text-center py-8">
                        <div class="inline-flex items-center gap-2 text-primary">
                            <svg class="w-6 h-6 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span class="font-medium">Menganalisis jawaban dengan AI...</span>
                        </div>
                        <p class="text-sm text-muted-foreground mt-2">Mohon tunggu, proses ini membutuhkan waktu
                            beberapa detik.</p>
                    </div>

                    <canvas id="captureCanvas" class="hidden"></canvas>
                </div>
            </x-ui.card-content>
        </x-ui.card>

        <!-- Step 2: Answer Input (Previously Step 3) -->
        <x-ui.card id="step2Card" class="hidden">
            <x-ui.card-header>
                <x-ui.card-title>Langkah 2: Periksa & Koreksi Jawaban</x-ui.card-title>
                <x-ui.card-description>Periksa jawaban yang terdeteksi. Klik untuk mengubah jika ada yang
                    salah.</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                <form action="{{ route('ljk.correction.process') }}" method="POST" id="correctionForm">
                    @csrf
                    <input type="hidden" name="ljk_answer_key_id" value="{{ $answerKey->id }}">
                    <input type="hidden" name="scan_image" id="scanImageInput">

                    <!-- Detection Result Message -->
                    <div id="detectionResultMsg"
                        class="mb-4 p-3 rounded-lg bg-blue-50 text-blue-700 border border-blue-200 hidden"></div>

                    <!-- Captured Image Preview -->
                    <div class="mb-4">
                        <img id="capturedImagePreview" class="w-full max-w-md mx-auto rounded-lg border"
                            style="display: none;">
                    </div>

                    <!-- Student Info -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div>
                            <label for="nama_peserta" class="label">Nama Peserta</label>
                            <input type="text" name="nama_peserta" id="nama_peserta" class="input w-full"
                                placeholder="Nama siswa">
                        </div>
                        <div>
                            <label for="nomor_peserta" class="label">Nomor Peserta</label>
                            <input type="text" name="nomor_peserta" id="nomor_peserta" class="input w-full"
                                placeholder="No. peserta">
                        </div>
                        <div>
                            <label for="kelas" class="label">Kelas</label>
                            <input type="text" name="kelas" id="kelas" class="input w-full"
                                value="{{ $answerKey->kelas }}" placeholder="Kelas">
                        </div>
                    </div>

                    <!-- Answer Grid -->
                    <div class="bg-[hsl(var(--muted)/.3)] border rounded-lg p-4 mb-6">
                        <div id="studentAnswerGrid" class="grid grid-cols-5 md:grid-cols-10 gap-2">
                            <!-- Generated by JS -->
                        </div>
                    </div>

                    <!-- Submit -->
                    <div class="flex items-center justify-between">
                        <div>
                            <button type="button" id="btnBackToScan" class="btn btn-secondary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Scan Ulang
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <button type="button" id="btnScanNext" class="btn btn-secondary">
                                Simpan & Scan Berikutnya
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Simpan & Selesai
                            </button>
                        </div>
                    </div>
                </form>
            </x-ui.card-content>
        </x-ui.card>
    </div>

    <script>
        // Elements
        const step1Card = document.getElementById('step1Card');
        const step2Card = document.getElementById('step2Card');
        const step1Indicator = document.getElementById('step1Indicator');
        const step2Indicator = document.getElementById('step2Indicator');

        const videoPreview = document.getElementById('videoPreview');
        const captureCanvas = document.getElementById('captureCanvas');
        const cameraContainer = document.getElementById('cameraContainer');
        const loadingIndicator = document.getElementById('loadingIndicator');

        const fileInput = document.getElementById('fileInput');
        const btnStartCamera = document.getElementById('btnStartCamera');
        const btnCapture = document.getElementById('btnCapture');
        const btnSwitchCamera = document.getElementById('btnSwitchCamera');
        const btnBackToScan = document.getElementById('btnBackToScan');
        const btnScanNext = document.getElementById('btnScanNext');

        const detectionResultMsg = document.getElementById('detectionResultMsg');
        const studentAnswerGrid = document.getElementById('studentAnswerGrid');
        const scanImageInput = document.getElementById('scanImageInput');
        const capturedImagePreview = document.getElementById('capturedImagePreview');
        const correctionForm = document.getElementById('correctionForm');

        // State
        let stream = null;
        let facingMode = 'environment';
        let currentImageData = null;
        const jumlahSoal = {{ $answerKey->jumlah_soal }};
        const jumlahPilihan = {{ count($answerKey->options) }};
        const optionLabels = @json($answerKey->options);

        // ============================================
        // Step Navigation (simplified to 2 steps)
        // ============================================

        function goToStep(step) {
            step1Card.classList.add('hidden');
            step2Card.classList.add('hidden');

            [step1Indicator, step2Indicator].forEach((ind, idx) => {
                const stepNum = idx + 1;
                if (stepNum < step) {
                    ind.classList.remove('text-primary', 'text-gray-400');
                    ind.classList.add('text-green-600');
                    ind.querySelector('span').classList.remove('bg-gray-300', 'bg-primary');
                    ind.querySelector('span').classList.add('bg-green-600');
                } else if (stepNum === step) {
                    ind.classList.remove('text-gray-400', 'text-green-600');
                    ind.classList.add('text-primary');
                    ind.querySelector('span').classList.remove('bg-gray-300', 'bg-green-600');
                    ind.querySelector('span').classList.add('bg-primary');
                } else {
                    ind.classList.remove('text-primary', 'text-green-600');
                    ind.classList.add('text-gray-400');
                    ind.querySelector('span').classList.remove('bg-primary', 'bg-green-600');
                    ind.querySelector('span').classList.add('bg-gray-300');
                }
            });

            if (step === 1) {
                step1Card.classList.remove('hidden');
            } else if (step === 2) {
                step2Card.classList.remove('hidden');
            }
        }

        // ============================================
        // Camera Functions
        // ============================================

        async function startCamera() {
            try {
                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }

                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        facingMode: facingMode,
                        width: {
                            ideal: 1920
                        },
                        height: {
                            ideal: 1080
                        }
                    }
                });

                videoPreview.srcObject = stream;
                cameraContainer.classList.remove('hidden');
                btnStartCamera.classList.add('hidden');
                btnCapture.classList.remove('hidden');
                btnSwitchCamera.classList.remove('hidden');
            } catch (err) {
                console.error('Camera error:', err);
                alert('Tidak dapat mengakses kamera. Gunakan upload foto.');
            }
        }

        async function captureAndAnalyze() {
            const context = captureCanvas.getContext('2d');
            captureCanvas.width = videoPreview.videoWidth;
            captureCanvas.height = videoPreview.videoHeight;
            context.drawImage(videoPreview, 0, 0);

            currentImageData = captureCanvas.toDataURL('image/jpeg', 0.9);
            scanImageInput.value = currentImageData;

            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                cameraContainer.classList.add('hidden');
            }

            // Analyze directly
            await analyzeImage(currentImageData);
        }

        function switchCamera() {
            facingMode = facingMode === 'environment' ? 'user' : 'environment';
            startCamera();
        }

        // File upload handler
        fileInput.addEventListener('change', async function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = async function(event) {
                    currentImageData = event.target.result;
                    scanImageInput.value = currentImageData;

                    if (stream) {
                        stream.getTracks().forEach(track => track.stop());
                        cameraContainer.classList.add('hidden');
                    }

                    // Analyze directly
                    await analyzeImage(currentImageData);
                };
                reader.readAsDataURL(file);
            }
        });

        btnStartCamera.addEventListener('click', startCamera);
        btnCapture.addEventListener('click', captureAndAnalyze);
        btnSwitchCamera.addEventListener('click', switchCamera);

        // ============================================
        // Image Analysis with Groq API
        // ============================================

        async function analyzeImage(imageData) {
            loadingIndicator.classList.remove('hidden');
            btnStartCamera.disabled = true;

            try {
                console.log('Calling Groq API...');
                const response = await fetch('{{ route('ljk.correction.analyze') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                            '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        ljk_answer_key_id: {{ $answerKey->id }},
                        image: imageData,
                    }),
                });

                console.log('Response status:', response.status);

                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('API Response error:', errorText);
                    throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 100)}`);
                }

                const result = await response.json();
                console.log('Groq result:', result);

                // Show image preview
                capturedImagePreview.src = imageData;
                capturedImagePreview.style.display = 'block';

                // Generate answer grid
                generateAnswerGrid();

                if (result.success && result.answers) {
                    const detectedCount = applyDetectedAnswers(result.answers);
                    detectionResultMsg.classList.remove('hidden');
                    detectionResultMsg.className =
                        'mb-4 p-3 rounded-lg bg-green-50 text-green-700 border border-green-200';
                    detectionResultMsg.innerHTML =
                        `<strong>✓ Terdeteksi ${detectedCount} dari ${jumlahSoal} jawaban.</strong> Periksa dan koreksi jika ada yang salah.`;
                } else {
                    detectionResultMsg.classList.remove('hidden');
                    detectionResultMsg.className =
                        'mb-4 p-3 rounded-lg bg-yellow-50 text-yellow-700 border border-yellow-200';
                    detectionResultMsg.innerHTML =
                        `<strong>⚠ ${result.error || 'Tidak dapat mendeteksi jawaban.'}</strong> Silakan input manual.`;
                }

                goToStep(2);

            } catch (error) {
                console.error('Analysis error:', error);

                // Show image preview anyway
                capturedImagePreview.src = imageData;
                capturedImagePreview.style.display = 'block';

                // Generate empty grid
                generateAnswerGrid();

                detectionResultMsg.classList.remove('hidden');
                detectionResultMsg.className = 'mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200';
                detectionResultMsg.innerHTML = `<strong>Error:</strong> ${error.message}. Silakan input manual.`;

                goToStep(2);
            } finally {
                loadingIndicator.classList.add('hidden');
                btnStartCamera.disabled = false;
            }
        }

        // ============================================
        // Answer Grid
        // ============================================

        function generateAnswerGrid() {
            studentAnswerGrid.innerHTML = '';
            for (let i = 1; i <= jumlahSoal; i++) {
                const questionDiv = document.createElement('div');
                questionDiv.className = 'flex flex-col items-center gap-1 p-2 bg-white rounded border';
                questionDiv.innerHTML = `
                        <span class="text-xs font-bold text-gray-600">${i}</span>
                        <select name="jawaban[${i}]" class="answer-select w-12 h-8 text-center text-sm border rounded focus:ring-2 focus:ring-primary">
                            <option value="">-</option>
                            ${optionLabels.map(opt => `<option value="${opt}">${opt}</option>`).join('')}
                        </select>
                    `;
                studentAnswerGrid.appendChild(questionDiv);
            }
        }

        function applyDetectedAnswers(answers) {
            let count = 0;
            const selects = studentAnswerGrid.querySelectorAll('select');

            answers.forEach((answer, index) => {
                if (answer && selects[index]) {
                    const option = Array.from(selects[index].options).find(opt => opt.value === answer);
                    if (option) {
                        selects[index].value = answer;
                        selects[index].classList.add('bg-green-50', 'border-green-300');
                        count++;
                    }
                }
            });

            return count;
        }

        // ============================================
        // Navigation
        // ============================================

        btnBackToScan.addEventListener('click', function() {
            currentImageData = null;
            scanImageInput.value = '';
            capturedImagePreview.style.display = 'none';
            detectionResultMsg.classList.add('hidden');
            btnStartCamera.classList.remove('hidden');
            btnCapture.classList.add('hidden');
            btnSwitchCamera.classList.add('hidden');
            fileInput.value = '';
            goToStep(1);
        });

        btnScanNext.addEventListener('click', function() {
            const formData = new FormData(correctionForm);
            formData.append('scan_next', '1');

            fetch(correctionForm.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                }).then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reset for next scan
                        currentImageData = null;
                        scanImageInput.value = '';
                        capturedImagePreview.style.display = 'none';
                        detectionResultMsg.classList.add('hidden');
                        document.getElementById('nama_peserta').value = '';
                        document.getElementById('nomor_peserta').value = '';
                        btnStartCamera.classList.remove('hidden');
                        btnCapture.classList.add('hidden');
                        btnSwitchCamera.classList.add('hidden');
                        fileInput.value = '';
                        goToStep(1);

                        // Show success message briefly
                        alert('Data tersimpan! Silakan scan LJK berikutnya.');
                    } else {
                        alert(data.message || 'Gagal menyimpan data.');
                    }
                }).catch(err => {
                    console.error(err);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                });
        });

        // Initialize
        generateAnswerGrid();
    </script>
</x-app-layout>
