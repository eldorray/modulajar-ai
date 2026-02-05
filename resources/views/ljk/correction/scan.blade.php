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

        <!-- Camera Section -->
        <x-ui.card id="cameraCard">
            <x-ui.card-header>
                <x-ui.card-title>Scan Lembar Jawaban</x-ui.card-title>
                <x-ui.card-description>Arahkan kamera ke LJK yang sudah diisi. Pastikan pencahayaan cukup dan LJK
                    terlihat jelas.</x-ui.card-description>
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
                            Capture
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
                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                            <div class="border-2 border-primary/50 rounded-lg w-4/5 h-4/5"></div>
                        </div>
                    </div>

                    <!-- Captured Image -->
                    <div id="capturedContainer" class="hidden">
                        <img id="capturedImage" class="w-full max-w-2xl mx-auto rounded-lg border-2 border-primary">
                        <div class="flex items-center justify-center gap-2 mt-4">
                            <button type="button" id="btnRetake" class="btn btn-secondary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                    </path>
                                </svg>
                                Ambil Ulang
                            </button>
                            <button type="button" id="btnProceed" class="btn btn-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7"></path>
                                </svg>
                                Lanjut Input Jawaban
                            </button>
                        </div>
                    </div>

                    <canvas id="captureCanvas" class="hidden"></canvas>
                </div>
            </x-ui.card-content>
        </x-ui.card>

        <!-- Manual Answer Input (shown after capture or directly) -->
        <x-ui.card id="answerInputCard" class="hidden">
            <x-ui.card-header>
                <x-ui.card-title>Input Jawaban Siswa</x-ui.card-title>
                <x-ui.card-description>Klik pada pilihan jawaban sesuai dengan yang diisi siswa pada
                    LJK.</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                <form action="{{ route('ljk.correction.process') }}" method="POST" id="correctionForm">
                    @csrf
                    <input type="hidden" name="ljk_answer_key_id" value="{{ $answerKey->id }}">
                    <input type="hidden" name="scan_image" id="scanImageInput">

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
                        <p class="text-sm text-[hsl(var(--muted-foreground))]" id="progressText">0 dari
                            {{ $answerKey->jumlah_soal }} jawaban diisi</p>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Koreksi Jawaban
                        </button>
                    </div>
                </form>
            </x-ui.card-content>
        </x-ui.card>

        <a href="{{ route('ljk.correction.index') }}" class="btn btn-ghost">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const jumlahSoal = {{ $answerKey->jumlah_soal }};
            const jumlahPilihan = {{ $answerKey->jumlah_pilihan }};
            const optionLabels = ['A', 'B', 'C', 'D', 'E'].slice(0, jumlahPilihan);

            // Elements
            const btnStartCamera = document.getElementById('btnStartCamera');
            const btnCapture = document.getElementById('btnCapture');
            const btnSwitchCamera = document.getElementById('btnSwitchCamera');
            const btnRetake = document.getElementById('btnRetake');
            const btnProceed = document.getElementById('btnProceed');
            const videoPreview = document.getElementById('videoPreview');
            const cameraContainer = document.getElementById('cameraContainer');
            const capturedContainer = document.getElementById('capturedContainer');
            const capturedImage = document.getElementById('capturedImage');
            const captureCanvas = document.getElementById('captureCanvas');
            const fileInput = document.getElementById('fileInput');
            const answerInputCard = document.getElementById('answerInputCard');
            const studentAnswerGrid = document.getElementById('studentAnswerGrid');
            const scanImageInput = document.getElementById('scanImageInput');
            const progressText = document.getElementById('progressText');

            let stream = null;
            let facingMode = 'environment';
            let filledCount = 0;
            let isAnalyzing = false;

            // ============================================
            // OMR (Optical Mark Recognition) Functions
            // ============================================

            /**
             * Analyze the LJK image and detect filled bubbles
             */
            async function analyzeImage(imageData) {
                return new Promise((resolve) => {
                    const img = new Image();
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        canvas.width = img.width;
                        canvas.height = img.height;
                        ctx.drawImage(img, 0, 0);

                        // Get image data
                        const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const pixels = imgData.data;

                        // Convert to grayscale and calculate average brightness
                        const grayscale = [];
                        for (let i = 0; i < pixels.length; i += 4) {
                            const gray = (pixels[i] * 0.299 + pixels[i + 1] * 0.587 + pixels[i +
                                2] * 0.114);
                            grayscale.push(gray);
                        }

                        // Detect answers using grid-based analysis
                        const detectedAnswers = detectAnswersFromGrid(grayscale, canvas.width,
                            canvas.height);
                        resolve(detectedAnswers);
                    };
                    img.src = imageData;
                });
            }

            /**
             * Detect answers by analyzing the grid structure
             * This assumes the LJK has a standard format with answer bubbles arranged in columns
             */
            function detectAnswersFromGrid(grayscale, width, height) {
                const answers = [];

                // LJK Configuration - Based on standard LJK format
                // The answer section is typically in the bottom portion of the LJK
                const gridConfig = {
                    // Approximate answer grid position (as percentage of image)
                    startY: 0.55, // Start at 55% from top
                    endY: 0.95, // End at 95% from top
                    startX: 0.05, // Start at 5% from left
                    endX: 0.95, // End at 95% from right
                    columns: 5, // 5 columns of answers
                    bubbleWidth: 0.015, // Bubble width as percentage
                    bubbleHeight: 0.02, // Bubble height as percentage
                };

                const gridStartY = Math.floor(height * gridConfig.startY);
                const gridEndY = Math.floor(height * gridConfig.endY);
                const gridStartX = Math.floor(width * gridConfig.startX);
                const gridEndX = Math.floor(width * gridConfig.endX);

                const gridHeight = gridEndY - gridStartY;
                const gridWidth = gridEndX - gridStartX;

                const questionsPerColumn = Math.ceil(jumlahSoal / gridConfig.columns);
                const columnWidth = gridWidth / gridConfig.columns;
                const rowHeight = gridHeight / questionsPerColumn;

                // Analyze each question
                for (let q = 0; q < jumlahSoal; q++) {
                    const col = Math.floor(q / questionsPerColumn);
                    const row = q % questionsPerColumn;

                    const baseX = gridStartX + (col * columnWidth);
                    const baseY = gridStartY + (row * rowHeight);

                    // Check each option (A, B, C, D, E)
                    let lowestIntensity = 255;
                    let selectedOption = null;
                    const threshold = 150; // Threshold for considering a bubble as filled

                    const bubbleStartX = baseX + (columnWidth * 0.15); // Skip question number
                    const bubbleSpacing = (columnWidth * 0.7) / jumlahPilihan;

                    for (let opt = 0; opt < jumlahPilihan; opt++) {
                        const bubbleX = Math.floor(bubbleStartX + (opt * bubbleSpacing) + (bubbleSpacing * 0.3));
                        const bubbleY = Math.floor(baseY + (rowHeight * 0.3));
                        const bubbleW = Math.max(10, Math.floor(bubbleSpacing * 0.4));
                        const bubbleH = Math.max(10, Math.floor(rowHeight * 0.4));

                        // Calculate average intensity in bubble area
                        let totalIntensity = 0;
                        let pixelCount = 0;

                        for (let y = bubbleY; y < bubbleY + bubbleH && y < height; y++) {
                            for (let x = bubbleX; x < bubbleX + bubbleW && x < width; x++) {
                                const idx = y * width + x;
                                if (idx < grayscale.length) {
                                    totalIntensity += grayscale[idx];
                                    pixelCount++;
                                }
                            }
                        }

                        const avgIntensity = pixelCount > 0 ? totalIntensity / pixelCount : 255;

                        // Check if this bubble is filled (darker than threshold)
                        if (avgIntensity < threshold && avgIntensity < lowestIntensity) {
                            lowestIntensity = avgIntensity;
                            selectedOption = optionLabels[opt];
                        }
                    }

                    answers.push(selectedOption);
                }

                return answers;
            }

            /**
             * Apply detected answers to the answer grid UI
             */
            function applyDetectedAnswers(answers) {
                // Reset all selections first
                document.querySelectorAll('.answer-option.bg-primary').forEach(btn => {
                    btn.classList.remove('bg-primary', 'text-white');
                });

                let detectedCount = 0;

                for (let i = 0; i < answers.length && i < jumlahSoal; i++) {
                    const answer = answers[i];
                    if (answer) {
                        const questionNum = i + 1;
                        const input = document.getElementById(`student_answer_${questionNum}`);
                        if (input) {
                            input.value = answer;
                            input.dataset.filled = 'true';

                            // Find and select the button
                            const btn = document.querySelector(
                                `.answer-option[data-number="${questionNum}"][data-option="${answer}"]`
                            );
                            if (btn) {
                                btn.classList.add('bg-primary', 'text-white');
                                detectedCount++;
                            }
                        }
                    }
                }

                updateProgress();
                return detectedCount;
            }

            /**
             * Show loading state during analysis
             */
            function showAnalyzing(show) {
                isAnalyzing = show;
                if (show) {
                    btnProceed.innerHTML = `
                        <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Menganalisis LJK...
                    `;
                    btnProceed.disabled = true;
                } else {
                    btnProceed.innerHTML = `
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        Lanjut Input Jawaban
                    `;
                    btnProceed.disabled = false;
                }
            }

            /**
             * Process image and detect answers automatically
             */
            async function processImageWithOMR(imageData) {
                showAnalyzing(true);

                try {
                    // Small delay to let UI update
                    await new Promise(r => setTimeout(r, 100));

                    // Analyze image
                    const detectedAnswers = await analyzeImage(imageData);

                    // Show input card
                    answerInputCard.classList.remove('hidden');

                    // Apply detected answers
                    const detectedCount = applyDetectedAnswers(detectedAnswers);

                    // Scroll to input card
                    answerInputCard.scrollIntoView({
                        behavior: 'smooth'
                    });

                    // Show detection result message
                    const resultMsg = document.createElement('div');
                    resultMsg.className = 'mb-4 p-3 rounded-lg ' + (detectedCount > 0 ?
                        'bg-green-50 text-green-700 border border-green-200' :
                        'bg-yellow-50 text-yellow-700 border border-yellow-200');
                    resultMsg.innerHTML = detectedCount > 0 ?
                        `<strong>✓ Terdeteksi ${detectedCount} jawaban.</strong> Silakan periksa dan koreksi jika ada yang salah.` :
                        `<strong>⚠ Tidak dapat mendeteksi jawaban otomatis.</strong> Silakan input manual.`;

                    const existingMsg = answerInputCard.querySelector('.detection-result-msg');
                    if (existingMsg) existingMsg.remove();
                    resultMsg.classList.add('detection-result-msg');
                    answerInputCard.querySelector('[class*="card-content"]').prepend(resultMsg);

                } catch (error) {
                    console.error('OMR Error:', error);
                    answerInputCard.classList.remove('hidden');
                    answerInputCard.scrollIntoView({
                        behavior: 'smooth'
                    });
                } finally {
                    showAnalyzing(false);
                }
            }

            // Generate student answer grid
            function generateAnswerGrid() {
                studentAnswerGrid.innerHTML = '';
                filledCount = 0;

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
                            const wasSelected = this.classList.contains('bg-primary');

                            // Deselect all in this item
                            itemDiv.querySelectorAll('.answer-option').forEach(btn => {
                                btn.classList.remove('bg-primary', 'text-white');
                            });

                            const input = document.getElementById(`student_answer_${i}`);

                            if (wasSelected) {
                                // Deselect
                                input.value = '';
                                filledCount--;
                            } else {
                                // Select
                                this.classList.add('bg-primary', 'text-white');
                                input.value = optionLabels[j];
                                if (!wasSelected && input.dataset.filled !== 'true') {
                                    filledCount++;
                                    input.dataset.filled = 'true';
                                }
                            }

                            updateProgress();
                        });

                        optionsDiv.appendChild(optionBtn);
                    }

                    // Hidden input
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `jawaban_siswa[${i - 1}]`;
                    hiddenInput.id = `student_answer_${i}`;
                    itemDiv.appendChild(hiddenInput);

                    itemDiv.appendChild(optionsDiv);
                    studentAnswerGrid.appendChild(itemDiv);
                }
            }

            function updateProgress() {
                const filled = document.querySelectorAll('.answer-option.bg-primary').length;
                progressText.textContent = `${filled} dari ${jumlahSoal} jawaban diisi`;
            }

            // Camera functions
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
                    capturedContainer.classList.add('hidden');
                    btnStartCamera.classList.add('hidden');
                    btnCapture.classList.remove('hidden');
                    btnSwitchCamera.classList.remove('hidden');
                } catch (err) {
                    console.error('Camera error:', err);
                    alert(
                        'Tidak dapat mengakses kamera. Pastikan browser memiliki izin kamera atau gunakan upload foto.'
                        );
                }
            }

            function captureImage() {
                const context = captureCanvas.getContext('2d');
                captureCanvas.width = videoPreview.videoWidth;
                captureCanvas.height = videoPreview.videoHeight;
                context.drawImage(videoPreview, 0, 0);

                const imageData = captureCanvas.toDataURL('image/jpeg', 0.8);
                capturedImage.src = imageData;
                scanImageInput.value = imageData;

                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }

                cameraContainer.classList.add('hidden');
                capturedContainer.classList.remove('hidden');
                btnCapture.classList.add('hidden');
                btnSwitchCamera.classList.add('hidden');

                // Auto-analyze the captured image
                processImageWithOMR(imageData);
            }

            function switchCamera() {
                facingMode = facingMode === 'environment' ? 'user' : 'environment';
                startCamera();
            }

            function retakePhoto() {
                capturedContainer.classList.add('hidden');
                answerInputCard.classList.add('hidden');
                scanImageInput.value = '';
                btnStartCamera.classList.remove('hidden');

                // Reset answers
                document.querySelectorAll('.answer-option.bg-primary').forEach(btn => {
                    btn.classList.remove('bg-primary', 'text-white');
                });
                document.querySelectorAll('[id^="student_answer_"]').forEach(input => {
                    input.value = '';
                    input.dataset.filled = '';
                });
                updateProgress();

                // Remove detection message
                const existingMsg = document.querySelector('.detection-result-msg');
                if (existingMsg) existingMsg.remove();
            }

            function proceedToInput() {
                if (!isAnalyzing) {
                    answerInputCard.classList.remove('hidden');
                    answerInputCard.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }

            // File upload - with auto OMR
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        capturedImage.src = event.target.result;
                        scanImageInput.value = event.target.result;
                        cameraContainer.classList.add('hidden');
                        capturedContainer.classList.remove('hidden');
                        btnStartCamera.classList.add('hidden');
                        btnCapture.classList.add('hidden');
                        btnSwitchCamera.classList.add('hidden');

                        if (stream) {
                            stream.getTracks().forEach(track => track.stop());
                        }

                        // Auto-analyze the uploaded image
                        processImageWithOMR(event.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Event listeners
            btnStartCamera.addEventListener('click', startCamera);
            btnCapture.addEventListener('click', captureImage);
            btnSwitchCamera.addEventListener('click', switchCamera);
            btnRetake.addEventListener('click', retakePhoto);
            btnProceed.addEventListener('click', proceedToInput);

            // Generate grid on load
            generateAnswerGrid();
        });
    </script>
</x-app-layout>
