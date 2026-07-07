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
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.55;
            color: #1a1a1a;
            background: #ffffff;
        }

        /* Repeating corner styles using fixed positioning for all pages */
        .decor-header {
            position: fixed; top: -2.5cm; left: -2.5cm; right: -2.5cm; height: 100px; z-index: -1;
        }
        .decor-footer {
            position: fixed; bottom: -2.5cm; left: -2.5cm; right: -2.5cm; height: 100px; z-index: -1;
        }

        /* Top right red corner */
        .decor-header::after {
            content: ""; position: absolute; top: 0; right: 0;
            width: 150px; height: 150px;
            background: linear-gradient(225deg, #b91c1c 50%, transparent 50%);
        }
        
        /* Bottom left yellow/red corner */
        .decor-footer::before {
            content: ""; position: absolute; bottom: 0; left: 0;
            width: 150px; height: 150px;
            background: linear-gradient(45deg, #eab308 30%, #7f1d1d 30%, #7f1d1d 50%, transparent 50%);
        }

        /* Bottom right red corner */
        .decor-footer::after {
            content: ""; position: absolute; bottom: 0; right: 0;
            width: 100px; height: 100px;
            background: linear-gradient(315deg, #b91c1c 50%, transparent 50%);
        }

        /* Top left dots (simplified to a red box for DomPDF compatibility if bg-image fails) */
        .decor-header::before {
            content: ""; position: absolute; top: 30px; left: 30px;
            width: 30px; height: 30px;
            background: radial-gradient(circle, #b91c1c 30%, transparent 40%);
            background-size: 10px 10px;
        }

        /* ============== COVER PAGE ============== */
        .cover {
            page-break-after: always;
            text-align: center;
            padding: 30px 20px 40px;
            position: relative;
            background: #ffffff; /* Hide background decorations on cover */
            height: 100%;
            z-index: 10;
        }
        
        /* Cover Corners */
        .cover::before {
            content: ""; position: absolute; top: -2.5cm; left: -2.5cm;
            width: 120px; height: 120px;
            background: linear-gradient(135deg, #7f1d1d 50%, transparent 50%);
        }
        .cover::after {
            content: ""; position: absolute; bottom: -2.5cm; right: -2.5cm;
            width: 120px; height: 120px;
            background: linear-gradient(315deg, #7f1d1d 50%, transparent 50%);
        }

        .cover-inner-tl {
            position: absolute; top: -2.5cm; left: -2.5cm; width: 100%; height: 100%; z-index: -1;
        }
        .cover-inner-tl::before {
            content: ""; position: absolute; top: 0; right: 0;
            width: 180px; height: 180px;
            background: linear-gradient(225deg, #b91c1c 40%, transparent 40%);
        }
        .cover-inner-tl::after {
            content: ""; position: absolute; bottom: 0; left: 0;
            width: 180px; height: 180px;
            background: linear-gradient(45deg, #b91c1c 40%, transparent 40%);
        }

        .cover-school-logo { margin-bottom: 8px; margin-top: 60px; position: relative; z-index: 2;}
        .cover-school-logo img { max-height: 80px; max-width: 80px; }
        .cover-school-name { font-size: 11pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #111; position: relative; z-index: 2;}
        .cover-school-sub { font-size: 9pt; color: #555; letter-spacing: 1px; margin-bottom: 30px; position: relative; z-index: 2;}
        
        .cover-title-main { font-size: 22pt; font-weight: bold; text-transform: uppercase; line-height: 1.2; margin: 40px 0 8px; letter-spacing: 1px; color: #374151; position: relative; z-index: 2;}
        .cover-subject { font-size: 32pt; font-weight: bold; color: #eab308; text-transform: uppercase; letter-spacing: 1px; margin: 10px 0; text-shadow: 1px 1px 2px rgba(0,0,0,0.1); position: relative; z-index: 2;}
        .cover-topic { font-size: 16pt; font-weight: bold; color: #b91c1c; text-transform: uppercase; margin-bottom: 10px; padding: 0 25px; position: relative; z-index: 2;}
        .cover-semester { font-size: 12pt; color: #333; margin-bottom: 30px; position: relative; z-index: 2;}
        
        .cover-garuda { margin: 25px auto; width: 150px; height: 150px; position: relative; z-index: 2;}
        .cover-garuda img { width: 100%; height: auto; }
        
        .cover-author-label { font-size: 11pt; color: #333; margin-top: 15px; margin-bottom: 8px; position: relative; z-index: 2;}
        .cover-author-name { font-size: 16pt; font-weight: bold; color: #b91c1c; position: relative; z-index: 2;}

        /* ============== TITLES ============== */
        .page-title { text-align: center; font-size: 14pt; font-weight: bold; margin-bottom: 20px; margin-top: 5px; color: #111;}
        .section-letter { font-size: 12pt; font-weight: bold; margin: 18px 0 8px; }
        .subsection-num { font-weight: bold; font-size: 11pt; margin: 14px 0 6px; }
        
        /* Table Headers for deep learning */
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; background: white;}
        th, td { border: 1px solid #666; padding: 6px 9px; vertical-align: top; font-size: 10.5pt; line-height: 1.5; }
        
        .tbl-red thead th, .tbl-red tbody .row-head td { background-color: #b91c1c; color: #ffffff; font-weight: bold; text-align: center; border: 1px solid #b91c1c; }
        .tbl-red tbody .row-sub td { background-color: #f5f5f5; font-weight: bold; }
        .tbl-info td { border: none; padding: 3px 0; background: transparent;}
        
        .tbl-langkah th { background-color: #b91c1c; color: #ffffff; text-align: center; font-weight: bold; border: 1px solid #b91c1c; }
        .tbl-langkah .col-pengalaman { width: 22%; text-align: center; vertical-align: middle; background-color: #fff6f6; font-weight: bold; color: #b91c1c; font-size: 13pt; }
        .tbl-langkah .phase-header { background-color: #f0f0f0; font-weight: bold; text-align: center; font-style: italic; }

        /* Standard Table styling */
        .section-header { background: #b91c1c; color: white; padding: 10px 20px; font-size: 12pt; font-weight: bold; border-radius: 6px 6px 0 0; margin-bottom: 0; }
        .section-body { border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 6px 6px; padding: 20px; background: #ffffff; margin-bottom: 25px; }
        .info-table-content { width: 100%; border-collapse: collapse; }
        .info-table-content td { padding: 12px 15px; border-bottom: 1px solid #e5e7eb; }
        .info-table-content td:first-child { width: 35%; font-weight: 600; color: #374151; background: #f3f4f6; }

        /* Activity Boxes */
        .activity-box { margin-bottom: 20px; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden; background: white; }
        .activity-header { background: linear-gradient(90deg, #f3f4f6, #e5e7eb); padding: 12px 20px; font-weight: bold; color: #1f2937; }
        .activity-content { padding: 15px 20px; }

        /* Helpers */
        .kata-pengantar-body { page-break-after: always; position: relative; z-index: 2;}
        .daftar-isi { width: 100%; border-collapse: collapse; page-break-after: always; position: relative; z-index: 2;}
    </style>
    
</head>
<body>
<div class="decor-header"></div>
<div class="decor-footer"></div>

@php
    $content = $rpp->content_result ?? [];
    $identifikasi = $content['identifikasi'] ?? [];
    $desain = $content['desain_pembelajaran'] ?? [];
    $langkah = $content['langkah_pembelajaran'] ?? [];
    $asesmen = $content['asesmen'] ?? [];
    $lampiran = $content['lampiran'] ?? [];
    $diagKog = $asesmen['diagnostik_kognitif'] ?? [];
    $diagNon = $asesmen['diagnostik_non_kognitif'] ?? [];
    $formatif = $asesmen['formatif'] ?? [];
    $sumatif = $asesmen['sumatif'] ?? [];

    $p1 = $langkah['pertemuan_1'] ?? [];
    $p2 = $langkah['pertemuan_2'] ?? [];
    $p3 = $langkah['pertemuan_3'] ?? [];

    $c1s = count($p1['stimulasi'] ?? []);
    $c1i = count($p1['identifikasi_masalah'] ?? []);
    $c1p = count($p1['pengumpulan_data'] ?? []);
    $c2a = count($p2['pengolahan_data_1'] ?? []);
    $c2b = count($p2['pengolahan_data_2'] ?? []);
    $c3a = count($p3['pembuktian'] ?? []);

    $startIdentifikasi = $c1s + 1;
    $startPengumpulan = $c1s + $c1i + 1;
    $startP2a = $c1s + $c1i + $c1p + 1;
    $startP2b = $startP2a + $c2a;
    $startP3a = $startP2b + $c2b;
    $startP3b = $startP3a + $c3a;

    $hasAwal = !empty($diagKog) || !empty($diagNon);
    $hasProses = !empty($formatif);
    $hasAkhir = !empty($sumatif);

    $schoolName = $schoolSettings->nama_sekolah ?? 'SEKOLAH';
    $schoolCity = $schoolSettings->kota ?? '';
    $tahunAjaran = date('Y') . '/' . (date('Y') + 1);
@endphp

{{-- =============================================================
     COVER PAGE
     ============================================================= --}}

<div class="cover">
    <div class="cover-inner-tl"></div>
    @php
        $schoolName = $schoolSettings->nama_sekolah ?? 'NAMA SEKOLAH';
        $schoolCity = $schoolSettings->kota ?? '';
        $tahunAjaran = date('Y') . '/' . (date('Y') + 1);
    @endphp

    @if(isset($schoolSettings) && $schoolSettings->logo)
    <div class="cover-school-logo">
        <img src="{{ ($print ?? false) ? asset('storage/' . $schoolSettings->logo) : storage_path('app/public/' . $schoolSettings->logo) }}" alt="Logo">
    </div>
    @endif
    <div class="cover-school-name">{{ strtoupper($schoolName) }}</div>
    @if($schoolCity)
    <div class="cover-school-sub">{{ strtoupper($schoolCity) }}</div>
    @endif

    <div class="cover-title-main">RENCANA PELAKSANAAN<br>PEMBELAJARAN MENDALAM</div>
    <div class="cover-subject">{{ strtoupper($rpp->mata_pelajaran) }}</div>
    <div class="cover-topic">{{ strtoupper($rpp->topik) }}</div>
    <div class="cover-semester">
        Semester {{ $rpp->semester ?? 'Ganjil' }} : Tahun Ajaran {{ $tahunAjaran }}
    </div>

    <div class="cover-garuda">
        <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAACWCAYAAAA8AXHiAAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEwAACxMBAJqcGAAABwBJREFUeJzt3D1uG0cUx/HfsrtACnAFXKE4uXADd+68hF3wLqBcJ5fgIrgL2IWvkFtYLvwCqQIFsAL4AgYEGwUWO7Q7c4aP/T8gEESQ+Gg+zO7OLg/LsiwbY6xp+8v+BvB34w2h3gO2yX8QagNgbYQ6x3rF2gC1P63Z/x2vWP718/D3X4B/Y/5/f9rfD/nQ9yPUL1+s/v2b1S+/Wf36W/PveO3+O94Y1gbAOtcR8H/O7Z+t//jN+u+/rf/yxfovX6z/+tX6t9/Mv+ONB1aP93Xq9QyQ1h1eYy32b7+Zf/vN/FtvWCMNrcd7hPplR8D9m/Vf/zBvA2uMh0K9b94O1hh2rF/sCKh/m/U33zBvCGsMU6wP7AgY/jBvDmsMN6x37Ag4/MO8QawxXLE+syOglR+rMW8SawxnrH/YEdDrP8ybxRrDGetXOwJ6+wdYc1hjmLB+tyOgp3+ANYs1homP7Qg4/mF/Yc1izWEl1nf+Ata/wQYLaw4rsb73O4D1M9hcWHNYifWh3wGsd8FGC2sOK7EO/Q5gvQs2WlhzWIn10O8A1stgow1gNceUWA/9DmDdDDbWQFZzTIn12O8A1r1gYw1kNUdVrMd+B7DuBRtsIKs5qmI99TuAdSnYYANZzVEV66nfAawLwQYayGqOqljP/Q5gXQg20EBWc1TFuu93AOtc2L4GsprjJtZ9vwNYZ8L2NZDVHDexnvodwDoRtq+BrOa4ifXU7wDWSdi+BrKa4ybWU78DWCdh+xrIao6bWE/9DmCdCOvXUFZz3MR67HcA61RYv4aymuMm1mO/A1inwvo1lNUcN7Hu+x3AOhPWryGs5riJ9djvANapsH4NYTXHTazHfgewToX1awirOSpivfQ7gHUtbF8DWc1REeul3wGsa2H7GshqjopYL/0OYF0L29dAVnNUxHrtdwDrWti+BrKaoyLWa78DWNfC9jWQ1RwVsV77HcC6FravgazmqIj12u8A1rWwfQ1kNUdFrNd+B7CuhW1rIatZzWEl1g+/A1g/gm0V1hxWYv3wO4D1I9hWYc1hJdYPvwNYP4JtFdYcVmL98DuA9SPYVmHNYf4e66XfAaxrYZvFmsPEe6yXfgeuroVtFmsOE++xXvodwLoWtimsOVxsQ7/7qF6HnwnbE9YcLvah34Gqa2GbwZrDxT70O1B1LWwzWHO42Kd+B6iuhW0Gaw4X+9TvQNW1sM1gzeFin/odoLoWthmsOVzsU78DVdfCNoM1h4t96neA6lrYZrDmcLFP/Q5QXQvbDNYcLvap34Gqa2GbwZrDxT71O1B1LWwzWHO42Kd+B6quhW0Gaw4X+9TvQNW1sM1gzeFin/odoLoWthmsOVzsU78DVdfCNoM1hwl7yC5P/Q5cnQvbLNYcJuzS20u/A1bnwjaLNYf5t/T21O/A1bmwzWLNYcX21O/A1bmwzWLNYcX21O/A1bmwzWLNYcX21O/A1bmwzWLNYcX21O/A1bmwzWLNYcX23O9A1bmwvf2HNYf5d8Z67ncA61zYvgaomuMm1kO/A1hnwtY1RNUcN7Hu+x3AOhO2riGq5riJdd/vANaZsH0NUTXHTaz7fvepjoUta4iqOW5i3fe7T3UsrF9DVM1xE+u+332qY2H9GqJqjptY9/3uUx0L69cQVXPcxLrvd5/qWFi/hqia4ybWfb/7VMfC+jVE1Rw3sR773ac6FdavIarmqIj12O8+1amwXQ1R1RwVsR773ac6FTaqIaq2oSLWQ7/7VKfCRjVE1TZUxHrtd5/qWtiphqi6jopYL/3uU10L+9QQVddREeul332qa2GfGqLqOipivfS7T3Ut7FNDVF1HRayXfveproV9aoiq66iI9dbvPtW1sE8NUXUdFbFe+t2nuha2qSGqrqMi1ku/+1TXwjY1RNV1VMR66Xef6lrYpoaouo6KWC/97lNdC9vUEFXXsRbr5d+Bq9fCdjVE1TUU4r38O3D1Wtiuhqi6hiK5vvw7cPVa2K6GqLqGIrle/h24ei1sV0NUXUORXC//Dly9FrapoSjXVCTXy78DV6+FbWooam41yeVnALZ3/1d4vRa2qaGouV2TXH4GYHvnfwW3r4XtaihqbndN8vwMwPYO/wp/Xwvb1VBU3O6a5PkZgO3t/RVevha2q6EouD0U6/wMwPZ2/gpP18J2NRS1tj0V6/wMwPZ2/gpP18L2NBS1tz0V6/wMwPZ2/gpP18L2tJ269r4X6/wMwPZ2/goP18I2tZ2q9kHFWudnALa381d4uBa2qS2o7aFiXZ8B2N7uX+HhWtinLlfXQ8W6PgOwvZ2/wsO1sE9dpq6HinV9BmB7O3+Fh2tjn7pEXYti/fsHAFW181dY+A9sA4/XzXw9EAAAAABJRU5ErkJggg==" alt="Garuda">
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
        Rencana Pelaksanaan Pembelajaran Mendalam Mendalam (RPPM) dengan judul
        <em>"{{ $rpp->topik }}"</em> ini dapat diselesaikan dengan baik. RPPM ini disusun sebagai
        salah satu perangkat pembelajaran untuk mendukung proses pembelajaran yang lebih bermakna dan
        mendalam bagi peserta didik dalam memahami materi {{ strtolower($rpp->mata_pelajaran) }}.
    </p>
    <p>
        Melalui pembelajaran ini, peserta didik diharapkan mampu mengembangkan kompetensi sesuai
        dengan capaian pembelajaran yang telah ditetapkan. Selain itu, peserta didik juga diharapkan
        mampu menumbuhkan sikap kritis, reflektif, serta mampu menerapkan nilai-nilai yang dipelajari
        dalam kehidupan bermasyarakat, berbangsa, dan bernegara.
    </p>
    <p>
        Rencana Pelaksanaan Pembelajaran Mendalam Mendalam ini juga mengintegrasikan Kompetensi Sosial
        Emosional yang mengacu pada kerangka CASEL <em>(Collaborative for Academic, Social, and
        Emotional Learning)</em>. Kompetensi tersebut meliputi lima aspek utama, yaitu kesadaran diri
        <em>(self-awareness)</em>, pengelolaan diri <em>(self-management)</em>, kesadaran sosial
        <em>(social awareness)</em>, keterampilan berhubungan <em>(relationship skills)</em>, serta
        pengambilan keputusan yang bertanggung jawab <em>(responsible decision-making)</em>.
        Integrasi kompetensi ini diharapkan mampu mendukung pengembangan karakter peserta didik
        secara holistik, tidak hanya dalam aspek kognitif tetapi juga dalam aspek sosial dan emosional.
    </p>
    <p>
        Melalui RPPM ini, diharapkan proses pembelajaran tidak hanya berfokus pada penguasaan materi,
        tetapi juga mampu membentuk peserta didik yang memiliki sikap kritis, reflektif, serta mampu
        mengambil nilai-nilai luhur sebagai pedoman dalam kehidupan sehari-hari. Ucapan terima kasih
        disampaikan kepada berbagai pihak yang telah memberikan dukungan, masukan, serta bimbingan
        dalam penyusunan Rencana Pelaksanaan Pembelajaran Mendalam Mendalam ini. Semoga RPPM ini dapat
        memberikan manfaat dalam mendukung proses pembelajaran yang lebih bermakna dan kontekstual.
    </p>

    <div class="kata-pengantar-signature">
        <p>
            {{ $schoolCity ?: '.............' }},
            {{ $rpp->tanggal ? \Carbon\Carbon::parse($rpp->tanggal)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
        </p>
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
            <td style="width:45%;">Kata Pengantar</td>
            <td class="dots"></td>
            <td class="page-col">ii</td>
        </tr>
        <tr>
            <td>Daftar Isi</td>
            <td class="dots"></td>
            <td class="page-col">iii</td>
        </tr>
        <tr class="level-1">
            <td>Rencana Pelaksanaan Pembelajaran Mendalam Mendalam (RPPM)</td>
            <td class="dots"></td>
            <td class="page-col">1</td>
        </tr>
        <tr class="level-2">
            <td>A. Identifikasi</td>
            <td class="dots"></td>
            <td class="page-col">1</td>
        </tr>
        <tr class="level-2">
            <td>B. Desain Pembelajaran</td>
            <td class="dots"></td>
            <td class="page-col">1</td>
        </tr>
        <tr class="level-2">
            <td>C. Langkah-Langkah Pembelajaran</td>
            <td class="dots"></td>
            <td class="page-col">2</td>
        </tr>
        <tr class="level-2">
            <td>D. Asesmen Pembelajaran</td>
            <td class="dots"></td>
            <td class="page-col">5</td>
        </tr>
        @if($hasAwal)
        <tr class="level-3">
            <td>1. Asesmen pada Awal Pembelajaran</td>
            <td class="dots"></td>
            <td class="page-col">5</td>
        </tr>
        @endif
        @if($hasProses)
        <tr class="level-3">
            <td>2. Asesmen pada Proses Pembelajaran</td>
            <td class="dots"></td>
            <td class="page-col">10</td>
        </tr>
        @endif
        @if($hasAkhir)
        <tr class="level-3">
            <td>3. Asesmen pada Akhir Pembelajaran</td>
            <td class="dots"></td>
            <td class="page-col">15</td>
        </tr>
        @endif
        <tr class="level-1">
            <td>Lampiran</td>
            <td class="dots"></td>
            <td class="page-col">16</td>
        </tr>
        @if(!empty($lampiran['lkpd']))
        <tr class="level-2">
            <td>Lampiran 1 : LKPD</td>
            <td class="dots"></td>
            <td class="page-col">16</td>
        </tr>
        @endif
        @if(!empty($lampiran['materi_ajar']))
        <tr class="level-2">
            <td>Lampiran 2 : Materi Ajar</td>
            <td class="dots"></td>
            <td class="page-col">17</td>
        </tr>
        @endif
    </table>
</div>

{{-- =============================================================
     RPPM UTAMA
     ============================================================= --}}
<div class="page-break">
    <div class="page-title">Rencana Pelaksanaan Pembelajaran Mendalam Mendalam (RPPM)</div>

    <table class="tbl-info">
        <tr><td>Nama Satuan Pendidikan</td><td>: {{ $schoolName }}</td></tr>
        <tr><td>Kelas/Fase</td><td>: {{ $rpp->kelas ?? '' }} / Fase {{ $rpp->fase }}</td></tr>
        <tr><td>Tahun Pelajaran</td><td>: {{ $tahunAjaran }}</td></tr>
        <tr><td>Mata Pelajaran</td><td>: {{ $rpp->mata_pelajaran }}</td></tr>
    </table>

    {{-- A. IDENTIFIKASI --}}
    <div class="section-letter">A. Identifikasi</div>
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:25%;">Identifikasi</th>
                <th>Uraian</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="label-cell">Peserta Didik</td>
                <td>{{ $identifikasi['peserta_didik'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Materi Pelajaran</td>
                <td>{{ $identifikasi['materi_pelajaran'] ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label-cell" style="vertical-align:middle;">Profil Lulusan</td>
                <td style="padding:0;">
                    @if(!empty($identifikasi['profil_lulusan']))
                    <table style="margin:0;border:none;width:100%;">
                        @foreach($identifikasi['profil_lulusan'] as $pl)
                        <tr>
                            <td style="border:none;padding:4px 9px;width:18%;font-weight:bold;">{{ $pl['kode'] ?? '' }}</td>
                            <td style="border:none;padding:4px 9px;">{{ $pl['nama'] ?? '' }}</td>
                            <td style="border:none;padding:4px 9px;text-align:center;width:10%;color:#cc0000;font-weight:bold;font-size:13pt;">
                                {!! !empty($pl['dipilih']) ? 'V' : '' !!}
                            </td>
                        </tr>
                        @endforeach
                    </table>
                    @else
                    <div style="padding:6px 9px;">-</div>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>

    {{-- B. DESAIN PEMBELAJARAN --}}
    <div class="section-letter">B. Desain Pembelajaran</div>
    <table class="tbl-red">
        <tbody>
            <tr class="row-head"><td colspan="2">Capaian Pembelajaran</td></tr>
            <tr><td colspan="2">{{ $desain['capaian_pembelajaran'] ?? '-' }}</td></tr>

            <tr class="row-head"><td colspan="2">Topik Pembelajaran</td></tr>
            <tr><td colspan="2" style="text-align:center;">{{ $desain['topik'] ?? $rpp->topik }}</td></tr>

            @if(!empty($desain['lintas_disiplin']))
            <tr class="row-head"><td colspan="2">Lintas Disiplin Ilmu</td></tr>
            <tr>
                <td colspan="2">
                    @foreach($desain['lintas_disiplin'] as $ld)
                    <div style="margin-bottom:3px;">{{ $ld }}</div>
                    @endforeach
                </td>
            </tr>
            @endif

            <tr class="row-head">
                <td colspan="2">Tujuan Pembelajaran akan dilaksanakan dalam waktu: {{ $rpp->alokasi_waktu }}</td>
            </tr>
        </tbody>
    </table>

    @if(!empty($desain['tujuan_pembelajaran']))
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:50%;">Tujuan Pembelajaran</th>
                <th>Topik Pembelajaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($desain['tujuan_pembelajaran'] as $tp)
            <tr>
                <td><strong>{{ $tp['kode'] ?? '' }}.</strong> {{ $tp['tujuan'] ?? '' }}</td>
                <td><strong>{{ $tp['kode'] ?? '' }}.</strong> {{ $tp['topik'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    <table class="tbl-red">
        <tbody>
            @if(!empty($desain['praktik_pedagogis']))
            <tr>
                <td class="label-cell" style="vertical-align:middle;">Praktik Pedagogis</td>
                <td>
                    <div>Model {{ $desain['praktik_pedagogis']['model'] ?? '' }}</div>
                    <div>Metode {{ $desain['praktik_pedagogis']['metode'] ?? '' }}</div>
                    <div>Pendekatan {{ $desain['praktik_pedagogis']['pendekatan'] ?? '' }}</div>
                </td>
            </tr>
            @endif
            @if(!empty($desain['kemitraan']))
            <tr>
                <td class="label-cell" style="vertical-align:middle;">Kemitraan Pembelajaran</td>
                <td>
                    @foreach($desain['kemitraan'] as $i => $km)
                    <div>{{ $i + 1 }}. {{ $km }}</div>
                    @endforeach
                </td>
            </tr>
            @endif
            @if(!empty($desain['lingkungan_pembelajaran']))
            <tr>
                <td class="label-cell" style="vertical-align:middle;">Lingkungan Pembelajaran</td>
                <td>
                    @foreach($desain['lingkungan_pembelajaran'] as $i => $lp)
                    <div>{{ $i + 1 }}. {{ $lp }}</div>
                    @endforeach
                </td>
            </tr>
            @endif
            @if(!empty($desain['pemanfaatan_digital']))
            <tr>
                <td class="label-cell" style="vertical-align:middle;">Pemanfaatan Digital</td>
                <td>
                    @foreach($desain['pemanfaatan_digital'] as $i => $pd)
                    <div>{{ $i + 1 }}. {{ $pd }}</div>
                    @endforeach
                </td>
            </tr>
            @endif
        </tbody>
    </table>
</div>

{{-- =============================================================
     C. LANGKAH-LANGKAH PEMBELAJARAN
     ============================================================= --}}
<div class="page-break">
    <div class="section-letter">C. Langkah-Langkah Pembelajaran</div>
    <table class="tbl-langkah">
        <thead>
            <tr>
                <th style="width:22%;">Pengalaman Belajar</th>
                <th>Langkah-Langkah Pembelajaran</th>
            </tr>
        </thead>
        <tbody>
            {{-- PEMBUKA --}}
            <tr>
                <td colspan="2" class="phase-header">
                    Awal (Berkesadaran, Menggembirakan) Alokasi {{ $langkah['pembuka']['alokasi'] ?? '10 Menit' }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <ul class="activity-list">
                        @foreach($langkah['pembuka']['aktivitas'] ?? [] as $akt)
                        <li>
                            {{ $akt['kegiatan'] ?? '' }}
                            @if(!empty($akt['kse']))<span class="kse-tag">({{ $akt['kse'] }})</span>@endif
                        </li>
                        @endforeach
                    </ul>
                </td>
            </tr>

            {{-- INTI HEADER --}}
            <tr>
                <td colspan="2" class="phase-header">
                    Inti (Bermakna, Berkesadaran, Menggembirakan) {{ $rpp->alokasi_waktu }}
                </td>
            </tr>

            {{-- PERTEMUAN 1 - MEMAHAMI --}}
            @if(!empty($p1))
            <tr>
                <td class="col-pengalaman" rowspan="3">Memahami</td>
                <td>
                    <div style="font-weight:bold;margin-bottom:4px;">Pertemuan 1</div>
                    @foreach($p1['tujuan_pembelajaran'] ?? [] as $tp)
                    <div style="font-size:10pt;">{{ $tp }}</div>
                    @endforeach

                    <div class="sub-section" style="margin-top:10px;">Stimulasi</div>
                    <ol start="1">
                        @foreach($p1['stimulasi'] ?? [] as $s)
                        <li>
                            {{ $s['kegiatan'] ?? '' }}
                            @if(!empty($s['kse']))<span class="kse-tag">({{ $s['kse'] }})</span>@endif
                        </li>
                        @endforeach
                    </ol>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="sub-section">Identifikasi Masalah</div>
                    <ol start="{{ $startIdentifikasi }}">
                        @foreach($p1['identifikasi_masalah'] ?? [] as $s)
                        <li>
                            {{ $s['kegiatan'] ?? '' }}
                            @if(!empty($s['kse']))<span class="kse-tag">({{ $s['kse'] }})</span>@endif
                        </li>
                        @endforeach
                    </ol>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="sub-section">Pengumpulan Data</div>
                    <ol start="{{ $startPengumpulan }}">
                        @foreach($p1['pengumpulan_data'] ?? [] as $s)
                        <li>
                            {{ $s['kegiatan'] ?? '' }}
                            @if(!empty($s['kse']))<span class="kse-tag">({{ $s['kse'] }})</span>@endif
                        </li>
                        @endforeach
                    </ol>
                </td>
            </tr>
            @endif

            {{-- PERTEMUAN 2 - MENGAPLIKASI --}}
            @if(!empty($p2))
            <tr>
                <td class="col-pengalaman" rowspan="2">Mengaplikasi</td>
                <td>
                    <div style="font-weight:bold;margin-bottom:4px;">Pertemuan 2</div>
                    @foreach($p2['tujuan_pembelajaran'] ?? [] as $tp)
                    <div style="font-size:10pt;">{{ $tp }}</div>
                    @endforeach

                    <div class="sub-section" style="margin-top:10px;">Pengolahan Data</div>
                    <ol start="{{ $startP2a }}">
                        @foreach($p2['pengolahan_data_1'] ?? [] as $s)
                        <li>
                            {{ $s['kegiatan'] ?? '' }}
                            @if(!empty($s['kse']))<span class="kse-tag">({{ $s['kse'] }})</span>@endif
                        </li>
                        @endforeach
                    </ol>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="sub-section">Pengolahan Data</div>
                    <ol start="{{ $startP2b }}">
                        @foreach($p2['pengolahan_data_2'] ?? [] as $s)
                        <li>
                            {{ $s['kegiatan'] ?? '' }}
                            @if(!empty($s['kse']))<span class="kse-tag">({{ $s['kse'] }})</span>@endif
                        </li>
                        @endforeach
                    </ol>
                </td>
            </tr>
            @endif

            {{-- PERTEMUAN 3 - MEREFLEKSI --}}
            @if(!empty($p3))
            <tr>
                <td class="col-pengalaman" rowspan="2">Merefleksi</td>
                <td>
                    <div style="font-weight:bold;margin-bottom:4px;">Pertemuan 3</div>
                    @foreach($p3['tujuan_pembelajaran'] ?? [] as $tp)
                    <div style="font-size:10pt;">{{ $tp }}</div>
                    @endforeach

                    <div class="sub-section" style="margin-top:10px;">Pembuktian</div>
                    <ol start="{{ $startP3a }}">
                        @foreach($p3['pembuktian'] ?? [] as $s)
                        <li>
                            {{ $s['kegiatan'] ?? '' }}
                            @if(!empty($s['kse']))<span class="kse-tag">({{ $s['kse'] }})</span>@endif
                        </li>
                        @endforeach
                    </ol>
                </td>
            </tr>
            <tr>
                <td>
                    <div class="sub-section">Penarikan Kesimpulan (Generalisasi)</div>
                    <ol start="{{ $startP3b }}">
                        @foreach($p3['penarikan_kesimpulan'] ?? [] as $s)
                        <li>
                            {{ $s['kegiatan'] ?? '' }}
                            @if(!empty($s['kse']))<span class="kse-tag">({{ $s['kse'] }})</span>@endif
                        </li>
                        @endforeach
                    </ol>
                </td>
            </tr>
            @endif

            {{-- PENUTUP --}}
            <tr>
                <td colspan="2" class="phase-header">
                    Penutup (Bermakna, Berkesadaran) {{ $langkah['penutup']['alokasi'] ?? '10 Menit' }}
                </td>
            </tr>
            <tr>
                <td></td>
                <td>
                    <ol>
                        @foreach($langkah['penutup']['aktivitas'] ?? [] as $akt)
                        <li>
                            {{ $akt['kegiatan'] ?? '' }}
                            @if(!empty($akt['kse']))<span class="kse-tag">({{ $akt['kse'] }})</span>@endif
                        </li>
                        @endforeach
                    </ol>
                    @if(!empty($langkah['penutup']['penugasan']))
                    <div style="margin-top:10px;padding:8px 10px;background:#fff6f6;border-left:3px solid #cc0000;">
                        <em><strong>*Penugasan:</strong> {{ $langkah['penutup']['penugasan'] }}</em>
                    </div>
                    @endif
                </td>
            </tr>
        </tbody>
    </table>
</div>

{{-- =============================================================
     D. ASESMEN PEMBELAJARAN
     ============================================================= --}}
<div class="page-break">
    <div class="section-letter">D. Asesmen Pembelajaran</div>

    @php $asesmenNum = 0; @endphp

    {{-- 1. Asesmen Awal --}}
    @if($hasAwal)
    @php $asesmenNum++; @endphp
    <div class="subsection-num">{{ $asesmenNum }}. Asesmen pada Awal Pembelajaran</div>

    @if(!empty($diagKog))
    <div class="asesmen-banner">Asesmen pada Awal Pembelajaran</div>
    <div class="asesmen-subbanner">Asesmen Diagnostik Kognitif</div>

    @if(!empty($diagKog['deskripsi']))
    <p>{{ $diagKog['deskripsi'] }}</p>
    @endif

    @if(!empty($diagKog['kisi_kisi']))
    <p class="text-bold mt-10 mb-5">Kisi-Kisi Tes Diagnostik Kognitif</p>
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:22%;">Capaian Pembelajaran</th>
                <th style="width:22%;">Tujuan Pembelajaran</th>
                <th>Indikator</th>
                <th style="width:11%;">Level Kognitif</th>
                <th style="width:10%;">Nomor Soal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($diagKog['kisi_kisi'] as $kk)
            <tr>
                <td>{{ $kk['capaian_pembelajaran'] ?? '-' }}</td>
                <td>{{ $kk['tujuan'] ?? '-' }}</td>
                <td>{{ $kk['indikator'] ?? '-' }}</td>
                <td style="text-align:center;">{{ $kk['level_kognitif'] ?? '-' }}</td>
                <td style="text-align:center;">{{ $kk['nomor_soal'] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif

    @if(!empty($diagKog['soal']))
    <p class="text-bold mt-15 mb-5">Lembar Soal Tes Diagnostik</p>
    <table class="tbl-info" style="margin-bottom:8px;">
        <tr>
            <td style="width:50%;">Nama : .........................................</td>
            <td>Kelas : .........................................</td>
        </tr>
    </table>
    <p class="text-bold mb-5">Petunjuk Pengerjaan :</p>
    <ol style="margin-bottom:10px;">
        <li>Bacalah setiap soal dengan cermat sebelum menentukan jawaban.</li>
        <li>Setiap soal memiliki lima alternatif jawaban (A, B, C, D, dan E).</li>
        <li>Pilihlah satu jawaban yang paling tepat.</li>
        <li>Kerjakan soal secara mandiri tanpa melihat buku atau berdiskusi dengan teman.</li>
        <li>Gunakan waktu yang tersedia dengan sebaik-baiknya.</li>
        <li>Periksa kembali jawaban sebelum lembar jawaban dikumpulkan.</li>
    </ol>
    <p class="text-bold mb-5">Jawablah soal berikut ini dengan tepat ...!</p>
    @foreach($diagKog['soal'] as $soal)
    <div class="soal-box">
        <div class="soal-num">{{ $soal['nomor'] ?? '' }}. {{ $soal['pertanyaan'] ?? '' }}</div>
        @if(!empty($soal['stimulus']))
        <div class="soal-stimulus">{{ $soal['stimulus'] }}</div>
        @endif
        <div class="soal-pilihan">
            @foreach($soal['pilihan'] ?? [] as $huruf => $teks)
            <div>{{ $huruf }}. {{ $teks }}</div>
            @endforeach
        </div>
    </div>
    @endforeach
    @endif

    @if(!empty($diagKog['kriteria_penilaian']))
    <p class="text-bold mt-15 mb-5">Kriteria Penilaian</p>
    <table class="tbl-red" style="width:50%;">
        <thead>
            <tr>
                <th>Kategori Jawaban</th>
                <th style="width:25%;">Skor</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Jawaban Benar</td><td style="text-align:center;">1</td></tr>
            <tr><td>Jawaban Salah</td><td style="text-align:center;">0</td></tr>
        </tbody>
    </table>
    <p class="mb-5"><strong>Rumus Nilai:</strong> {{ $diagKog['kriteria_penilaian']['rumus'] ?? 'Jumlah Jawaban Benar / Total Soal x 100' }}</p>

    @if(!empty($diagKog['kriteria_penilaian']['kategori']))
    <p class="text-bold mt-10 mb-5">Kategori Hasil Tes Diagnostik</p>
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:18%;">Rentang Nilai</th>
                <th style="width:22%;">Kategori</th>
                <th>Interpretasi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($diagKog['kriteria_penilaian']['kategori'] as $kat)
            <tr>
                <td style="text-align:center;">{{ $kat['rentang'] ?? '' }}</td>
                <td style="text-align:center;font-weight:bold;">{{ $kat['kategori'] ?? '' }}</td>
                <td>{{ $kat['deskripsi'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    @endif
    @endif

    @if(!empty($diagNon))
    <div class="asesmen-banner" style="margin-top:20px;">Asesmen pada Awal Pembelajaran</div>
    <div class="asesmen-subbanner">Asesmen Diagnostik Non-Kognitif — {{ $diagNon['jenis'] ?? 'Gaya Belajar' }}</div>

    @if(!empty($diagNon['deskripsi']))
    <p>{{ $diagNon['deskripsi'] }}</p>
    @endif
    @if(!empty($diagNon['instrumen']))
    <p><strong>Instrumen:</strong> {{ $diagNon['instrumen'] }}</p>
    @endif

    @if(!empty($diagNon['rekomendasi']))
    <p class="text-bold mt-10 mb-5">Rekomendasi Strategi Pembelajaran</p>
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:22%;">Gaya Belajar</th>
                <th>Rekomendasi Pembelajaran</th>
            </tr>
        </thead>
        <tbody>
            @foreach($diagNon['rekomendasi'] as $rek)
            <tr>
                <td style="font-weight:bold;text-align:center;">{{ $rek['gaya'] ?? '' }}</td>
                <td>{{ $rek['strategi'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    @endif
    @endif

    {{-- 2. Asesmen Proses --}}
    @if($hasProses)
    @php $asesmenNum++; @endphp
    <div class="subsection-num" style="margin-top:20px;">{{ $asesmenNum }}. Asesmen pada Proses Pembelajaran</div>

    <div class="asesmen-banner">Asesmen pada Proses Pembelajaran</div>
    <div class="asesmen-subbanner">
        Asesmen Formatif — Pertemuan ke-{{ $formatif['pertemuan'] ?? 2 }} (Assessment For Learning)<br>
        <span style="font-weight:normal;font-style:italic;">Penilaian Diskusi Kelompok</span>
    </div>

    @if(!empty($formatif['indikator']))
    <p class="text-bold mb-5">Indikator Penilaian:</p>
    <ol style="margin-bottom:10px;">
        @foreach($formatif['indikator'] as $ind)
        <li>{{ $ind }}</li>
        @endforeach
    </ol>
    @endif

    @if(!empty($formatif['rubrik_diskusi']))
    <p class="text-bold mt-10 mb-5">Rubrik Penilaian Diskusi</p>
    <table class="tbl-rubrik">
        <thead>
            <tr>
                <th style="width:5%;">No</th>
                <th style="width:20%;">Aspek</th>
                <th>Skor 4</th>
                <th>Skor 3</th>
                <th>Skor 2</th>
                <th>Skor 1</th>
            </tr>
        </thead>
        <tbody>
            @foreach($formatif['rubrik_diskusi'] as $i => $rb)
            <tr>
                <td style="text-align:center;">{{ $i + 1 }}</td>
                <td class="aspek-cell">{{ $rb['aspek'] ?? '' }}</td>
                <td>{{ $rb['skor_4'] ?? '' }}</td>
                <td>{{ $rb['skor_3'] ?? '' }}</td>
                <td>{{ $rb['skor_2'] ?? '' }}</td>
                <td>{{ $rb['skor_1'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p class="mb-5"><strong>Teknik Penilaian:</strong> {{ $formatif['teknik_penilaian_diskusi'] ?? 'Penilaian = Perolehan Skor x 5' }}</p>

    @if(!empty($formatif['interval_diskusi']))
    <p class="text-bold mt-5 mb-5">Pedoman Pengambilan Keputusan</p>
    <table class="tbl-red" style="width:55%;">
        <thead>
            <tr>
                <th style="width:35%;">Interval Nilai</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($formatif['interval_diskusi'] as $iv)
            <tr>
                <td style="text-align:center;">{{ $iv['rentang'] ?? '' }}</td>
                <td>{{ $iv['keterangan'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    @endif

    @if(!empty($formatif['rubrik_produk']))
    <p class="text-bold mt-15 mb-5">Kriteria Penilaian Produk</p>
    <table class="tbl-rubrik">
        <thead>
            <tr>
                <th style="width:5%;">No</th>
                <th style="width:18%;">Kriteria</th>
                <th>Skor 5</th>
                <th>Skor 4</th>
                <th>Skor 3</th>
                <th>Skor 2</th>
                <th>Skor 1</th>
            </tr>
        </thead>
        <tbody>
            @foreach($formatif['rubrik_produk'] as $i => $rp)
            <tr>
                <td style="text-align:center;">{{ $i + 1 }}</td>
                <td class="aspek-cell">{{ $rp['kriteria'] ?? '' }}</td>
                <td>{{ $rp['skor_5'] ?? '' }}</td>
                <td>{{ $rp['skor_4'] ?? '' }}</td>
                <td>{{ $rp['skor_3'] ?? '' }}</td>
                <td>{{ $rp['skor_2'] ?? '' }}</td>
                <td>{{ $rp['skor_1'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p class="mb-5"><strong>Teknik Penilaian:</strong> Penilaian = Jumlah Skor : 20 x 100</p>
    @endif

    @if(!empty($formatif['rubrik_presentasi']))
    <p class="text-bold mt-15 mb-5">Kriteria Penilaian Presentasi</p>
    <table class="tbl-rubrik">
        <thead>
            <tr>
                <th style="width:25%;">Aspek</th>
                <th>Skor 3</th>
                <th>Skor 2</th>
                <th>Skor 1</th>
            </tr>
        </thead>
        <tbody>
            @foreach($formatif['rubrik_presentasi'] as $rp)
            <tr>
                <td class="aspek-cell">{{ $rp['aspek'] ?? '' }}</td>
                <td>{{ $rp['skor_3'] ?? '' }}</td>
                <td>{{ $rp['skor_2'] ?? '' }}</td>
                <td>{{ $rp['skor_1'] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <p class="mb-5"><strong>Teknik Penilaian:</strong> Penilaian = Jumlah Skor : 12 x 100</p>

    @if(!empty($formatif['interval_kelompok']))
    <p class="text-bold mt-10 mb-5">Interval Penilaian Kelompok</p>
    <table class="tbl-red">
        <thead>
            <tr>
                <th style="width:18%;">Interval Nilai</th>
                <th style="width:22%;">Kategori</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @php
            $keteranganKelompok = [
                '86-100' => 'Menunjukkan penguasaan sangat baik terhadap aspek yang dinilai serta mampu bekerja secara optimal dalam kelompok.',
                '71-85' => 'Menunjukkan kemampuan yang baik namun masih terdapat beberapa aspek yang perlu ditingkatkan.',
                '56-70' => 'Menunjukkan kemampuan dasar tetapi masih memerlukan penguatan pada beberapa aspek.',
                '0-55' => 'Menunjukkan kemampuan yang masih rendah sehingga memerlukan pendampingan dan latihan lebih lanjut.',
            ];
            @endphp
            @foreach($formatif['interval_kelompok'] as $iv)
            <tr>
                <td style="text-align:center;">{{ $iv['rentang'] ?? '' }}</td>
                <td style="text-align:center;font-weight:bold;">{{ $iv['kategori'] ?? '' }}</td>
                <td>{{ $keteranganKelompok[$iv['rentang'] ?? ''] ?? '' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    @endif
    @endif

    {{-- 3. Asesmen Akhir --}}
    @if($hasAkhir)
    @php $asesmenNum++; @endphp
    <div class="subsection-num" style="margin-top:20px;">{{ $asesmenNum }}. Asesmen pada Akhir Pembelajaran</div>

    <div class="asesmen-banner">Asesmen pada Akhir Pembelajaran</div>
    <div class="asesmen-subbanner">Asesmen Sumatif (Assessment Of Learning)</div>

    <div style="border:1px solid #ccc;padding:12px 15px;margin-bottom:10px;background:#fafafa;">
        <p style="text-align:justify;">
            {{ $sumatif['deskripsi'] ?? 'Asesmen pada akhir pembelajaran dilaksanakan untuk mengukur ketercapaian seluruh tujuan pembelajaran secara menyeluruh.' }}
        </p>
        @if(!empty($sumatif['waktu']))
        <p class="mt-5"><strong>Waktu Pelaksanaan:</strong> {{ $sumatif['waktu'] }}</p>
        @endif
        @if(!empty($sumatif['bentuk']))
        <p class="mt-5"><strong>Bentuk:</strong> {{ $sumatif['bentuk'] }}</p>
        @endif
    </div>

    @if(!empty($sumatif['kisi_kisi']))
    <p class="text-bold mt-10 mb-5">Kisi-Kisi Asesmen Sumatif</p>
    <table class="tbl-red">
        <thead>
            <tr>
                <th>Tujuan Pembelajaran</th>
                <th>Indikator</th>
                <th style="width:12%;">Level</th>
                <th style="width:12%;">Nomor Soal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sumatif['kisi_kisi'] as $kk)
            <tr>
                <td>{{ $kk['tujuan'] ?? '-' }}</td>
                <td>{{ $kk['indikator'] ?? '-' }}</td>
                <td style="text-align:center;">{{ $kk['level'] ?? '-' }}</td>
                <td style="text-align:center;">{{ $kk['nomor'] ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
    @endif

    {{-- Tanda Tangan --}}
    <div class="signature-section">
        <p style="text-align:right;margin-bottom:15px;">
            {{ $rpp->kota ?: $schoolCity ?: '.....................' }},
            {{ $rpp->tanggal ? \Carbon\Carbon::parse($rpp->tanggal)->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}
        </p>
        <table class="signature-table">
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
     LAMPIRAN 1: LKPD
     ============================================================= --}}
@php $lkpd = $lampiran['lkpd'] ?? []; @endphp
@if(!empty($lkpd))
<div class="page-break">
    <div class="page-title">Lampiran</div>
    <p class="text-bold">Lampiran 1 : LKPD</p>

    <div class="lkpd-wrapper">
        <div class="lkpd-header">
            <div class="lkpd-title">{{ $lkpd['judul'] ?? 'LEMBAR KERJA PESERTA DIDIK' }}</div>
            <div style="font-size:10pt;color:#666;margin-top:4px;">{{ $rpp->mata_pelajaran }} — Fase {{ $rpp->fase }}</div>
        </div>

        <table class="tbl-info" style="margin-bottom:15px;">
            <tr><td style="width:20%;">Nama</td><td>: ................................................</td></tr>
            <tr><td>Kelas</td><td>: ................................................</td></tr>
            <tr><td>Kelompok</td><td>: ................................................</td></tr>
            <tr><td>Tanggal</td><td>: ................................................</td></tr>
        </table>

        @if(!empty($lkpd['tujuan']))
        <p class="text-bold mt-10 mb-5">Tujuan Kegiatan:</p>
        <p>{{ $lkpd['tujuan'] }}</p>
        @endif

        @if(!empty($lkpd['petunjuk']))
        <p class="text-bold mt-10 mb-5">Petunjuk Pengerjaan:</p>
        <ol>
            @foreach($lkpd['petunjuk'] as $p)
            <li>{{ $p }}</li>
            @endforeach
        </ol>
        @endif

        @foreach($lkpd['kegiatan'] ?? [] as $keg)
        <div style="margin-top:15px;padding:12px;border:1px solid #ddd;background:#fcfcfc;page-break-inside:avoid;">
            <p class="text-bold text-red">Kegiatan {{ $keg['nomor'] ?? '' }}: {{ $keg['judul'] ?? '' }}</p>
            @if(!empty($keg['petunjuk']))
            <p><em>{{ $keg['petunjuk'] }}</em></p>
            @endif
            @foreach($keg['pertanyaan'] ?? [] as $i => $pq)
            <div style="margin-top:8px;">
                <div>{{ $i + 1 }}. {{ $pq }}</div>
                <div class="jawaban-box">Jawaban:</div>
            </div>
            @endforeach
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- =============================================================
     LAMPIRAN 2: MATERI AJAR
     ============================================================= --}}
@php $materi = $lampiran['materi_ajar'] ?? []; @endphp
@if(!empty($materi))
<div class="page-break">
    <p class="text-bold">Lampiran 2 : Materi Ajar</p>

    <div class="materi-section" style="margin-top:10px;">
        @if(!empty($materi['pendahuluan']))
        <div class="materi-sub">Pendahuluan</div>
        <p>{{ $materi['pendahuluan'] }}</p>
        @endif

        @foreach($materi['sub_materi'] ?? [] as $sm)
        <div class="materi-sub">{{ $sm['judul'] ?? '' }}</div>
        <p>{{ $sm['konten'] ?? '' }}</p>
        @endforeach

        @if(!empty($materi['referensi']))
        <div class="materi-sub">Referensi</div>
        <ol>
            @foreach($materi['referensi'] as $ref)
            <li>{{ $ref }}</li>
            @endforeach
        </ol>
        @endif
    </div>
</div>
@endif

@if($print ?? false)
<script>window.addEventListener('load', function () { window.print(); });</script>
@endif
</body>
</html>
