<!DOCTYPE html>
<html lang="id" dir="auto">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STS - {{ $sts->mata_pelajaran }}</title>
    <style>
        @page {
            margin: 1.2cm 1.5cm 1.2cm 1.5cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            line-height: 1.3;
            color: #000;
            background: #fff;
        }

        /* Kop Surat / Header */
        .kop-surat {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 6px;
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
            width: 60px;
            text-align: center;
        }

        .kop-logo img {
            max-width: 50px;
            max-height: 50px;
            object-fit: contain;
        }

        .kop-center {
            text-align: center;
            padding: 0 8px;
        }

        .kop-sekolah {
            font-size: 13pt;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .kop-alamat {
            font-size: 8pt;
            margin-top: 2px;
        }

        /* Title Section */
        .title-section {
            text-align: center;
            margin: 8px 0 6px;
            padding: 5px;
            background: #f0f0f0;
            border: 1px solid #ccc;
        }

        .title-section h1 {
            font-size: 11pt;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
        }

        .title-section div {
            font-size: 9pt;
        }

        /* Info Table */
        .info-table {
            width: 100%;
            margin-bottom: 6px;
            border-collapse: collapse;
        }

        .info-table td {
            padding: 2px 4px;
            vertical-align: top;
            font-size: 9pt;
        }

        .info-table .label {
            width: 110px;
            font-weight: bold;
        }

        .info-table .colon {
            width: 10px;
            text-align: center;
        }

        .info-table .value {
            border-bottom: 1px dotted #000;
        }

        /* Instruction */
        .instruction {
            font-weight: bold;
            font-size: 8.5pt;
            margin: 6px 0;
            padding: 4px 6px;
            background: #eee;
            border-left: 3px solid #333;
        }

        /* 2-Column Layout */
        .two-col-table {
            width: 100%;
            border-collapse: collapse;
        }

        .two-col-table td {
            width: 50%;
            vertical-align: top;
            padding: 0 8px;
        }

        .two-col-table td:first-child {
            border-right: 1px solid #ccc;
            padding-left: 0;
        }

        .two-col-table td:last-child {
            padding-right: 0;
        }

        /* Questions */
        .section-title {
            font-weight: bold;
            font-size: 9.5pt;
            margin: 6px 0 4px;
            padding: 3px 6px;
            background: #ddd;
        }

        .question-item {
            margin-bottom: 6px;
            font-size: 9pt;
            line-height: 1.3;
        }

        .question-number {
            font-weight: bold;
        }

        .options-compact {
            margin: 2px 0 0 12px;
        }

        .option-inline {
            display: inline-block;
            margin-right: 8px;
            font-size: 8.5pt;
        }

        /* Statement list for PG Kompleks */
        .statement-item {
            margin: 2px 0 2px 14px;
            padding: 2px 4px;
            font-size: 8.5pt;
            background: #fafafa;
            border-left: 2px solid #ccc;
        }

        /* Matching table */
        .matching-table {
            width: 100%;
            border-collapse: collapse;
            margin: 4px 0;
            font-size: 8.5pt;
        }

        .matching-table th,
        .matching-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            text-align: left;
        }

        .matching-table th {
            background: #ddd;
            text-align: center;
            font-size: 8.5pt;
        }

        /* Essay */
        .essay-lines {
            margin: 3px 0 6px 14px;
            font-size: 8pt;
            color: #999;
        }

        /* Footer */
        .footer-text {
            text-align: right;
            font-style: italic;
            margin-top: 8px;
            font-size: 9pt;
        }

        /* Page break */
        .page-break {
            page-break-before: always;
        }

        /* Kunci Jawaban */
        .kunci-title {
            font-weight: bold;
            font-size: 11pt;
            text-align: center;
            margin: 8px 0 6px;
        }

        .kunci-section-title {
            font-weight: bold;
            font-size: 9pt;
            margin: 4px 0 2px;
        }

        .kunci-content {
            font-size: 8.5pt;
            margin-bottom: 4px;
            line-height: 1.4;
        }
    </style>
</head>

<body>
    @php
        $content = $sts->content_result ?? [];

        // Split soal pilihan ganda into 2 columns
        $pgSoal = $content['soal_pilihan_ganda'] ?? [];
        $totalPg = count($pgSoal);
        $halfPg = ceil($totalPg / 2);
        $pgLeft = array_slice($pgSoal, 0, $halfPg);
        $pgRight = array_slice($pgSoal, $halfPg);
    @endphp

    {{-- ============ PAGE 1: KOP + SOAL ============ --}}

    {{-- KOP SURAT --}}
    <div class="kop-surat">
        @if (isset($schoolSettings) && $schoolSettings->kop_surat)
            <img src="{{ storage_path('app/public/' . $schoolSettings->kop_surat) }}" alt="Kop Surat"
                class="kop-surat-image">
        @elseif(isset($schoolSettings) && ($schoolSettings->logo || $schoolSettings->logo_kanan || $schoolSettings->nama_sekolah))
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
                            <div style="font-size: 8pt;">
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
            <div style="font-size: 12pt; font-weight: bold; text-transform: uppercase;">SOAL SUMATIF TENGAH SEMESTER
            </div>
        @endif
    </div>

    {{-- TITLE --}}
    <div class="title-section">
        <h1>SUMATIF TENGAH SEMESTER (STS)</h1>
        <div>TAHUN PELAJARAN {{ date('Y') }}/{{ date('Y') + 1 }}</div>
    </div>

    {{-- INFO TABLE --}}
    <table class="info-table">
        <tr>
            <td class="label">Mata Pelajaran</td>
            <td class="colon">:</td>
            <td class="value">{{ $sts->mata_pelajaran }}</td>
            <td class="label" style="padding-left: 20px;">Nama</td>
            <td class="colon">:</td>
            <td class="value" style="width: 150px;"></td>
        </tr>
        <tr>
            <td class="label">Kelas</td>
            <td class="colon">:</td>
            <td class="value">{{ $sts->kelas }}</td>
            <td class="label" style="padding-left: 20px;">Hari, Tanggal</td>
            <td class="colon">:</td>
            <td class="value"></td>
        </tr>
    </table>

    {{-- INSTRUCTION --}}
    <div class="instruction">
        Berilah tanda silang (X) pada huruf A, B, C, atau D di depan jawaban yang paling tepat!
    </div>

    {{-- SOAL PILIHAN GANDA - 2 COLUMNS --}}
    @if (!empty($pgSoal))
        <div class="section-title">I. PILIHAN GANDA</div>
        <table class="two-col-table">
            <tr>
                <td>
                    @foreach ($pgLeft as $index => $soal)
                        <div class="question-item">
                            <span class="question-number">{{ $index + 1 }}.</span>
                            {{ $soal['pertanyaan'] ?? '' }}
                            @if (!empty($soal['pilihan']))
                                <div class="options-compact">
                                    @foreach ($soal['pilihan'] as $key => $pilihan)
                                        <span class="option-inline">{{ $key }}. {{ $pilihan }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </td>
                <td>
                    @foreach ($pgRight as $index => $soal)
                        <div class="question-item">
                            <span class="question-number">{{ $halfPg + $index + 1 }}.</span>
                            {{ $soal['pertanyaan'] ?? '' }}
                            @if (!empty($soal['pilihan']))
                                <div class="options-compact">
                                    @foreach ($soal['pilihan'] as $key => $pilihan)
                                        <span class="option-inline">{{ $key }}. {{ $pilihan }}</span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </td>
            </tr>
        </table>
    @endif

    {{-- SOAL PG KOMPLEKS --}}
    @if (!empty($content['soal_pg_kompleks']))
        <div class="section-title">II. PILIHAN GANDA KOMPLEKS</div>
        <div style="margin-bottom: 4px; font-style: italic; font-size: 8.5pt;">Tentukan pernyataan berikut Benar atau
            Salah!</div>
        @foreach ($content['soal_pg_kompleks'] as $index => $soal)
            <div class="question-item">
                <span class="question-number">{{ $index + 1 }}.</span>
                {{ $soal['pertanyaan'] ?? '' }}
                @if (!empty($soal['pernyataan']))
                    @foreach ($soal['pernyataan'] as $p)
                        <div class="statement-item">{{ $p['teks'] ?? '' }} (................)</div>
                    @endforeach
                @endif
            </div>
        @endforeach
    @endif

    {{-- SOAL MENJODOHKAN --}}
    @if (!empty($content['soal_menjodohkan']))
        <div class="section-title">III. MENJODOHKAN</div>
        <div style="margin-bottom: 4px; font-style: italic; font-size: 8.5pt;">Jodohkan pernyataan di kolom kiri dengan
            jawaban di kolom kanan!</div>
        <table class="matching-table">
            <thead>
                <tr>
                    <th style="width: 25px;">No</th>
                    <th>Soal</th>
                    <th style="width: 60px;">Jawaban</th>
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

    {{-- SOAL URAIAN --}}
    @if (!empty($content['soal_uraian']))
        <div class="section-title">IV. URAIAN</div>
        <div style="margin-bottom: 4px; font-style: italic; font-size: 8.5pt;">Jawablah pertanyaan berikut dengan jelas
            dan lengkap!</div>
        @foreach ($content['soal_uraian'] as $index => $soal)
            <div class="question-item">
                <span class="question-number">{{ $index + 1 }}.</span>
                {{ $soal['pertanyaan'] ?? '' }}
                <div class="essay-lines">
                    .........................................................................................................................
                </div>
            </div>
        @endforeach
    @endif

    {{-- FOOTER --}}
    <div class="footer-text">*** Selamat Mengerjakan ***</div>

    {{-- ============ PAGE 2: KUNCI JAWABAN - 2 COLUMNS ============ --}}
    @if (!empty($content['kunci_jawaban']))
        <div class="page-break"></div>

        <div class="kunci-title">KUNCI JAWABAN</div>

        <table class="two-col-table">
            <tr>
                <td>
                    {{-- Kolom Kiri: PG + PG Kompleks --}}
                    @if (!empty($content['kunci_jawaban']['pilihan_ganda']))
                        <div class="kunci-section-title">A. Pilihan Ganda</div>
                        <div class="kunci-content">
                            @foreach ($content['kunci_jawaban']['pilihan_ganda'] as $i => $kunci)
                                {{ $i + 1 . '. ' . $kunci }}@if (!$loop->last)
                                    &nbsp;|&nbsp;
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if (!empty($content['kunci_jawaban']['pg_kompleks']))
                        <div class="kunci-section-title">B. Pilihan Ganda Kompleks</div>
                        <div class="kunci-content">
                            @foreach ($content['kunci_jawaban']['pg_kompleks'] as $i => $item)
                                <div>{{ $i + 1 . '. ' . ($item['jawaban'] ?? '') }}</div>
                            @endforeach
                        </div>
                    @endif
                </td>
                <td>
                    {{-- Kolom Kanan: Menjodohkan + Uraian --}}
                    @if (!empty($content['kunci_jawaban']['menjodohkan']))
                        <div class="kunci-section-title">C. Menjodohkan</div>
                        <div class="kunci-content">
                            {{ implode(', ', $content['kunci_jawaban']['menjodohkan']) }}
                        </div>
                    @endif

                    @if (!empty($content['kunci_jawaban']['uraian']))
                        <div class="kunci-section-title">D. Uraian</div>
                        <div class="kunci-content">
                            @foreach ($content['kunci_jawaban']['uraian'] as $i => $item)
                                <div style="margin-bottom: 4px;">
                                    <strong>{{ $i + 1 }}.</strong> {{ $item['jawaban'] ?? '' }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    @endif

</body>

</html>
