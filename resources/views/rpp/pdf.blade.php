@php
    // Palet tema warna dokumen (config/rpp_themes.php). Dihitung di paling atas
    // karena dipakai di dalam <style> pada <head>.
    $themeKey = $rpp->tema ?? 'merah';
    $themeSet = config('rpp_themes.'.$themeKey) ?? config('rpp_themes.merah');
    $primary = '#'.$themeSet['primary'];
    $dark = '#'.$themeSet['dark'];
    $accent = '#'.$themeSet['accent'];
    // Tint 90% ke putih untuk latar kolom tahap (padanan #fff6f6 pada tema merah)
    $primaryTint = '#'.collect([0, 2, 4])
        ->map(fn ($i) => sprintf('%02x', (int) round(($h = hexdec(substr($themeSet['primary'], $i, 2))) + (255 - $h) * 0.9)))
        ->implode('');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>RPPM - {{ $rpp->mata_pelajaran }}</title>

    <style>
        @page {
            margin: 2.5cm;
            size: A4;
        }
        /* Reset bertarget — JANGAN reset `html` (dan jangan pakai `* {}`):
           margin:0 pada html menggeser acuan posisi elemen fixed di DomPDF,
           sehingga ornamen sudut (offset negatif) terlempar ke luar kanvas. */
        body, div, p, h1, h2, h3, h4, ol, ul, li, table, thead, tbody, tr, th, td, span { margin: 0; padding: 0; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.55;
            color: #1a1a1a;
        }

        /* ============== DECORATIONS ==============
           Ornamen sudut = PNG transparan (di-generate GD, public/decor-*.png).
           SVG, gradient, dan border-top triangle tidak dirender DomPDF;
           gambar PNG dirender andal. position:fixed = berulang tiap halaman. */
        .fx-dots { position: fixed; top: -1.6cm; left: -1.6cm; width: 48px; }
        .fx-tr   { position: fixed; top: -2.5cm; right: -2.5cm; width: 180px; }
        .fx-bl   { position: fixed; bottom: -2.5cm; left: -2.5cm; width: 160px; }
        .fx-br   { position: fixed; bottom: -2.5cm; right: -2.5cm; width: 115px; }

        .page-num { position: fixed; bottom: -1.7cm; right: 0; font-size: 12pt; font-weight: bold; color: #1a1a1a; }
        .page-num:before { content: counter(page); }

        /* ============== COVER PAGE ============== */
        .cover {
            page-break-after: always;
            text-align: center;
            padding: 30px 20px 40px;
            position: relative;
            /* JANGAN kasih height: DomPDF abaikan box-sizing utk height,
               height + padding overflow → cover terdorong ke halaman 2.
               Ornamen sudut sudah fixed per halaman, tak butuh cover full-height. */
        }

        .cover-school-logo { margin-bottom: 8px; margin-top: 40px; position: relative; z-index: 2; }
        .cover-school-logo img { max-height: 85px; max-width: 85px; }
        .cover-school-name { font-size: 12pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; position: relative; z-index: 2; }
        .cover-school-sub { font-size: 9pt; color: #555; letter-spacing: 1px; margin-bottom: 25px; position: relative; z-index: 2; }

        .cover-title-main { font-size: 24pt; font-weight: bold; text-transform: uppercase; line-height: 1.15; margin: 15px 0 0; letter-spacing: 1px; color: #4b5563; position: relative; z-index: 2; }
        .cover-subject { font-size: 32pt; font-weight: bold; color: {{ $accent }}; text-transform: uppercase; letter-spacing: 1px; margin: 0; line-height: 1.15; position: relative; z-index: 2; }
        .cover-semester { font-size: 13pt; color: #6b7280; margin-bottom: 20px; position: relative; z-index: 2; }

        .cover-garuda { margin: 15px auto; width: 210px; position: relative; z-index: 2; }
        .cover-garuda img { width: 100%; max-width: 230px; }

        .cover-author-label { font-size: 12pt; color: #374151; margin-top: 20px; margin-bottom: 5px; position: relative; z-index: 2; }
        .cover-author-name { font-size: 17pt; font-weight: bold; color: {{ $primary }}; position: relative; z-index: 2; }

        /* ============== TITLES ============== */
        .page-title { text-align: center; font-size: 14pt; font-weight: bold; margin-bottom: 20px; margin-top: 5px; }
        .section-letter { font-size: 12pt; font-weight: bold; margin: 18px 0 8px; }

        .page-break { page-break-before: always; }

        /* ============== KATA PENGANTAR ============== */
        .kata-pengantar-body { page-break-after: always; position: relative; z-index: 2; }
        .kata-pengantar-body p { text-align: justify; text-indent: 35px; margin-bottom: 10px; line-height: 1.7; font-size: 11pt; }
        .kata-pengantar-signature { text-align: right; margin-top: 35px; font-size: 11pt; }
        .kata-pengantar-signature .space { height: 55px; }

        /* ============== DAFTAR ISI ============== */
        /* Tanpa page-break-after: bagian modul berikutnya sudah punya
           page-break-before (double break = halaman kosong) */
        .daftar-isi { width: 100%; border-collapse: collapse; position: relative; z-index: 2; }
        .daftar-isi td { border: none; padding: 6px 0; font-size: 11pt; vertical-align: bottom; background: transparent; }
        .daftar-isi .dots { border-bottom: 1px dotted #555; padding: 0 8px 4px 8px; }
        .daftar-isi .page-col { text-align: right; width: 35px; }
        .daftar-isi .level-1 td { font-weight: bold; }
        .daftar-isi .level-2 td:first-child { padding-left: 20px; }

        /* ============== TABLES ============== */
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; position: relative; z-index: 2; background: white; }
        th, td { border: 1px solid #666; padding: 6px 9px; vertical-align: top; font-size: 10.5pt; line-height: 1.5; }

        .tbl-info td { border: none; padding: 3px 0; font-size: 11pt; background: transparent; }
        .tbl-info td:first-child { width: 32%; }

        .tbl-red thead th, .tbl-red tbody .row-head td { background-color: {{ $primary }}; color: #ffffff; font-weight: bold; text-align: center; padding: 7px 9px; border: 1px solid {{ $primary }}; }
        .tbl-red tbody .row-sub td { background-color: #f5f5f5; font-weight: bold; }
        .label-cell { font-weight: bold; background-color: #fafafa; width: 25%; }

        .tbl-langkah th { background-color: {{ $primary }}; color: #ffffff; text-align: center; font-weight: bold; padding: 8px; border: 1px solid {{ $primary }}; }
        .tbl-langkah .col-pengalaman { width: 20%; text-align: center; vertical-align: middle; background-color: {{ $primaryTint }}; font-weight: bold; color: {{ $primary }}; font-size: 12pt; padding: 15px 8px; }
        .tbl-langkah .fase-sintaks { font-weight: bold; font-style: italic; margin: 8px 0 4px; }
        .tbl-langkah .durasi { font-size: 9.5pt; color: #444; font-weight: normal; }

        /* ============== LKPD ============== */
        .lkpd-wrapper { border: 2px solid {{ $primary }}; padding: 15px; }
        .lkpd-header { text-align: center; border-bottom: 2px solid {{ $primary }}; padding-bottom: 10px; margin-bottom: 12px; }
        .lkpd-title { font-size: 13pt; font-weight: bold; color: {{ $primary }}; text-transform: uppercase; }
        .jawaban-box { border: 1px dashed #999; min-height: 55px; padding: 6px 8px; margin-top: 5px; color: #999; font-size: 9.5pt; background: #ffffff; }

        /* ============== SIGNATURE ============== */
        .signature-section { margin-top: 30px; page-break-inside: avoid; position: relative; z-index: 2; }
        .signature-table td { border: none; text-align: center; font-size: 11pt; background: transparent; }
        .signature-space { height: 60px; }
        .signature-name { font-weight: bold; text-decoration: underline; }
        .signature-nip { font-size: 10pt; }

        /* ============== HELPERS ============== */
        .text-bold { font-weight: bold; }
        .text-red { color: {{ $primary }}; }
        .text-center { text-align: center; }
        .mt-5 { margin-top: 5px; } .mt-10 { margin-top: 10px; } .mt-15 { margin-top: 15px; }
        .mb-5 { margin-bottom: 5px; } .mb-10 { margin-bottom: 10px; }
        ol, ul { margin-left: 20px; }
        ol li, ul li { margin-bottom: 4px; }
    </style>
</head>
<body>

@php
    $content = $rpp->content_result ?? [];
    $isPrint = $print ?? false;

    $schoolName = $schoolSettings->nama_sekolah ?? 'NAMA SEKOLAH';
    $schoolCity = $schoolSettings->kota ?? '';
    $tahunAjaran = date('Y') . '/' . (date('Y') + 1);

    $isKBC = ($rpp->kurikulum === 'Kurikulum Berbasis Cinta');
    $docName = 'Rencana Pelaksanaan Pembelajaran Mendalam (RPPM)';

    $tanggalDok = ($rpp->tanggal ? \Carbon\Carbon::parse($rpp->tanggal) : now())
        ->locale('id')->translatedFormat('d F Y');
    $kotaDok = $rpp->kota ?: ($schoolCity ?: '.............');

    $garudaSrc = $isPrint ? asset('garuda.png') : public_path('garuda.png');

    $huruf = 'A'; // penomoran section dinamis (section kondisional tidak bikin huruf loncat)
@endphp

{{-- =============================================================
     COVER
     ============================================================= --}}
{{-- Ornamen sudut: fixed langsung di bawah <body> (DomPDF tak merender fixed
     di dalam parent position:relative). Berulang otomatis di semua halaman. --}}
<img class="fx-dots" src="{{ $isPrint ? asset("decor-{$themeKey}-dots.png") : public_path("decor-{$themeKey}-dots.png") }}" alt="">
<img class="fx-tr" src="{{ $isPrint ? asset("decor-{$themeKey}-tr.png") : public_path("decor-{$themeKey}-tr.png") }}" alt="">
<img class="fx-bl" src="{{ $isPrint ? asset("decor-{$themeKey}-bl.png") : public_path("decor-{$themeKey}-bl.png") }}" alt="">
<img class="fx-br" src="{{ $isPrint ? asset("decor-{$themeKey}-br.png") : public_path("decor-{$themeKey}-br.png") }}" alt="">
<div class="page-num"></div>

<div class="cover">
    @if(isset($schoolSettings) && $schoolSettings->logo)
    <div class="cover-school-logo">
        <img src="{{ $isPrint ? asset('storage/' . $schoolSettings->logo) : storage_path('app/public/' . $schoolSettings->logo) }}" alt="Logo">
    </div>
    @endif
    <div class="cover-school-name">{{ strtoupper($schoolName) }}</div>
    @if($schoolCity)
    <div class="cover-school-sub">{{ strtoupper($schoolCity) }}</div>
    @endif

    <div class="cover-title-main">RENCANA PELAKSANAAN<br>PEMBELAJARAN MENDALAM<br><span style="font-size:13pt;">{{ strtoupper($rpp->kurikulum ?? 'Kurikulum Merdeka') }}</span></div>
    <div class="cover-subject">{{ strtoupper($rpp->mata_pelajaran) }}</div>
    <div class="cover-semester">
        Semester {{ $rpp->semester ?? 'Ganjil' }} : Tahun Ajaran {{ $tahunAjaran }}
    </div>

    <div class="cover-garuda">
        <img src="{{ $garudaSrc }}" alt="Garuda Pancasila">
    </div>

    <div class="cover-author-label">Disusun oleh:</div>
    <div class="cover-author-name">{{ $rpp->nama_guru }}</div>
</div>

{{-- =============================================================
     KATA PENGANTAR
     ============================================================= --}}
<div class="kata-pengantar-body">
    <div class="page-title">Kata Pengantar</div>

    <p>
        Puji syukur kehadirat Tuhan Yang Maha Esa atas segala rahmat dan karunia-Nya sehingga
        {{ $docName }} dengan topik <em>"{{ $rpp->topik }}"</em> ini dapat diselesaikan dengan baik.
        RPPM ini disusun sebagai salah satu perangkat pembelajaran untuk mendukung proses
        pembelajaran yang lebih bermakna bagi peserta didik dalam memahami materi
        {{ strtolower($rpp->mata_pelajaran) }}.
    </p>
    <p>
        Melalui pembelajaran ini, peserta didik diharapkan mampu mengembangkan kompetensi sesuai
        dengan tujuan pembelajaran yang telah ditetapkan. Selain itu, peserta didik juga diharapkan
        mampu menumbuhkan sikap kritis, reflektif, serta mampu menerapkan nilai-nilai yang dipelajari
        dalam kehidupan sehari-hari, bermasyarakat, berbangsa, dan bernegara.
    </p>
    @if($isKBC)
    <p>
        RPPM ini disusun berdasarkan Kurikulum Berbasis Cinta (KBC) Kementerian Agama yang
        mengintegrasikan nilai-nilai cinta, yaitu cinta kepada Allah dan Rasul-Nya, cinta ilmu,
        cinta lingkungan, cinta diri dan sesama manusia, serta cinta tanah air. Integrasi nilai-nilai
        tersebut diharapkan mampu membentuk peserta didik yang berakhlak mulia dan moderat dalam
        beragama.
    </p>
    @else
    <p>
        RPPM ini juga mengintegrasikan dimensi Profil Pelajar Pancasila yang meliputi beriman
        dan bertakwa kepada Tuhan Yang Maha Esa, mandiri, bergotong royong, bernalar kritis, kreatif,
        serta berkebinekaan global. Integrasi dimensi tersebut diharapkan mampu mendukung
        pengembangan karakter peserta didik secara holistik, tidak hanya dalam aspek kognitif tetapi
        juga dalam aspek sikap dan keterampilan.
    </p>
    @endif
    @if(isset($content['integrasi_panca_cinta']) || isset($content['integrasi_adiwiyata']))
    <p>
        Selain itu, RPPM ini memuat
        @if(isset($content['integrasi_panca_cinta']))integrasi nilai-nilai Panca Cinta @endif
        @if(isset($content['integrasi_panca_cinta']) && isset($content['integrasi_adiwiyata'])) serta @endif
        @if(isset($content['integrasi_adiwiyata']))integrasi program Adiwiyata (Sekolah Peduli dan Berbudaya Lingkungan)@endif
        yang dihubungkan secara kontekstual dengan topik pembelajaran, sehingga penanaman karakter
        dan kepedulian lingkungan menyatu dalam aktivitas belajar peserta didik.
    </p>
    @endif
    <p>
        Ucapan terima kasih disampaikan kepada berbagai pihak yang telah memberikan dukungan,
        masukan, serta bimbingan dalam penyusunan RPPM ini. Semoga RPPM ini dapat
        memberikan manfaat dalam mendukung proses pembelajaran
        {{ strtolower($rpp->mata_pelajaran) }} yang lebih bermakna dan kontekstual.
    </p>

    <div class="kata-pengantar-signature">
        <p>{{ $kotaDok }}, {{ $tanggalDok }}</p>
        <div class="space"></div>
        <p><strong>{{ $rpp->nama_guru }}</strong></p>
    </div>
</div>

{{-- =============================================================
     DAFTAR ISI
     ============================================================= --}}
<div>
    <div class="page-title">Daftar Isi</div>
    <table class="daftar-isi">
        <tr>
            <td style="width:55%;">Kata Pengantar</td>
            <td class="dots"></td>
            <td class="page-col">ii</td>
        </tr>
        <tr>
            <td>Daftar Isi</td>
            <td class="dots"></td>
            <td class="page-col">iii</td>
        </tr>
        <tr class="level-1">
            <td>{{ $docName }}</td>
            <td class="dots"></td>
            <td class="page-col">1</td>
        </tr>
        @php
            $tocPage = 1;
            $tocItems = [];
            $tocItems[] = 'Informasi Umum';
            if (!empty($content['kompetensi_awal'])) $tocItems[] = 'Kompetensi Awal';
            if ($isKBC && isset($content['nilai_nilai_cinta'])) $tocItems[] = 'Nilai-Nilai Cinta';
            if ($isKBC && isset($content['profil_lulusan_madrasah'])) $tocItems[] = 'Profil Lulusan Madrasah';
            if ($isKBC && isset($content['moderasi_beragama'])) $tocItems[] = 'Moderasi Beragama';
            if (isset($content['profil_pelajar_pancasila'])) $tocItems[] = 'Profil Pelajar Pancasila';
            if (isset($content['sarana_prasarana'])) $tocItems[] = 'Sarana dan Prasarana';
            if (isset($content['tujuan_pembelajaran'])) $tocItems[] = 'Tujuan Pembelajaran';
            if (!empty($content['pemahaman_bermakna'])) $tocItems[] = 'Pemahaman Bermakna';
            if (isset($content['pertanyaan_pemantik'])) $tocItems[] = 'Pertanyaan Pemantik';
            if (isset($content['kegiatan_pembelajaran'])) $tocItems[] = 'Kegiatan Pembelajaran';
            if (isset($content['asesmen'])) $tocItems[] = 'Asesmen Pembelajaran';
            if (isset($content['pengayaan_remedial'])) $tocItems[] = 'Pengayaan dan Remedial';
            if (isset($content['refleksi']) || isset($content['refleksi_guru'])) $tocItems[] = 'Refleksi';
            if (isset($content['integrasi_panca_cinta'])) $tocItems[] = 'Integrasi Panca Cinta';
            if (isset($content['integrasi_adiwiyata'])) $tocItems[] = 'Integrasi Adiwiyata';
            if (isset($content['integrasi_kka'])) $tocItems[] = 'Integrasi Koding & Kecerdasan Artifisial';
            if (isset($content['glosarium'])) $tocItems[] = 'Glosarium';
            if (isset($content['daftar_pustaka'])) $tocItems[] = 'Daftar Pustaka';
            $tocLetter = 'A';
        @endphp
        @foreach($tocItems as $i => $item)
        <tr class="level-2">
            <td>{{ $tocLetter++ }}. {{ $item }}</td>
            <td class="dots"></td>
            <td class="page-col">{{ 1 + intdiv($i, 2) }}</td>
        </tr>
        @endforeach
        @if(isset($content['lkpd']))
        <tr class="level-1">
            <td>Lampiran : Lembar Kerja Peserta Didik (LKPD)</td>
            <td class="dots"></td>
            <td class="page-col">{{ 2 + intdiv(count($tocItems), 2) }}</td>
        </tr>
        @endif
    </table>
</div>

{{-- =============================================================
     MODUL AJAR UTAMA
     ============================================================= --}}
<div class="page-break">
    <div class="page-title">{{ $docName }}</div>

    <table class="tbl-info">
        <tr><td>Nama Satuan Pendidikan</td><td>: {{ $schoolName }}</td></tr>
        <tr><td>Kelas/Fase</td><td>: {{ $rpp->kelas ?: '-' }} / Fase {{ $rpp->fase }}</td></tr>
        <tr><td>Tahun Pelajaran</td><td>: {{ $tahunAjaran }}</td></tr>
        <tr><td>Mata Pelajaran</td><td>: {{ $rpp->mata_pelajaran }}</td></tr>
    </table>

    {{-- INFORMASI UMUM --}}
    <div class="section-letter">{{ $huruf++ }}. Informasi Umum</div>
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:30%;">Komponen</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr><td class="label-cell">Nama Penyusun</td><td>{{ $rpp->nama_guru }}</td></tr>
            <tr><td class="label-cell">Topik / Materi</td><td>{{ $rpp->topik }}</td></tr>
            <tr><td class="label-cell">Semester</td><td>{{ $rpp->semester ?? '-' }}</td></tr>
            <tr><td class="label-cell">Alokasi Waktu</td><td>{{ $rpp->alokasi_waktu }}{{ $rpp->jumlah_pertemuan ? ' (' . $rpp->jumlah_pertemuan . ' pertemuan)' : '' }}</td></tr>
            <tr><td class="label-cell">Model Pembelajaran</td><td>{{ $rpp->model_pembelajaran }}</td></tr>
            <tr><td class="label-cell">Kurikulum</td><td>{{ $rpp->kurikulum ?? 'Kurikulum Merdeka' }}</td></tr>
            <tr><td class="label-cell">Jenis Asesmen</td><td>{{ $rpp->jenis_asesmen ?? 'Formatif dan Sumatif' }}</td></tr>
            @if($rpp->target_peserta_didik)
            <tr><td class="label-cell">Target Peserta Didik</td><td>{{ $rpp->target_peserta_didik }}</td></tr>
            @endif
        </tbody>
    </table>

    {{-- KOMPETENSI AWAL --}}
    @if(!empty($content['kompetensi_awal']))
    <div class="section-letter">{{ $huruf++ }}. Kompetensi Awal</div>
    <table class="tbl-red">
        <tbody>
            <tr class="row-head"><td>Kompetensi Awal Peserta Didik</td></tr>
            <tr><td>{{ $content['kompetensi_awal'] }}</td></tr>
        </tbody>
    </table>
    @endif

    {{-- NILAI-NILAI CINTA (KBC) --}}
    @if($isKBC && isset($content['nilai_nilai_cinta']))
    <div class="section-letter">{{ $huruf++ }}. Nilai-Nilai Cinta (Kurikulum Berbasis Cinta)</div>
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:32%;">Dimensi Cinta</th>
                <th>Deskripsi Pengembangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($content['nilai_nilai_cinta'] as $nilai)
            @if(is_array($nilai))
            <tr>
                <td class="label-cell">{{ $nilai['dimensi'] ?? '-' }}</td>
                <td>{{ $nilai['deskripsi'] ?? '-' }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- PROFIL LULUSAN MADRASAH (KBC) --}}
    @if($isKBC && isset($content['profil_lulusan_madrasah']))
    <div class="section-letter">{{ $huruf++ }}. Profil Lulusan Madrasah</div>
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:32%;">Dimensi</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($content['profil_lulusan_madrasah'] as $profil)
            @if(is_array($profil))
            <tr>
                <td class="label-cell">{{ $profil['dimensi'] ?? '-' }}</td>
                <td>{{ $profil['deskripsi'] ?? '-' }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- MODERASI BERAGAMA (KBC) --}}
    @if($isKBC && isset($content['moderasi_beragama']))
    <div class="section-letter">{{ $huruf++ }}. Moderasi Beragama (Wasathiyah)</div>
    <table class="tbl-red">
        <tbody>
            @if(!empty($content['moderasi_beragama']['nilai_wasathiyah']))
            <tr class="row-head"><td>Nilai Wasathiyah</td></tr>
            <tr><td>{{ $content['moderasi_beragama']['nilai_wasathiyah'] }}</td></tr>
            @endif
            @if(!empty($content['moderasi_beragama']['implementasi']))
            <tr class="row-head"><td>Implementasi dalam Pembelajaran</td></tr>
            <tr>
                <td>
                    <ol style="margin-bottom:0;">
                        @foreach($content['moderasi_beragama']['implementasi'] as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ol>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
    @endif

    {{-- PROFIL PELAJAR PANCASILA --}}
    @if(isset($content['profil_pelajar_pancasila']))
    <div class="section-letter">{{ $huruf++ }}. Profil Pelajar Pancasila</div>
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:32%;">Dimensi</th>
                <th>Deskripsi Pengembangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($content['profil_pelajar_pancasila'] as $profil)
            @if(is_array($profil))
            <tr>
                <td class="label-cell">{{ $profil['dimensi'] ?? '-' }}</td>
                <td>{{ $profil['deskripsi'] ?? '-' }}</td>
            </tr>
            @else
            <tr><td colspan="2">{{ $profil }}</td></tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- SARANA DAN PRASARANA --}}
    @if(isset($content['sarana_prasarana']) && is_array($content['sarana_prasarana']))
    <div class="section-letter">{{ $huruf++ }}. Sarana dan Prasarana</div>
    <table class="tbl-red">
        <tbody>
            @foreach(['alat' => 'Alat', 'bahan' => 'Bahan', 'media' => 'Media', 'sumber_belajar' => 'Sumber Belajar'] as $key => $label)
            @if(!empty($content['sarana_prasarana'][$key]))
            <tr>
                <td class="label-cell">{{ $label }}</td>
                <td>{{ is_array($content['sarana_prasarana'][$key]) ? implode(', ', $content['sarana_prasarana'][$key]) : $content['sarana_prasarana'][$key] }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- TUJUAN PEMBELAJARAN --}}
    @if(isset($content['tujuan_pembelajaran']))
    <div class="section-letter">{{ $huruf++ }}. Tujuan Pembelajaran</div>
    <table class="tbl-red">
        <tbody>
            <tr class="row-head"><td style="width:10%;">No</td><td>Tujuan Pembelajaran</td></tr>
            @foreach($content['tujuan_pembelajaran'] as $i => $tujuan)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $tujuan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- PEMAHAMAN BERMAKNA --}}
    @if(!empty($content['pemahaman_bermakna']))
    <div class="section-letter">{{ $huruf++ }}. Pemahaman Bermakna</div>
    <table class="tbl-red">
        <tbody>
            <tr class="row-head"><td>Pemahaman Bermakna</td></tr>
            <tr><td>{{ $content['pemahaman_bermakna'] }}</td></tr>
        </tbody>
    </table>
    @endif

    {{-- PERTANYAAN PEMANTIK --}}
    @if(isset($content['pertanyaan_pemantik']))
    <div class="section-letter">{{ $huruf++ }}. Pertanyaan Pemantik</div>
    <table class="tbl-red">
        <tbody>
            <tr class="row-head"><td style="width:10%;">No</td><td>Pertanyaan</td></tr>
            @foreach($content['pertanyaan_pemantik'] as $i => $pertanyaan)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td>{{ $pertanyaan }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- KEGIATAN PEMBELAJARAN --}}
    @if(isset($content['kegiatan_pembelajaran']))
    <div class="section-letter">{{ $huruf++ }}. Kegiatan Pembelajaran</div>
    <table class="tbl-langkah">
        <thead>
            <tr>
                <th style="width:20%;">Tahap</th>
                <th>Langkah-Langkah Pembelajaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['pendahuluan' => 'Pendahuluan', 'inti' => 'Kegiatan Inti', 'penutup' => 'Penutup'] as $tahapKey => $tahapLabel)
            @php $tahap = $content['kegiatan_pembelajaran'][$tahapKey] ?? null; @endphp
            @if($tahap)
            <tr>
                <td class="col-pengalaman">
                    {{ $tahapLabel }}
                    @if(!empty($tahap['durasi']))
                    <br><span class="durasi">({{ $tahap['durasi'] }})</span>
                    @endif
                </td>
                <td>
                    @if($tahapKey === 'inti' && !empty($tahap['sintaks_model']))
                    <p class="text-bold mb-5">Model: {{ $tahap['sintaks_model'] }}</p>
                    @endif
                    @php
                        $aktivitas = $tahap['aktivitas'] ?? [];
                        $faseSebelumnya = null;
                        $no = 0;
                    @endphp
                    @foreach($aktivitas as $akt)
                        @if(is_array($akt))
                            @if(!empty($akt['fase_sintaks']) && $akt['fase_sintaks'] !== $faseSebelumnya)
                            <div class="fase-sintaks">{{ $akt['fase_sintaks'] }}@if(!empty($akt['durasi'])) <span class="durasi">({{ $akt['durasi'] }})</span>@endif</div>
                            @php $faseSebelumnya = $akt['fase_sintaks']; @endphp
                            @endif
                            @php $no++; @endphp
                            <div style="margin-bottom:6px;">
                                <span class="text-bold">{{ $no }}.</span>
                                Guru: {{ $akt['kegiatan_guru'] ?? '-' }}<br>
                                <span style="padding-left:18px;display:inline-block;">Peserta didik: {{ $akt['kegiatan_siswa'] ?? '-' }}</span>
                            </div>
                        @else
                            @php $no++; @endphp
                            <div style="margin-bottom:6px;"><span class="text-bold">{{ $no }}.</span> {{ $akt }}</div>
                        @endif
                    @endforeach
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- ASESMEN --}}
    @if(isset($content['asesmen']))
    <div class="section-letter">{{ $huruf++ }}. Asesmen Pembelajaran</div>
    <table class="tbl-red">
        <tbody>
            <tr>
                <td class="label-cell">Jenis Asesmen</td>
                <td>{{ $content['asesmen']['jenis'] ?? ($rpp->jenis_asesmen ?? '-') }}</td>
            </tr>
            <tr>
                <td class="label-cell">Teknik Asesmen</td>
                <td>{{ is_array($content['asesmen']['teknik'] ?? null) ? implode(', ', $content['asesmen']['teknik']) : ($content['asesmen']['teknik'] ?? '-') }}</td>
            </tr>
            @if(!empty($content['asesmen']['bentuk']))
            <tr>
                <td class="label-cell">Bentuk Asesmen</td>
                <td>{{ $content['asesmen']['bentuk'] }}</td>
            </tr>
            @endif
        </tbody>
    </table>

    @if(!empty($content['asesmen']['instrumen']))
    <p class="text-bold mt-10 mb-5">Instrumen Asesmen</p>
    @foreach($content['asesmen']['instrumen'] as $instrumen)
    @if(is_array($instrumen))
    <div style="margin-bottom:10px;padding:10px 12px;border:1px solid #ddd;background:#fcfcfc;page-break-inside:avoid;">
        <p class="text-bold text-red">{{ $instrumen['jenis'] ?? 'Instrumen' }}</p>
        @if(!empty($instrumen['deskripsi']))
        <p>{{ $instrumen['deskripsi'] }}</p>
        @endif
        @if(!empty($instrumen['contoh_soal']))
        <p class="text-bold mt-5 mb-5">Contoh Soal/Tugas:</p>
        <ol style="margin-bottom:0;">
            @foreach($instrumen['contoh_soal'] as $soal)
            <li>{{ $soal }}</li>
            @endforeach
        </ol>
        @endif
    </div>
    @endif
    @endforeach
    @endif

    @php $rubrikData = $content['asesmen']['rubrik_penilaian'] ?? $content['asesmen']['rubrik'] ?? []; @endphp
    @if(is_array($rubrikData) && count($rubrikData) > 0)
    <p class="text-bold mt-10 mb-5">Rubrik Penilaian</p>
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:20%;">Kriteria</th>
                <th>Sangat Baik (4)</th>
                <th>Baik (3)</th>
                <th>Cukup (2)</th>
                <th>Perlu Perbaikan (1)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rubrikData as $rubrik)
            @if(is_array($rubrik))
            <tr>
                <td class="label-cell">{{ $rubrik['kriteria'] ?? '-' }}</td>
                <td>{{ $rubrik['skor_4'] ?? '-' }}</td>
                <td>{{ $rubrik['skor_3'] ?? '-' }}</td>
                <td>{{ $rubrik['skor_2'] ?? '-' }}</td>
                <td>{{ $rubrik['skor_1'] ?? '-' }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif
    @endif

    {{-- PENGAYAAN & REMEDIAL --}}
    @if(isset($content['pengayaan_remedial']))
    <div class="section-letter">{{ $huruf++ }}. Pengayaan dan Remedial</div>
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:18%;">Program</th>
                <th style="width:30%;">Sasaran</th>
                <th>Kegiatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach(['pengayaan' => 'Pengayaan', 'remedial' => 'Remedial'] as $progKey => $progLabel)
            @php $prog = $content['pengayaan_remedial'][$progKey] ?? null; @endphp
            @if($prog)
            <tr>
                <td class="label-cell">{{ $progLabel }}</td>
                <td>{{ $prog['sasaran'] ?? '-' }}</td>
                <td>
                    <ol style="margin-bottom:0;">
                        @foreach($prog['kegiatan'] ?? [] as $kegiatan)
                        <li>{{ $kegiatan }}</li>
                        @endforeach
                    </ol>
                </td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- REFLEKSI --}}
    @if(isset($content['refleksi']) || isset($content['refleksi_guru']))
    <div class="section-letter">{{ $huruf++ }}. Refleksi</div>
    <table class="tbl-red">
        <tbody>
            @if(!empty($content['refleksi']['refleksi_siswa']))
            <tr class="row-head"><td>Refleksi Peserta Didik</td></tr>
            <tr>
                <td>
                    <ol style="margin-bottom:0;">
                        @foreach($content['refleksi']['refleksi_siswa'] as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ol>
                </td>
            </tr>
            @endif
            @php $refleksiGuru = $content['refleksi']['refleksi_guru'] ?? $content['refleksi_guru'] ?? []; @endphp
            @if(!empty($refleksiGuru))
            <tr class="row-head"><td>Refleksi Guru</td></tr>
            <tr>
                <td>
                    <ol style="margin-bottom:0;">
                        @foreach($refleksiGuru as $item)
                        <li>{{ $item }}</li>
                        @endforeach
                    </ol>
                </td>
            </tr>
            @endif
        </tbody>
    </table>
    @endif

    {{-- INTEGRASI PANCA CINTA --}}
    @if(isset($content['integrasi_panca_cinta']))
    <div class="section-letter">{{ $huruf++ }}. Integrasi Panca Cinta</div>
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:32%;">Nilai Panca Cinta</th>
                <th>Implementasi dalam Pembelajaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($content['integrasi_panca_cinta'] as $item)
            @if(is_array($item))
            <tr>
                <td class="label-cell">{{ $item['nilai'] ?? '-' }}</td>
                <td>{{ $item['implementasi'] ?? '-' }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- INTEGRASI ADIWIYATA --}}
    @if(isset($content['integrasi_adiwiyata']))
    <div class="section-letter">{{ $huruf++ }}. Integrasi Adiwiyata</div>
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:32%;">Komponen Adiwiyata</th>
                <th>Kegiatan / Aksi Nyata</th>
            </tr>
        </thead>
        <tbody>
            @foreach($content['integrasi_adiwiyata'] as $item)
            @if(is_array($item))
            <tr>
                <td class="label-cell">{{ $item['komponen'] ?? '-' }}</td>
                <td>{{ $item['kegiatan'] ?? '-' }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- INTEGRASI KKA (KODING & KECERDASAN ARTIFISIAL) --}}
    @if(isset($content['integrasi_kka']))
    <div class="section-letter">{{ $huruf++ }}. Integrasi Koding &amp; Kecerdasan Artifisial (KKA)</div>
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:32%;">Aspek KKA</th>
                <th>Implementasi dalam Pembelajaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($content['integrasi_kka'] as $item)
            @if(is_array($item))
            <tr>
                <td class="label-cell">{{ $item['aspek'] ?? '-' }}</td>
                <td>{{ $item['implementasi'] ?? '-' }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- GLOSARIUM --}}
    @if(isset($content['glosarium']) && count($content['glosarium']) > 0)
    <div class="section-letter">{{ $huruf++ }}. Glosarium</div>
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:28%;">Istilah</th>
                <th>Definisi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($content['glosarium'] as $item)
            @if(is_array($item))
            <tr>
                <td class="label-cell">{{ $item['istilah'] ?? '-' }}</td>
                <td>{{ $item['definisi'] ?? '-' }}</td>
            </tr>
            @endif
            @endforeach
        </tbody>
    </table>
    @endif

    {{-- DAFTAR PUSTAKA --}}
    @if(isset($content['daftar_pustaka']) && count($content['daftar_pustaka']) > 0)
    <div class="section-letter">{{ $huruf++ }}. Daftar Pustaka</div>
    <ol>
        @foreach($content['daftar_pustaka'] as $pustaka)
        <li>{{ $pustaka }}</li>
        @endforeach
    </ol>
    @endif

    {{-- TANDA TANGAN --}}
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td style="width:50%;"></td>
                <td style="width:50%;"><p>{{ $kotaDok }}, {{ $tanggalDok }}</p></td>
            </tr>
            <tr>
                <td style="width:50%;">
                    <p>Mengetahui,</p>
                    <p>Kepala {{ $schoolName }}</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ $rpp->kepala_sekolah ?: '.................................' }}</p>
                    <p class="signature-nip">NIP. {{ $rpp->nip_kepala_sekolah ?: '.................................' }}</p>
                </td>
                <td style="width:50%;">
                    <p>Guru Mata Pelajaran</p>
                    <p>{{ $rpp->mata_pelajaran }}</p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ $rpp->nama_guru }}</p>
                    <p class="signature-nip">NIP. -</p>
                </td>
            </tr>
        </table>
    </div>
</div>

{{-- =============================================================
     LAMPIRAN : LKPD
     ============================================================= --}}
@if(isset($content['lkpd']))
@php $lkpd = $content['lkpd']; @endphp
<div class="page-break">
    <div class="page-title">Lampiran</div>
    <p class="text-bold mb-10">Lampiran 1 : Lembar Kerja Peserta Didik (LKPD)</p>

    <div class="lkpd-wrapper">
        <div class="lkpd-header">
            <div class="lkpd-title">{{ $lkpd['judul'] ?? 'LEMBAR KERJA PESERTA DIDIK' }}</div>
            <div style="font-size:10pt;color:#666;margin-top:4px;">{{ $rpp->mata_pelajaran }} — Fase {{ $rpp->fase }}</div>
        </div>

        <table class="tbl-info" style="margin-bottom:15px;">
            <tr><td style="width:20%;">Nama</td><td>: ................................................</td></tr>
            <tr><td>Kelas</td><td>: ................................................</td></tr>
            <tr><td>Tanggal</td><td>: ................................................</td></tr>
        </table>

        @if(!empty($lkpd['tujuan']))
        <p class="text-bold mt-10 mb-5">Tujuan Kegiatan:</p>
        <p>{{ $lkpd['tujuan'] }}</p>
        @endif

        @php $petunjuk = $lkpd['petunjuk_umum'] ?? $lkpd['petunjuk_pengerjaan'] ?? $lkpd['petunjuk'] ?? []; @endphp
        @if(count($petunjuk) > 0)
        <p class="text-bold mt-10 mb-5">Petunjuk Pengerjaan:</p>
        <ol>
            @foreach($petunjuk as $p)
            <li>{{ $p }}</li>
            @endforeach
        </ol>
        @endif

        @foreach($lkpd['kegiatan'] ?? [] as $keg)
        <div style="margin-top:15px;padding:12px;border:1px solid #ddd;background:#fcfcfc;page-break-inside:avoid;">
            <p class="text-bold text-red">Kegiatan {{ $keg['nomor'] ?? $loop->iteration }}: {{ $keg['judul_kegiatan'] ?? $keg['judul'] ?? '' }}</p>
            @if(!empty($keg['petunjuk']))
            <p><em>{{ $keg['petunjuk'] }}</em></p>
            @endif
            @foreach($keg['soal_tugas'] ?? [] as $soal)
            <div style="margin-top:8px;">
                <div>{{ $soal['nomor'] ?? '' }}. {{ $soal['pertanyaan'] ?? '' }}</div>
                <div class="jawaban-box">Jawaban:</div>
            </div>
            @endforeach
            @foreach($keg['pertanyaan'] ?? [] as $i => $pq)
            <div style="margin-top:8px;">
                <div>{{ $i + 1 }}. {{ $pq }}</div>
                <div class="jawaban-box">Jawaban:</div>
            </div>
            @endforeach
        </div>
        @endforeach

        @if(!empty($lkpd['kesimpulan']))
        <p class="text-bold mt-15 mb-5">Kesimpulan:</p>
        <p>{{ $lkpd['kesimpulan'] }}</p>
        <div class="jawaban-box" style="min-height:70px;">Tulis kesimpulanmu di sini:</div>
        @endif
    </div>
</div>
@endif

@if($print ?? false)
<script>window.addEventListener('load', function () { window.print(); });</script>
@endif
</body>
</html>
