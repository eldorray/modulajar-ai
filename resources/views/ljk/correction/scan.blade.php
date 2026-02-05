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

        <!-- Step Indicator -->
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
                Pilih Area Jawaban
            </div>
            <div class="w-8 h-px bg-gray-300"></div>
            <div id="step3Indicator" class="flex items-center gap-2 text-gray-400">
                <span
                    class="w-6 h-6 rounded-full bg-gray-300 text-white flex items-center justify-center text-xs">3</span>
                Koreksi
            </div>
        </div>

        <!-- Step 1: Camera/Upload Section -->
        <x-ui.card id="step1Card">
            <x-ui.card-header>
                <x-ui.card-title>Langkah 1: Foto/Upload LJK</x-ui.card-title>
                <x-ui.card-description>Arahkan kamera ke LJK atau upload foto. Pastikan pencahayaan
                    cukup.</x-ui.card-description>
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
                    </div>

                    <canvas id="captureCanvas" class="hidden"></canvas>
                </div>
            </x-ui.card-content>
        </x-ui.card>

        <!-- Step 2: Crop/Select Area -->
        <x-ui.card id="step2Card" class="hidden">
            <x-ui.card-header>
                <x-ui.card-title>Langkah 2: Pilih Area Jawaban</x-ui.card-title>
                <x-ui.card-description>Geser kotak untuk memilih area grid jawaban (bagian "JAWABAN"). Pastikan semua
                    nomor 1-{{ $answerKey->jumlah_soal }} termasuk dalam area.</x-ui.card-description>
            </x-ui.card-header>
            <x-ui.card-content>
                <div class="space-y-4">
                    <!-- Crop Container -->
                    <div id="cropContainer"
                        class="relative w-full max-w-2xl mx-auto overflow-hidden rounded-lg border-2 border-gray-300">
                        <img id="cropImage" class="w-full" style="display: block;">
                        <!-- Selection Box -->
                        <div id="selectionBox" class="absolute border-2 border-primary bg-primary/10 cursor-move"
                            style="left: 10%; top: 50%; width: 80%; height: 40%;">
                            <!-- Resize Handles -->
                            <div class="absolute w-4 h-4 bg-primary rounded-full -top-2 -left-2 cursor-nw-resize"
                                data-handle="tl"></div>
                            <div class="absolute w-4 h-4 bg-primary rounded-full -top-2 -right-2 cursor-ne-resize"
                                data-handle="tr"></div>
                            <div class="absolute w-4 h-4 bg-primary rounded-full -bottom-2 -left-2 cursor-sw-resize"
                                data-handle="bl"></div>
                            <div class="absolute w-4 h-4 bg-primary rounded-full -bottom-2 -right-2 cursor-se-resize"
                                data-handle="br"></div>
                            <!-- Label -->
                            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <span class="bg-primary text-white px-2 py-1 rounded text-xs font-medium">Area
                                    Jawaban</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-center gap-2">
                        <button type="button" id="btnBackToStep1" class="btn btn-secondary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Ambil Ulang
                        </button>
                        <button type="button" id="btnAnalyze" class="btn btn-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7"></path>
                            </svg>
                            Analisis Jawaban
                        </button>
                    </div>
                </div>
            </x-ui.card-content>
        </x-ui.card>

        <!-- Step 3: Answer Input -->
        <x-ui.card id="step3Card" class="hidden">
            <x-ui.card-header>
                <x-ui.card-title>Langkah 3: Periksa & Koreksi Jawaban</x-ui.card-title>
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
                            <button type="button" id="btnBackToStep2" class="btn btn-ghost">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Pilih Ulang Area
                            </button>
                            <span class="text-sm text-[hsl(var(--muted-foreground))] ml-4" id="progressText">0 dari
                                {{ $answerKey->jumlah_soal }} jawaban diisi</span>
                        </div>
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

            // Elements - Step 1
            const step1Card = document.getElementById('step1Card');
            const btnStartCamera = document.getElementById('btnStartCamera');
            const btnCapture = document.getElementById('btnCapture');
            const btnSwitchCamera = document.getElementById('btnSwitchCamera');
            const videoPreview = document.getElementById('videoPreview');
            const cameraContainer = document.getElementById('cameraContainer');
            const captureCanvas = document.getElementById('captureCanvas');
            const fileInput = document.getElementById('fileInput');

            // Elements - Step 2
            const step2Card = document.getElementById('step2Card');
            const cropContainer = document.getElementById('cropContainer');
            const cropImage = document.getElementById('cropImage');
            const selectionBox = document.getElementById('selectionBox');
            const btnBackToStep1 = document.getElementById('btnBackToStep1');
            const btnAnalyze = document.getElementById('btnAnalyze');

            // Elements - Step 3
            const step3Card = document.getElementById('step3Card');
            const studentAnswerGrid = document.getElementById('studentAnswerGrid');
            const scanImageInput = document.getElementById('scanImageInput');
            const progressText = document.getElementById('progressText');
            const detectionResultMsg = document.getElementById('detectionResultMsg');
            const btnBackToStep2 = document.getElementById('btnBackToStep2');

            // Step Indicators
            const step1Indicator = document.getElementById('step1Indicator');
            const step2Indicator = document.getElementById('step2Indicator');
            const step3Indicator = document.getElementById('step3Indicator');

            let stream = null;
            let facingMode = 'environment';
            let currentImageData = null;

            // Selection box state
            let isDragging = false;
            let isResizing = false;
            let activeHandle = null;
            let startX, startY, startLeft, startTop, startWidth, startHeight;

            // ============================================
            // Step Navigation
            // ============================================

            function goToStep(step) {
                // Hide all
                step1Card.classList.add('hidden');
                step2Card.classList.add('hidden');
                step3Card.classList.add('hidden');

                // Reset indicators
                [step1Indicator, step2Indicator, step3Indicator].forEach((ind, i) => {
                    const num = i + 1;
                    if (num < step) {
                        ind.classList.remove('text-gray-400', 'text-primary');
                        ind.classList.add('text-green-600');
                        ind.querySelector('span').classList.remove('bg-gray-300', 'bg-primary');
                        ind.querySelector('span').classList.add('bg-green-600');
                    } else if (num === step) {
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

                // Show current step
                if (step === 1) {
                    step1Card.classList.remove('hidden');
                } else if (step === 2) {
                    step2Card.classList.remove('hidden');
                } else if (step === 3) {
                    step3Card.classList.remove('hidden');
                }
            }

            // ============================================
            // Step 1: Camera/Upload
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

            function captureImage() {
                const context = captureCanvas.getContext('2d');
                captureCanvas.width = videoPreview.videoWidth;
                captureCanvas.height = videoPreview.videoHeight;
                context.drawImage(videoPreview, 0, 0);

                currentImageData = captureCanvas.toDataURL('image/jpeg', 0.9);
                scanImageInput.value = currentImageData;

                if (stream) {
                    stream.getTracks().forEach(track => track.stop());
                }

                // Go to step 2
                cropImage.src = currentImageData;
                cropImage.onload = function() {
                    initSelectionBox();
                    goToStep(2);
                };
            }

            function switchCamera() {
                facingMode = facingMode === 'environment' ? 'user' : 'environment';
                startCamera();
            }

            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        currentImageData = event.target.result;
                        scanImageInput.value = currentImageData;

                        if (stream) {
                            stream.getTracks().forEach(track => track.stop());
                        }

                        // Go to step 2
                        cropImage.src = currentImageData;
                        cropImage.onload = function() {
                            initSelectionBox();
                            goToStep(2);
                        };
                    };
                    reader.readAsDataURL(file);
                }
            });

            btnStartCamera.addEventListener('click', startCamera);
            btnCapture.addEventListener('click', captureImage);
            btnSwitchCamera.addEventListener('click', switchCamera);

            // ============================================
            // Step 2: Crop/Select Area
            // ============================================

            function initSelectionBox() {
                // Set initial position (bottom 45% of image for answer area)
                const containerRect = cropContainer.getBoundingClientRect();
                selectionBox.style.left = '5%';
                selectionBox.style.top = '50%';
                selectionBox.style.width = '90%';
                selectionBox.style.height = '40%';
            }

            // Mouse/Touch events for selection box
            selectionBox.addEventListener('mousedown', startDrag);
            selectionBox.addEventListener('touchstart', startDrag, {
                passive: false
            });

            document.querySelectorAll('[data-handle]').forEach(handle => {
                handle.addEventListener('mousedown', startResize);
                handle.addEventListener('touchstart', startResize, {
                    passive: false
                });
            });

            document.addEventListener('mousemove', onDrag);
            document.addEventListener('touchmove', onDrag, {
                passive: false
            });
            document.addEventListener('mouseup', stopDrag);
            document.addEventListener('touchend', stopDrag);

            function getEventCoords(e) {
                if (e.touches && e.touches.length > 0) {
                    return {
                        x: e.touches[0].clientX,
                        y: e.touches[0].clientY
                    };
                }
                return {
                    x: e.clientX,
                    y: e.clientY
                };
            }

            function startDrag(e) {
                if (e.target.hasAttribute('data-handle')) return;
                e.preventDefault();
                isDragging = true;
                const coords = getEventCoords(e);
                startX = coords.x;
                startY = coords.y;
                startLeft = parseFloat(selectionBox.style.left) || 0;
                startTop = parseFloat(selectionBox.style.top) || 0;
            }

            function startResize(e) {
                e.preventDefault();
                e.stopPropagation();
                isResizing = true;
                activeHandle = e.target.dataset.handle;
                const coords = getEventCoords(e);
                startX = coords.x;
                startY = coords.y;
                startLeft = parseFloat(selectionBox.style.left) || 0;
                startTop = parseFloat(selectionBox.style.top) || 0;
                startWidth = parseFloat(selectionBox.style.width) || 0;
                startHeight = parseFloat(selectionBox.style.height) || 0;
            }

            function onDrag(e) {
                if (!isDragging && !isResizing) return;
                e.preventDefault();

                const containerRect = cropContainer.getBoundingClientRect();
                const coords = getEventCoords(e);
                const dx = ((coords.x - startX) / containerRect.width) * 100;
                const dy = ((coords.y - startY) / containerRect.height) * 100;

                if (isDragging) {
                    let newLeft = startLeft + dx;
                    let newTop = startTop + dy;
                    const boxWidth = parseFloat(selectionBox.style.width) || 80;
                    const boxHeight = parseFloat(selectionBox.style.height) || 40;

                    // Constrain
                    newLeft = Math.max(0, Math.min(100 - boxWidth, newLeft));
                    newTop = Math.max(0, Math.min(100 - boxHeight, newTop));

                    selectionBox.style.left = newLeft + '%';
                    selectionBox.style.top = newTop + '%';
                }

                if (isResizing && activeHandle) {
                    let newLeft = startLeft;
                    let newTop = startTop;
                    let newWidth = startWidth;
                    let newHeight = startHeight;

                    if (activeHandle.includes('r')) {
                        newWidth = Math.max(20, Math.min(100 - startLeft, startWidth + dx));
                    }
                    if (activeHandle.includes('l')) {
                        const potentialWidth = startWidth - dx;
                        if (potentialWidth >= 20 && startLeft + dx >= 0) {
                            newLeft = startLeft + dx;
                            newWidth = potentialWidth;
                        }
                    }
                    if (activeHandle.includes('b')) {
                        newHeight = Math.max(10, Math.min(100 - startTop, startHeight + dy));
                    }
                    if (activeHandle.includes('t')) {
                        const potentialHeight = startHeight - dy;
                        if (potentialHeight >= 10 && startTop + dy >= 0) {
                            newTop = startTop + dy;
                            newHeight = potentialHeight;
                        }
                    }

                    selectionBox.style.left = newLeft + '%';
                    selectionBox.style.top = newTop + '%';
                    selectionBox.style.width = newWidth + '%';
                    selectionBox.style.height = newHeight + '%';
                }
            }

            function stopDrag() {
                isDragging = false;
                isResizing = false;
                activeHandle = null;
            }

            btnBackToStep1.addEventListener('click', function() {
                cameraContainer.classList.add('hidden');
                btnStartCamera.classList.remove('hidden');
                btnCapture.classList.add('hidden');
                btnSwitchCamera.classList.add('hidden');
                goToStep(1);
            });

            btnAnalyze.addEventListener('click', function() {
                analyzeSelectedArea();
            });

            // ============================================
            // OMR Analysis (Improved with crop)
            // ============================================

            async function analyzeSelectedArea() {
                btnAnalyze.innerHTML =
                    '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Menganalisis...';
                btnAnalyze.disabled = true;

                await new Promise(r => setTimeout(r, 100));

                try {
                    // Get selection coordinates
                    const left = parseFloat(selectionBox.style.left) / 100;
                    const top = parseFloat(selectionBox.style.top) / 100;
                    const width = parseFloat(selectionBox.style.width) / 100;
                    const height = parseFloat(selectionBox.style.height) / 100;

                    // Crop and analyze
                    const detectedAnswers = await analyzeImageWithCrop(currentImageData, left, top, width,
                        height);

                    // Generate grid and apply answers
                    generateAnswerGrid();
                    const detectedCount = applyDetectedAnswers(detectedAnswers);

                    // Show result
                    detectionResultMsg.classList.remove('hidden');
                    if (detectedCount > 0) {
                        detectionResultMsg.className =
                            'mb-4 p-3 rounded-lg bg-green-50 text-green-700 border border-green-200';
                        detectionResultMsg.innerHTML =
                            `<strong>✓ Terdeteksi ${detectedCount} dari ${jumlahSoal} jawaban.</strong> Periksa dan koreksi jika ada yang salah.`;
                    } else {
                        detectionResultMsg.className =
                            'mb-4 p-3 rounded-lg bg-yellow-50 text-yellow-700 border border-yellow-200';
                        detectionResultMsg.innerHTML =
                            `<strong>⚠ Tidak dapat mendeteksi jawaban.</strong> Silakan input manual atau coba pilih ulang area.`;
                    }

                    goToStep(3);
                } catch (error) {
                    console.error('Analysis error:', error);
                    generateAnswerGrid();
                    detectionResultMsg.className =
                        'mb-4 p-3 rounded-lg bg-red-50 text-red-700 border border-red-200';
                    detectionResultMsg.innerHTML =
                        `<strong>Error:</strong> ${error.message}. Silakan input manual.`;
                    detectionResultMsg.classList.remove('hidden');
                    goToStep(3);
                } finally {
                    btnAnalyze.innerHTML =
                        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg> Analisis Jawaban';
                    btnAnalyze.disabled = false;
                }
            }

            async function analyzeImageWithCrop(imageData, cropLeft, cropTop, cropWidth, cropHeight) {
                // Call Groq API for accurate image analysis
                try {
                    const response = await fetch('{{ route('ljk.correction.analyze') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            ljk_answer_key_id: {{ $answerKey->id }},
                            image: imageData,
                        }),
                    });

                    const result = await response.json();

                    if (!result.success) {
                        throw new Error(result.error || 'Gagal menganalisis gambar');
                    }

                    // Return answers array from Groq
                    return result.answers || [];
                } catch (error) {
                    console.error('Groq API error:', error);
                    // Fallback to client-side detection
                    return fallbackClientSideDetection(imageData, cropLeft, cropTop, cropWidth, cropHeight);
                }
            }

            // Fallback client-side detection if API fails
            async function fallbackClientSideDetection(imageData, cropLeft, cropTop, cropWidth, cropHeight) {
                return new Promise((resolve) => {
                    const img = new Image();
                    img.onload = function() {
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');

                        const sx = Math.floor(img.width * cropLeft);
                        const sy = Math.floor(img.height * cropTop);
                        const sw = Math.floor(img.width * cropWidth);
                        const sh = Math.floor(img.height * cropHeight);

                        canvas.width = sw;
                        canvas.height = sh;
                        ctx.drawImage(img, sx, sy, sw, sh, 0, 0, sw, sh);

                        const imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const pixels = imgData.data;

                        const grayscale = [];
                        for (let i = 0; i < pixels.length; i += 4) {
                            const gray = (pixels[i] * 0.299 + pixels[i + 1] * 0.587 + pixels[i +
                                2] * 0.114);
                            grayscale.push(gray);
                        }

                        const sortedGray = [...grayscale].sort((a, b) => a - b);
                        const medianGray = sortedGray[Math.floor(sortedGray.length / 2)];
                        const threshold = Math.min(medianGray * 0.7, 120);

                        const answers = [];
                        const columns = 5;
                        const rowsPerColumn = Math.ceil(jumlahSoal / columns);
                        const columnWidth = canvas.width / columns;
                        const rowHeight = canvas.height / rowsPerColumn;

                        for (let q = 0; q < jumlahSoal; q++) {
                            const col = Math.floor(q / rowsPerColumn);
                            const row = q % rowsPerColumn;
                            const cellX = col * columnWidth;
                            const cellY = row * rowHeight;
                            const bubbleStartX = cellX + (columnWidth * 0.18);
                            const bubbleSpacing = (columnWidth * 0.75) / jumlahPilihan;

                            let darkestOption = null;
                            let darkestValue = 255;

                            for (let opt = 0; opt < jumlahPilihan; opt++) {
                                const bubbleX = Math.floor(bubbleStartX + (opt * bubbleSpacing) + (
                                    bubbleSpacing * 0.2));
                                const bubbleY = Math.floor(cellY + (rowHeight * 0.25));
                                const bubbleW = Math.max(8, Math.floor(bubbleSpacing * 0.6));
                                const bubbleH = Math.max(8, Math.floor(rowHeight * 0.5));

                                let totalIntensity = 0;
                                let pixelCount = 0;

                                for (let y = bubbleY; y < bubbleY + bubbleH && y < canvas
                                    .height; y++) {
                                    for (let x = bubbleX; x < bubbleX + bubbleW && x < canvas
                                        .width; x++) {
                                        const idx = y * canvas.width + x;
                                        if (idx >= 0 && idx < grayscale.length) {
                                            totalIntensity += grayscale[idx];
                                            pixelCount++;
                                        }
                                    }
                                }

                                const avgIntensity = pixelCount > 0 ? totalIntensity / pixelCount :
                                    255;
                                if (avgIntensity < threshold && avgIntensity < darkestValue) {
                                    darkestValue = avgIntensity;
                                    darkestOption = optionLabels[opt];
                                }
                            }
                            answers.push(darkestOption);
                        }
                        resolve(answers);
                    };
                    img.src = imageData;
                });
            }

            // ============================================
            // Step 3: Answer Grid
            // ============================================

            function generateAnswerGrid() {
                studentAnswerGrid.innerHTML = '';

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
                            // Deselect all in this item
                            itemDiv.querySelectorAll('.answer-option').forEach(btn => {
                                btn.classList.remove('bg-primary', 'text-white');
                            });

                            const input = document.getElementById(`student_answer_${i}`);

                            if (this.classList.contains('bg-primary')) {
                                input.value = '';
                            } else {
                                this.classList.add('bg-primary', 'text-white');
                                input.value = optionLabels[j];
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

            function applyDetectedAnswers(answers) {
                let detectedCount = 0;

                for (let i = 0; i < answers.length && i < jumlahSoal; i++) {
                    const answer = answers[i];
                    if (answer) {
                        const questionNum = i + 1;
                        const input = document.getElementById(`student_answer_${questionNum}`);
                        if (input) {
                            input.value = answer;

                            const btn = document.querySelector(
                                `.answer-option[data-number="${questionNum}"][data-option="${answer}"]`);
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

            function updateProgress() {
                const filled = document.querySelectorAll('.answer-option.bg-primary').length;
                progressText.textContent = `${filled} dari ${jumlahSoal} jawaban diisi`;
            }

            btnBackToStep2.addEventListener('click', function() {
                goToStep(2);
            });

            // Initialize
            goToStep(1);
        });
    </script>
</x-app-layout>
