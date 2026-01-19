<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STS - {{ $sts->mata_pelajaran }}</title>
    <style>
        @page {
            margin: 2.5cm 2.5cm 2.5cm 2.5cm;
            size: A4;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 12pt;
            line-height: 1.4;
            color: #000;
            background: #fff;
        }

        /* Kop Surat / Header */
        .kop-surat {
            text-align: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 3px double #000;
        }

        .kop-surat-image {
            width: 100%;
            height: auto;
            max-width: 100%;
        }

        .kop-table {
            width: 100%;
            border-collapse: collapse;
        }

        .kop-table td {
            vertical-align: middle;
            padding: 0;
        }

        .kop-logo {
            width: 80px;
            text-align: center;
        }

        .kop-logo img {
            max-width: 70px;
            max-height: 70px;
            object-fit: contain;
        }

        .kop-center {
            text-align: center;
            padding: 0 10px;
        }

        .kop-yayasan {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .kop-sekolah {
            font-size: 16pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .kop-alamat {
            font-size: 10pt;
            margin-top: 3px;
        }

        /* Title Section */
        .title-section {
            text-align: center;
            margin: 20px 0 15px;
            padding: 10px;
            background: #f5f5f5;
            border: 1px solid #ccc;
        }

        .title-section h1 {
            font-size: 14pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        /* Info Table */
        .info-table {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 3px 5px;
            vertical-align: top;
        }

        .info-table .label {
            width: 140px;
            font-weight: bold;
        }

        .info-table .colon {
            width: 15px;
            text-align: center;
        }

        .info-table .value {
            border-bottom: 1px dotted #000;
        }

        .info-table .right-column {
            text-align: right;
            padding-left: 30px;
        }

        /* Instruction */
        .instruction {
            font-weight: bold;
            margin: 15px 0 10px;
            padding: 8px 10px;
            background: #eee;
            border-left: 4px solid #333;
        }

        /* Questions */
        .section-title {
            font-weight: bold;
            font-size: 12pt;
            margin: 15px 0 10px;
            padding: 5px 10px;
            background: #ddd;
        }

        .question-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .question-item {
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .question-number {
            font-weight: bold;
            display: inline;
        }

        .question-text {
            display: inline;
            margin-left: 5px;
        }

        .options-list {
            margin: 5px 0 0 25px;
            padding: 0;
            list-style: none;
        }

        .options-list li {
            margin-bottom: 3px;
        }

        .option-letter {
            font-weight: normal;
            margin-right: 8px;
        }

        /* Statement list for PG Kompleks */
        .statement-list {
            margin: 8px 0 0 25px;
            padding: 0;
            list-style: none;
        }

        .statement-list li {
            margin-bottom: 4px;
            padding: 3px 5px;
            background: #fafafa;
            border-left: 2px solid #ccc;
        }

        /* Matching table */
        .matching-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
        }

        .matching-table th,
        .matching-table td {
            border: 1px solid #000;
            padding: 5px 8px;
            text-align: left;
        }

        .matching-table th {
            background: #ddd;
            text-align: center;
        }

        /* Essay */
        .essay-space {
            height: 60px;
            border-bottom: 1px dotted #999;
            margin: 5px 0 15px 25px;
        }

        /* Page break */
        .page-break {
            page-break-before: always;
        }

        /* Footer */
        .footer-space {
            margin-top: 30px;
            text-align: right;
        }

        /* Compact 2-column layout for PG */
        .question-item-compact {
            margin-bottom: 10px;
            font-size: 11pt;
            line-height: 1.3;
        }

        .options-compact {
            margin: 3px 0 0 18px;
        }

        .option-inline {
            display: inline-block;
            margin-right: 12px;
            font-size: 11pt;
        }

        /* Two column layout for PG */
        .two-column-container {
            width: 100%;
            overflow: hidden;
        }

        .column-left {
            float: left;
            width: 48%;
            padding-right: 2%;
        }

        .column-right {
            float: right;
            width: 48%;
            padding-left: 2%;
        }
    </style>
</head>

<body>
    @php $content = $sts->content_result ?? []; @endphp

    <!-- KOP SURAT -->
    <div class="kop-surat">
        @if (isset($schoolSettings) && $schoolSettings->kop_surat)
            {{-- Jika ada gambar kop surat, gunakan langsung --}}
            <img src="{{ storage_path('app/public/' . $schoolSettings->kop_surat) }}" alt="Kop Surat"
                class="kop-surat-image">
        @elseif(isset($schoolSettings) && ($schoolSettings->logo || $schoolSettings->logo_kanan || $schoolSettings->nama_sekolah))
            {{-- Generate kop surat dari komponen --}}
            <table class="kop-table">
                <tr>
                    <td class="kop-logo">
                        @if ($schoolSettings->logo)
                            <img src="{{ storage_path('app/public/' . $schoolSettings->logo) }}" alt="Logo Kiri">
                        @endif
                    </td>
                    <td class="kop-center">
                        @if ($schoolSettings->nama_sekolah)
                            <div class="kop-sekolah">{{ $schoolSettings->nama_sekolah }}</div>
                        @endif
                        @if ($schoolSettings->npsn || $schoolSettings->nsm)
                            <div style="font-size: 10pt;">
                                @if ($schoolSettings->npsn)
                                    NPSN: {{ $schoolSettings->npsn }}
                                @endif
                                @if ($schoolSettings->npsn && $schoolSettings->nsm)
                                    |
                                @endif
                                @if ($schoolSettings->nsm)
                                    NSM: {{ $schoolSettings->nsm }}
                                @endif
                            </div>
                        @endif
                        @if ($schoolSettings->alamat)
                            <div class="kop-alamat">{{ $schoolSettings->alamat }}</div>
                        @endif
                    </td>
                    <td class="kop-logo">
                        @if ($schoolSettings->logo_kanan)
                            <img src="{{ storage_path('app/public/' . $schoolSettings->logo_kanan) }}" alt="Logo Kanan">
                        @endif
                    </td>
                </tr>
            </table>
        @else
            {{-- Default header jika tidak ada settings --}}
            <div style="font-size: 14pt; font-weight: bold; text-transform: uppercase;">SOAL SUMATIF TENGAH SEMESTER
            </div>
        @endif
    </div>

    <!-- TITLE -->
    <div class="title-section">
        <h1>SUMATIF TENGAH SEMESTER (STS)</h1>
        <div style="font-size: 11pt;">TAHUN PELAJARAN {{ date('Y') }}/{{ date('Y') + 1 }}</div>
    </div>

    <!-- INFO TABLE -->
    <table class="info-table">
        <tr>
            <td class="label">Mata Pelajaran</td>
            <td class="colon">:</td>
            <td class="value">{{ $sts->mata_pelajaran }}</td>
            <td class="right-column label">Nama</td>
            <td class="colon">:</td>
            <td class="value" style="width: 150px;"></td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td class="colon">:</td>
            <td class="value">{{ $sts->kelas }}</td>
            <td class="right-column label">Hari, Tanggal</td>
            <td class="colon">:</td>
            <td class="value"></td>
        </tr>
    </table>

    <!-- INSTRUCTION -->
    <div class="instruction">
        Berilah tanda silang (X) pada huruf A, B, C, atau D di depan jawaban yang paling tepat!
    </div>

    <!-- SOAL PILIHAN GANDA -->
    @if (!empty($content['soal_pilihan_ganda']))
        <div class="section-title">I. PILIHAN GANDA</div>
        <div class="pg-container">
            @foreach ($content['soal_pilihan_ganda'] as $index => $soal)
                <div class="question-item-compact">
                    <span class="question-number">{{ $index + 1 }}.</span>
                    <span class="question-text">{{ $soal['pertanyaan'] ?? '' }}</span>
                    @if (!empty($soal['pilihan']))
                        <div class="options-compact">
                            @foreach ($soal['pilihan'] as $key => $pilihan)
                                <span class="option-inline">{{ $key }}. {{ $pilihan }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @endif

    <!-- SOAL PG KOMPLEKS -->
    @if (!empty($content['soal_pg_kompleks']))
        <div class="section-title">II. PILIHAN GANDA KOMPLEKS</div>
        <p style="margin-bottom: 10px; font-style: italic;">Tentukan pernyataan berikut Benar atau Salah!</p>
        <ol class="question-list">
            @foreach ($content['soal_pg_kompleks'] as $index => $soal)
                <li class="question-item">
                    <span class="question-number">{{ $index + 1 }}.</span>
                    <span class="question-text">{{ $soal['pertanyaan'] ?? '' }}</span>
                    @if (!empty($soal['pernyataan']))
                        <ul class="statement-list">
                            @foreach ($soal['pernyataan'] as $p)
                                <li>{{ $p['teks'] ?? '' }} (................)</li>
                            @endforeach
                        </ul>
                    @endif
                </li>
            @endforeach
        </ol>
    @endif

    <!-- SOAL MENJODOHKAN -->
    @if (!empty($content['soal_menjodohkan']))
        <div class="section-title">III. MENJODOHKAN</div>
        <p style="margin-bottom: 10px; font-style: italic;">Jodohkan pernyataan di kolom kiri dengan jawaban di kolom
            kanan!</p>
        <table class="matching-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Soal</th>
                    <th style="width: 80px;">Jawaban</th>
                    <th>Pilihan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($content['soal_menjodohkan'] as $index => $soal)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $soal['soal'] ?? '' }}</td>
                        <td style="text-align: center;">(........)</td>
                        <td>{{ chr(65 + $index) }}. {{ $soal['jawaban'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- SOAL URAIAN -->
    @if (!empty($content['soal_uraian']))
        <div class="section-title">IV. URAIAN</div>
        <p style="margin-bottom: 10px; font-style: italic;">Jawablah pertanyaan berikut dengan jelas dan lengkap!</p>
        <ol class="question-list">
            @foreach ($content['soal_uraian'] as $index => $soal)
                <li class="question-item">
                    <span class="question-number">{{ $index + 1 }}.</span>
                    <span class="question-text">{{ $soal['pertanyaan'] ?? '' }}</span>
                    <div class="essay-space"></div>
                </li>
            @endforeach
        </ol>
    @endif

    <!-- FOOTER -->
    <div class="footer-space">
        <p>*** Selamat Mengerjakan ***</p>
    </div>
</body>

</html>
