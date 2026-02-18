<!DOCTYPE html>
<html lang="id" dir="auto">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>STS - {{ $sts->mata_pelajaran }}</title>
    <style>
        @page {
            margin: 1cm 1.2cm 1cm 1.2cm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9pt;
            line-height: 1.35;
            color: #000;
        }

        /* ===== KOP SURAT ===== */
        .kop-surat {
            text-align: center;
            margin-bottom: 5px;
            padding-bottom: 4px;
            border-bottom: 3px double #000;
        }

        .kop-surat-image {
            width: 100%;
            height: auto;
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
            width: 55px;
            text-align: center;
        }

        .kop-logo img {
            max-width: 45px;
            max-height: 45px;
        }

        .kop-center {
            text-align: center;
            padding: 0 5px;
        }

        .kop-sekolah {
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .kop-alamat {
            font-size: 7.5pt;
        }

        /* ===== TITLE ===== */
        .title-box {
            text-align: center;
            margin: 5px 0;
            padding: 4px;
            background: #f0f0f0;
            border: 1px solid #bbb;
        }

        .title-box h1 {
            font-size: 10.5pt;
            margin: 0;
            text-transform: uppercase;
        }

        .title-box .sub {
            font-size: 8.5pt;
        }

        /* ===== INFO ===== */
        .info-row {
            width: 100%;
            margin-bottom: 3px;
            border-collapse: collapse;
        }

        .info-row td {
            padding: 1px 3px;
            font-size: 8.5pt;
            vertical-align: top;
        }

        .info-row .lbl {
            font-weight: bold;
            width: 100px;
        }

        .info-row .col {
            width: 8px;
            text-align: center;
        }

        .info-row .val {
            border-bottom: 1px dotted #000;
        }

        /* ===== PETUNJUK ===== */
        .petunjuk {
            font-size: 8pt;
            font-weight: bold;
            padding: 3px 5px;
            background: #eee;
            border-left: 3px solid #333;
            margin-bottom: 4px;
        }

        /* ===== SECTION TITLE ===== */
        .sec-title {
            font-weight: bold;
            font-size: 9pt;
            padding: 2px 5px;
            background: #ddd;
            margin: 4px 0 3px;
        }

        /* ===== 2-COLUMN FLOAT LAYOUT ===== */
        .col-wrap {
            overflow: hidden;
        }

        .col-left {
            float: left;
            width: 49%;
            padding-right: 6px;
            border-right: 1px dotted #bbb;
        }

        .col-right {
            float: right;
            width: 49%;
            padding-left: 6px;
        }

        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        /* ===== SOAL ===== */
        .soal {
            margin-bottom: 5px;
            font-size: 8.5pt;
            line-height: 1.3;
        }

        .soal .no {
            font-weight: bold;
        }

        .opsi {
            margin: 1px 0 0 10px;
            font-size: 8pt;
        }

        .opsi span {
            display: inline-block;
            margin-right: 6px;
        }

        /* PG Kompleks pernyataan */
        .pernyataan {
            margin: 1px 0 1px 12px;
            padding: 1px 3px;
            font-size: 8pt;
            background: #f8f8f8;
            border-left: 2px solid #ccc;
        }

        /* ===== TABEL MENJODOHKAN ===== */
        .tbl-match {
            width: 100%;
            border-collapse: collapse;
            margin: 3px 0;
            font-size: 8pt;
        }

        .tbl-match th,
        .tbl-match td {
            border: 1px solid #000;
            padding: 2px 4px;
        }

        .tbl-match th {
            background: #ddd;
            text-align: center;
            font-size: 8pt;
        }

        /* ===== URAIAN ===== */
        .uraian-lines {
            margin: 2px 0 4px 12px;
            border-bottom: 1px dotted #999;
            height: 25px;
        }

        /* ===== FOOTER ===== */
        .footer {
            text-align: right;
            font-style: italic;
            font-size: 8.5pt;
            margin-top: 6px;
        }

        /* ===== PAGE 2 ===== */
        .page-break {
            page-break-before: always;
        }

        .kunci-title {
            text-align: center;
            font-weight: bold;
            font-size: 11pt;
            margin: 6px 0;
        }

        .kunci-head {
            font-weight: bold;
            font-size: 8.5pt;
            margin: 3px 0 1px;
        }

        .kunci-text {
            font-size: 8pt;
            line-height: 1.4;
            margin-bottom: 3px;
        }
    </style>
</head>

<body>
    @php
        $content = $sts->content_result ?? [];
        $pgSoal = $content['soal_pilihan_ganda'] ?? [];
        $totalPg = count($pgSoal);
        $halfPg = (int) ceil($totalPg / 2);
        $pgLeft = array_slice($pgSoal, 0, $halfPg);
        $pgRight = array_slice($pgSoal, $halfPg);
    @endphp

    {{-- ===== KOP SURAT ===== --}}
    <div class="kop-surat">
        @if (isset($schoolSettings) &&
                $schoolSettings->kop_surat &&
                file_exists(storage_path('app/public/' . $schoolSettings->kop_surat)))
            <img src="{{ storage_path('app/public/' . $schoolSettings->kop_surat) }}" alt="Kop Surat"
                class="kop-surat-image">
        @elseif(isset($schoolSettings) && ($schoolSettings->logo || $schoolSettings->logo_kanan || $schoolSettings->nama_sekolah))
            <table class="kop-table">
                <tr>
                    <td class="kop-logo">
                        @if ($schoolSettings->logo && file_exists(storage_path('app/public/' . $schoolSettings->logo)))
                            <img src="{{ storage_path('app/public/' . $schoolSettings->logo) }}">
                        @endif
                    </td>
                    <td class="kop-center">
                        @if ($schoolSettings->nama_sekolah)
                            <div class="kop-sekolah">{{ $schoolSettings->nama_sekolah }}</div>
                        @endif
                        @if ($schoolSettings->npsn || $schoolSettings->nsm)
                            <div style="font-size: 7.5pt;">
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
                        @if ($schoolSettings->logo_kanan && file_exists(storage_path('app/public/' . $schoolSettings->logo_kanan)))
                            <img src="{{ storage_path('app/public/' . $schoolSettings->logo_kanan) }}">
                        @endif
                    </td>
                </tr>
            </table>
        @else
            <div style="font-size: 11pt; font-weight: bold;">SOAL SUMATIF TENGAH SEMESTER</div>
        @endif
    </div>

    {{-- ===== TITLE ===== --}}
    <div class="title-box">
        <h1>SUMATIF TENGAH SEMESTER (STS)</h1>
        <div class="sub">TAHUN PELAJARAN {{ date('Y') }}/{{ date('Y') + 1 }}</div>
    </div>

    {{-- ===== INFO ===== --}}
    <table class="info-row">
        <tr>
            <td class="lbl">Mata Pelajaran</td>
            <td class="col">:</td>
            <td class="val">{{ $sts->mata_pelajaran }}</td>
            <td style="width: 30px;"></td>
            <td class="lbl">Nama</td>
            <td class="col">:</td>
            <td class="val" style="width: 180px;">.........................</td>
        </tr>
        <tr>
            <td class="lbl">Kelas</td>
            <td class="col">:</td>
            <td class="val">{{ $sts->kelas }}</td>
            <td></td>
            <td class="lbl">Hari/Tanggal</td>
            <td class="col">:</td>
            <td class="val">.........................</td>
        </tr>
    </table>

    {{-- ===== PETUNJUK ===== --}}
    <div class="petunjuk">
        Berilah tanda silang (X) pada huruf A, B, C, atau D di depan jawaban yang paling tepat!
    </div>

    {{-- ===== I. PILIHAN GANDA (2 KOLOM) ===== --}}
    @if (!empty($pgSoal))
        <div class="sec-title">I. PILIHAN GANDA</div>
        <div class="col-wrap clearfix">
            <div class="col-left">
                @foreach ($pgLeft as $index => $soal)
                    <div class="soal">
                        <span class="no">{{ $index + 1 }}.</span> {{ $soal['pertanyaan'] ?? '' }}
                        @if (!empty($soal['pilihan']))
                            <div class="opsi">
                                @foreach ($soal['pilihan'] as $key => $pilihan)
                                    <span>{{ $key }}. {{ $pilihan }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            <div class="col-right">
                @foreach ($pgRight as $index => $soal)
                    <div class="soal">
                        <span class="no">{{ $halfPg + $index + 1 }}.</span> {{ $soal['pertanyaan'] ?? '' }}
                        @if (!empty($soal['pilihan']))
                            <div class="opsi">
                                @foreach ($soal['pilihan'] as $key => $pilihan)
                                    <span>{{ $key }}. {{ $pilihan }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ===== II. PG KOMPLEKS ===== --}}
    @if (!empty($content['soal_pg_kompleks']))
        <div class="sec-title">II. PILIHAN GANDA KOMPLEKS</div>
        <div style="font-size: 7.5pt; font-style: italic; margin-bottom: 2px;">Tentukan pernyataan berikut Benar atau
            Salah!</div>
        @foreach ($content['soal_pg_kompleks'] as $index => $soal)
            <div class="soal">
                <span class="no">{{ $index + 1 }}.</span> {{ $soal['pertanyaan'] ?? '' }}
                @if (!empty($soal['pernyataan']))
                    @foreach ($soal['pernyataan'] as $p)
                        <div class="pernyataan">{{ $p['teks'] ?? '' }} (................)</div>
                    @endforeach
                @endif
            </div>
        @endforeach
    @endif

    {{-- ===== III. MENJODOHKAN ===== --}}
    @if (!empty($content['soal_menjodohkan']))
        <div class="sec-title">III. MENJODOHKAN</div>
        <div style="font-size: 7.5pt; font-style: italic; margin-bottom: 2px;">Jodohkan pernyataan di kolom kiri dengan
            jawaban di kolom kanan!</div>
        <table class="tbl-match">
            <thead>
                <tr>
                    <th style="width: 20px;">No</th>
                    <th>Soal</th>
                    <th style="width: 55px;">Jawaban</th>
                    <th>Pilihan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($content['soal_menjodohkan'] as $index => $soal)
                    <tr>
                        <td style="text-align: center;">{{ $index + 1 }}</td>
                        <td>{{ $soal['soal'] ?? '' }}</td>
                        <td style="text-align: center;">(......)</td>
                        <td>{{ chr(65 + $index) }}. {{ $soal['jawaban'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- ===== IV. URAIAN ===== --}}
    @if (!empty($content['soal_uraian']))
        <div class="sec-title">IV. URAIAN</div>
        <div style="font-size: 7.5pt; font-style: italic; margin-bottom: 2px;">Jawablah pertanyaan berikut dengan jelas
            dan lengkap!</div>
        @foreach ($content['soal_uraian'] as $index => $soal)
            <div class="soal">
                <span class="no">{{ $index + 1 }}.</span> {{ $soal['pertanyaan'] ?? '' }}
                <div class="uraian-lines"></div>
            </div>
        @endforeach
    @endif

    {{-- ===== FOOTER ===== --}}
    <div class="footer">*** Selamat Mengerjakan ***</div>

    {{-- ===== HALAMAN 2: KUNCI JAWABAN (2 KOLOM) ===== --}}
    @if (!empty($content['kunci_jawaban']))
        <div class="page-break"></div>
        <div class="kunci-title">KUNCI JAWABAN</div>

        <div class="col-wrap clearfix">
            <div class="col-left">
                @if (!empty($content['kunci_jawaban']['pilihan_ganda']))
                    <div class="kunci-head">A. Pilihan Ganda</div>
                    <div class="kunci-text">
                        @foreach ($content['kunci_jawaban']['pilihan_ganda'] as $i => $kunci)
                            {{ $i + 1 . '. ' . $kunci }}@if (!$loop->last)
                                &nbsp;|
                            @endif
                        @endforeach
                    </div>
                @endif

                @if (!empty($content['kunci_jawaban']['pg_kompleks']))
                    <div class="kunci-head">B. Pilihan Ganda Kompleks</div>
                    <div class="kunci-text">
                        @foreach ($content['kunci_jawaban']['pg_kompleks'] as $i => $item)
                            <div>{{ $i + 1 . '. ' . ($item['jawaban'] ?? '') }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="col-right">
                @if (!empty($content['kunci_jawaban']['menjodohkan']))
                    <div class="kunci-head">C. Menjodohkan</div>
                    <div class="kunci-text">{{ implode(', ', $content['kunci_jawaban']['menjodohkan']) }}</div>
                @endif

                @if (!empty($content['kunci_jawaban']['uraian']))
                    <div class="kunci-head">D. Uraian</div>
                    <div class="kunci-text">
                        @foreach ($content['kunci_jawaban']['uraian'] as $i => $item)
                            <div><strong>{{ $i + 1 }}.</strong> {{ $item['jawaban'] ?? '' }}</div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endif

</body>

</html>
