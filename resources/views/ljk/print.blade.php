<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LJK - {{ $template->nama_template }}</title>
    <style>
        @page {
            margin: 10mm;
            size: A4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.3;
            color: #000;
            background: #fff;
        }

        .container {
            max-width: 100%;
            padding: 0;
        }

        /* Kop Surat */
        .kop-surat {
            margin-bottom: 10px;
        }

        .kop-surat img {
            width: 100%;
            height: auto;
            max-height: 100px;
            object-fit: contain;
        }

        /* Title Section */
        .title-section {
            text-align: center;
            border: 2px solid #000;
            padding: 8px;
            margin-bottom: 8px;
            background: #e8f5e9;
        }

        .title-section h1 {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 3px;
        }

        .title-section h2 {
            font-size: 11pt;
            font-weight: normal;
        }

        .title-section h3 {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 3px;
        }

        /* Main Content Layout */
        .main-content {
            display: flex;
            gap: 10px;
        }

        .left-panel {
            flex: 2.5;
        }

        .right-panel {
            flex: 1;
            font-size: 9pt;
        }

        /* Instructions Box */
        .instructions-box {
            border: 1px solid #000;
            padding: 6px;
            margin-bottom: 8px;
        }

        .instructions-box h4 {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 3px;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }

        .instructions-box ol {
            font-size: 8pt;
            margin-left: 15px;
            line-height: 1.4;
        }

        /* Example Box */
        .example-box {
            border: 1px solid #000;
            padding: 6px;
            margin-bottom: 8px;
        }

        .example-box h4 {
            font-size: 9pt;
            text-align: center;
            margin-bottom: 5px;
        }

        .example-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 8pt;
        }

        .bubble-example {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        /* Bubble styles */
        .bubble {
            width: 14px;
            height: 14px;
            border: 1px solid #000;
            border-radius: 2px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            font-weight: bold;
        }

        .bubble.filled {
            background: #000;
            color: #fff;
        }

        .bubble.crossed {
            position: relative;
        }

        .bubble.crossed::after {
            content: '×';
            font-size: 12pt;
            font-weight: bold;
        }

        /* Student Info Section */
        .student-info {
            border: 1px solid #000;
            margin-bottom: 8px;
        }

        .student-info-row {
            display: flex;
            border-bottom: 1px solid #000;
        }

        .student-info-row:last-child {
            border-bottom: none;
        }

        .student-info-label {
            width: 100px;
            padding: 5px;
            font-weight: bold;
            font-size: 9pt;
            background: #f5f5f5;
            border-right: 1px solid #000;
        }

        .student-info-value {
            flex: 1;
            padding: 5px;
            min-height: 25px;
        }

        /* Proctor Info */
        .proctor-info {
            border: 1px solid #000;
            margin-bottom: 8px;
        }

        .proctor-row {
            display: flex;
        }

        .proctor-cell {
            flex: 1;
            border-right: 1px solid #000;
            padding: 4px;
        }

        .proctor-cell:last-child {
            border-right: none;
        }

        .proctor-cell-label {
            font-size: 8pt;
            font-weight: bold;
            text-align: center;
            border-bottom: 1px solid #000;
            padding: 3px;
            background: #f5f5f5;
        }

        .proctor-cell-value {
            padding: 4px;
            min-height: 25px;
        }

        .date-box {
            border: 1px solid #000;
            padding: 3px 8px;
            display: inline-block;
            margin: 2px;
            min-width: 60px;
            text-align: center;
            font-size: 8pt;
        }

        /* Right Panel Boxes */
        .right-box {
            border: 1px solid #000;
            margin-bottom: 8px;
            padding: 6px;
        }

        .right-box h4 {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 4px;
            text-align: center;
        }

        .peserta-box {
            border: 2px solid #000;
            padding: 15px 10px;
            text-align: center;
            font-size: 14pt;
            min-height: 50px;
        }

        /* Subject Checkboxes */
        .subject-list {
            font-size: 8pt;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3px;
        }

        .subject-item {
            display: flex;
            align-items: center;
            gap: 3px;
        }

        .checkbox-box {
            width: 10px;
            height: 10px;
            border: 1px solid #000;
        }

        /* Answer Section */
        .answer-section {
            border: 2px solid #000;
            margin-top: 8px;
        }

        .answer-title {
            font-size: 10pt;
            font-weight: bold;
            text-align: center;
            padding: 5px;
            background: #f5f5f5;
            border-bottom: 2px solid #000;
        }

        .answer-grid {
            display: flex;
            padding: 8px;
        }

        .answer-column {
            flex: 1;
            padding: 0 5px;
        }

        .answer-row {
            display: flex;
            align-items: center;
            margin-bottom: 4px;
        }

        .answer-number {
            width: 20px;
            font-weight: bold;
            font-size: 9pt;
            text-align: right;
            padding-right: 5px;
        }

        .answer-bubbles {
            display: flex;
            gap: 2px;
        }

        .answer-bubble {
            width: 16px;
            height: 16px;
            border: 1px solid #000;
            border-radius: 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 8pt;
            font-weight: bold;
        }

        /* Essay Lines */
        .essay-section {
            border: 1px solid #000;
            margin-top: 8px;
            padding: 8px;
        }

        .essay-line {
            border-bottom: 1px solid #ccc;
            height: 25px;
            margin-bottom: 2px;
        }

        /* Print specific */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Preview mode */
        @if (isset($preview) && $preview)
            .preview-actions {
                position: fixed;
                top: 10px;
                right: 10px;
                z-index: 1000;
                display: flex;
                gap: 10px;
            }

            .preview-btn {
                padding: 10px 20px;
                background: #4CAF50;
                color: white;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-size: 14px;
            }

            .preview-btn:hover {
                background: #45a049;
            }

            .preview-btn.secondary {
                background: #6c757d;
            }
        @endif
    </style>
</head>

<body>
    @if (isset($preview) && $preview)
        <div class="preview-actions no-print">
            <button class="preview-btn" onclick="window.print()">🖨️ Cetak</button>
            <a href="{{ route('ljk.print', $template) }}" class="preview-btn secondary" target="_blank">📄 Download PDF</a>
            <a href="{{ route('ljk.index') }}" class="preview-btn secondary">← Kembali</a>
        </div>
    @endif

    <div class="container">
        <!-- Kop Surat -->
        <div class="kop-surat">
            @php
                $kopImage = null;
                $kopPath = null;

                if ($template->kop_image) {
                    $kopPath = storage_path('app/public/' . $template->kop_image);
                } elseif (isset($schoolSettings) && $schoolSettings->kop_surat) {
                    $kopPath = storage_path('app/public/' . $schoolSettings->kop_surat);
                }

                if ($kopPath && file_exists($kopPath)) {
                    if (isset($preview) && $preview) {
                        // For preview, use asset URL
                        $kopImage = asset('storage/' . ($template->kop_image ?? $schoolSettings->kop_surat));
                    } else {
                        // For PDF, use base64
                        $imageData = base64_encode(file_get_contents($kopPath));
                        $mimeType = mime_content_type($kopPath);
                        $kopImage = 'data:' . $mimeType . ';base64,' . $imageData;
                    }
                }
            @endphp
            @if ($kopImage)
                <img src="{{ $kopImage }}" alt="Kop Surat">
            @endif
        </div>

        <!-- Title Section -->
        <div class="title-section">
            <h1>{{ strtoupper($template->jenis_ujian == 'STS' ? 'SUMATIF TENGAH SEMESTER (STS)' : ($template->jenis_ujian == 'SAS' ? 'SUMATIF AKHIR SEMESTER (SAS)' : $template->jenis_ujian)) }}
                {{ strtoupper(getSemester()) }}</h1>
            <h2>TAHUN PELAJARAN {{ $template->tahun_ajaran ?? date('Y') . '/' . (date('Y') + 1) }}</h2>
            <h3>LEMBAR JAWABAN UJIAN</h3>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Left Panel -->
            <div class="left-panel">
                <!-- Example Box -->
                <div class="example-box">
                    <h4>Contoh cara menyilang</h4>
                    <div class="example-row">
                        <div class="bubble-example">
                            <span class="bubble">A</span>
                            <span class="bubble">B</span>
                            <span class="bubble filled">C</span>
                            <span class="bubble">D</span>
                            @if ($template->jumlah_pilihan >= 5)
                                <span class="bubble">E</span>
                            @endif
                            <span style="margin-left: 5px;">Benar</span>
                        </div>
                        <div class="bubble-example">
                            <span class="bubble">A</span>
                            <span class="bubble">B</span>
                            <span class="bubble">C</span>
                            <span class="bubble crossed"></span>
                            @if ($template->jumlah_pilihan >= 5)
                                <span class="bubble">E</span>
                            @endif
                            <span style="margin-left: 5px;">Salah</span>
                        </div>
                    </div>
                    <div class="example-row">
                        <div class="bubble-example">
                            <span class="bubble crossed">A</span>
                            <span class="bubble crossed">B</span>
                            <span class="bubble">C</span>
                            <span class="bubble">D</span>
                            @if ($template->jumlah_pilihan >= 5)
                                <span class="bubble">E</span>
                            @endif
                            <span style="margin-left: 5px;">Salah</span>
                        </div>
                        <div class="bubble-example">
                            <span class="bubble">A</span>
                            <span class="bubble crossed">☑</span>
                            <span class="bubble">C</span>
                            <span class="bubble">D</span>
                            @if ($template->jumlah_pilihan >= 5)
                                <span class="bubble">E</span>
                            @endif
                            <span style="margin-left: 5px;">Salah</span>
                        </div>
                    </div>
                </div>

                <!-- Student Info -->
                <div class="student-info">
                    <div class="student-info-row">
                        <div class="student-info-label">NAMA PESERTA</div>
                        <div class="student-info-value"></div>
                        <div class="student-info-label" style="width: 60px;">KELAS</div>
                        <div class="student-info-value" style="width: 80px;"></div>
                    </div>
                </div>

                <!-- Proctor Info -->
                <div class="proctor-info">
                    <div class="proctor-row">
                        <div class="proctor-cell">
                            <div class="proctor-cell-label">NAMA PENGAWAS</div>
                            <div class="proctor-cell-value"></div>
                        </div>
                        <div class="proctor-cell">
                            <div class="proctor-cell-label">Tgl Ujian</div>
                            <div class="proctor-cell-value" style="text-align: center;">
                                <span class="date-box">Tanggal</span><br>
                                <span class="date-box">Bulan</span>
                            </div>
                        </div>
                        <div class="proctor-cell">
                            <div class="proctor-cell-label">Tanggal Lahir</div>
                            <div class="proctor-cell-value" style="text-align: center;">
                                <span class="date-box">Tanggal</span><br>
                                <span class="date-box">Bulan</span><br>
                                <span class="date-box">Tahun</span>
                            </div>
                        </div>
                    </div>
                    <div class="proctor-row">
                        <div class="proctor-cell">
                            <div class="proctor-cell-label">NILAI</div>
                            <div class="proctor-cell-value" style="min-height: 40px;"></div>
                        </div>
                        <div class="proctor-cell">
                            <div class="proctor-cell-label">Paraf Guru</div>
                            <div class="proctor-cell-value" style="min-height: 40px;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Panel -->
            <div class="right-panel">
                <!-- Instructions -->
                <div class="instructions-box">
                    <h4>PETUNJUK PENGISIAN:</h4>
                    <ol>
                        <li>Isilah dengan Pensil 2B</li>
                        <li>Tulis Nama Peserta kemudian X</li>
                        <li>Jika salah hapus yang bersih</li>
                        <li>LJU tidak boleh kotor atau robek</li>
                        <li>Jika tidak paham tanya pengawas</li>
                    </ol>
                </div>

                <!-- Nomor Peserta -->
                <div class="right-box">
                    <h4>Nomor Peserta</h4>
                    <div class="peserta-box"></div>
                </div>

                <!-- Mata Pelajaran -->
                <div class="right-box">
                    <h4>Mata Pelajaran</h4>
                    <div class="subject-list">
                        @php
                            $subjects =
                                $template->mata_pelajaran_list ?? \App\Models\LjkTemplate::defaultMataPelajaranList();
                        @endphp
                        @foreach ($subjects as $subject)
                            <div class="subject-item">
                                <div class="checkbox-box"></div>
                                <span>{{ strtoupper($subject) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Answer Section -->
        <div class="answer-section">
            <div class="answer-title">JAWABAN</div>
            <div class="answer-grid">
                @php
                    $totalQuestions = $template->jumlah_soal;
                    $options = $template->options;
                    $columns = 5;
                    $questionsPerColumn = ceil($totalQuestions / $columns);
                @endphp

                @for ($col = 0; $col < $columns; $col++)
                    <div class="answer-column">
                        @for ($i = 0; $i < $questionsPerColumn; $i++)
                            @php $questionNum = $col * $questionsPerColumn + $i + 1; @endphp
                            @if ($questionNum <= $totalQuestions)
                                <div class="answer-row">
                                    <span class="answer-number">{{ $questionNum }}</span>
                                    <div class="answer-bubbles">
                                        @foreach ($options as $option)
                                            <span class="answer-bubble">{{ $option }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endfor
                    </div>
                @endfor
            </div>
        </div>

        <!-- Essay Lines -->
        @if ($template->show_essay_lines)
            <div class="essay-section">
                @for ($i = 0; $i < 8; $i++)
                    <div class="essay-line"></div>
                @endfor
            </div>
        @endif
    </div>
</body>

</html>

@php
    function getSemester()
    {
        $month = date('n');
        return $month >= 1 && $month <= 6 ? 'GENAP' : 'GANJIL';
    }
@endphp
