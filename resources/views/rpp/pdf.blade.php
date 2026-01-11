<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modul Ajar - {{ $rpp->mata_pelajaran }}</title>
    <style>
        @page {
            margin: 3cm 2.5cm 2.5cm 3cm; /* atas, kanan, bawah, kiri */
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
            line-height: 1.6;
            color: #1a1a1a;
            background: #fff;
        }

        /* Cover Page */
        .cover {
            text-align: center;
            padding: 40px 30px;
            page-break-after: always;
            min-height: 90vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .cover-border {
            border: 3px solid #27a38a;
            border-radius: 8px;
            padding: 50px 40px;
            margin: 0 auto;
            max-width: 100%;
        }
        .cover h1 {
            font-size: 22pt;
            font-weight: bold;
            color: #27a38a;
            margin-bottom: 8px;
            letter-spacing: 3px;
        }
        .cover h2 {
            font-size: 14pt;
            font-weight: normal;
            color: #374151;
            margin-bottom: 40px;
        }
        .cover .subject-box {
            background: linear-gradient(135deg, #27a38a 0%, #27a38a 100%);
            color: white;
            font-size: 18pt;
            font-weight: bold;
            padding: 25px 40px;
            border-radius: 10px;
            margin: 30px auto;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
        }
        .cover .topic {
            font-size: 14pt;
            color: #374151;
            margin: 25px 0;
            font-style: italic;
        }
        .cover .info-table {
            margin: 40px auto;
            text-align: left;
            border-collapse: collapse;
        }
        .cover .info-table td {
            padding: 8px 15px;
            font-size: 12pt;
        }
        .cover .info-table td:first-child {
            font-weight: bold;
            width: 180px;
        }
        .cover .author {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid #e5e7eb;
        }
        .cover .author p {
            margin: 5px 0;
        }

        /* Content Styling */
        .page-title {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #27a38a;
        }
        .page-title h1 {
            font-size: 16pt;
            color: #27a38a;
            margin-bottom: 5px;
        }
        .page-title h2 {
            font-size: 12pt;
            font-weight: normal;
            color: #6b7280;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-header {
            background: #27a38a;
            color: white;
            padding: 10px 20px;
            font-size: 12pt;
            font-weight: bold;
            border-radius: 6px 6px 0 0;
            margin-bottom: 0;
        }
        .section-body {
            border: 1px solid #e5e7eb;
            border-top: none;
            border-radius: 0 0 6px 6px;
            padding: 20px;
            background: #fafafa;
        }

        /* Tables */
        .info-table-content {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table-content tr {
            border-bottom: 1px solid #e5e7eb;
        }
        .info-table-content tr:last-child {
            border-bottom: none;
        }
        .info-table-content td {
            padding: 12px 15px;
            vertical-align: top;
        }
        .info-table-content td:first-child {
            width: 35%;
            font-weight: 600;
            color: #374151;
            background: #f3f4f6;
        }
        .info-table-content td:last-child {
            background: white;
        }

        /* Lists */
        ol, ul {
            margin-left: 20px;
            padding-left: 10px;
        }
        li {
            margin-bottom: 10px;
            text-align: justify;
            line-height: 1.7;
        }

        /* Activity Boxes */
        .activity-container {
            margin-top: 15px;
        }
        .activity-box {
            margin-bottom: 20px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            overflow: hidden;
            background: white;
        }
        .activity-header {
            background: linear-gradient(90deg, #f3f4f6, #e5e7eb);
            padding: 12px 20px;
            font-weight: bold;
            color: #1f2937;
            display: table;
            width: 100%;
        }
        .activity-header span:first-child {
            display: table-cell;
            text-align: left;
        }
        .activity-header span:last-child {
            display: table-cell;
            text-align: right;
            color: #1e40af;
            font-weight: normal;
        }
        .activity-content {
            padding: 15px 20px;
        }
        .activity-content ul {
            margin: 0;
        }
        .activity-content li {
            margin-bottom: 8px;
        }
        .activity-detail {
            margin-bottom: 12px;
            padding: 10px;
            background: #f9fafb;
            border-radius: 6px;
        }
        .activity-detail p {
            margin: 4px 0;
        }
        .activity-detail strong {
            color: #374151;
        }

        /* Badges */
        .badge-container {
            margin-top: 10px;
        }
        .badge {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 11pt;
            margin: 4px;
            border: 1px solid #93c5fd;
        }
        .profil-item {
            margin-bottom: 15px;
            padding: 12px;
            background: #f0fdf4;
            border-left: 4px solid #27a38a;
            border-radius: 0 6px 6px 0;
        }
        .profil-item h4 {
            color: #27a38a;
            margin-bottom: 5px;
        }

        /* Rubric Table */
        .rubrik-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10pt;
        }
        .rubrik-table th {
            background: #27a38a;
            color: white;
            padding: 12px 10px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #27a38a;
        }
        .rubrik-table td {
            padding: 10px;
            border: 1px solid #d1d5db;
            vertical-align: top;
            text-align: left;
        }
        .rubrik-table tr:nth-child(even) {
            background: #f9fafb;
        }
        .rubrik-table td:first-child {
            font-weight: 600;
            background: #f3f4f6;
            width: 18%;
        }

        /* Signature Section */
        .signature-section {
            margin-top: 60px;
            page-break-inside: avoid;
        }
        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }
        .signature-table td {
            width: 50%;
            text-align: center;
            vertical-align: top;
            padding: 10px 20px;
        }
        .signature-space {
            height: 80px;
        }
        .signature-name {
            font-weight: bold;
            text-decoration: underline;
        }
        .signature-nip {
            font-size: 11pt;
            color: #4b5563;
        }

        /* Two column layout */
        .two-column {
            display: table;
            width: 100%;
        }
        .two-column > div {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }
        .remedial-box {
            background: #fef3c7;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #f59e0b;
        }
        .pengayaan-box {
            background: #d1fae5;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #10b981;
        }

        /* Utility */
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .mt-10 { margin-top: 10px; }
        .mt-15 { margin-top: 15px; }
        .mt-20 { margin-top: 20px; }
        .mb-10 { margin-bottom: 10px; }
    </style>
</head>
<body>
    @php $content = $rpp->content_result ?? []; @endphp

    <!-- ==================== COVER PAGE ==================== -->
    <div class="cover">
        <div class="cover-border">
            <!-- School Header -->
            @if(isset($schoolSettings) && ($schoolSettings->logo || $schoolSettings->nama_sekolah))
            <div style="margin-bottom: 30px;">
                @if($schoolSettings->logo)
                <div style="margin-bottom: 15px;">
                    <img src="{{ storage_path('app/public/' . $schoolSettings->logo) }}" alt="Logo Sekolah" style="max-height: 80px; max-width: 80px; object-fit: contain;">
                </div>
                @endif
                @if($schoolSettings->nama_sekolah)
                <h3 style="font-size: 16pt; font-weight: bold; color: #1f2937; margin-bottom: 5px;">{{ strtoupper($schoolSettings->nama_sekolah) }}</h3>
                @endif
                @if($schoolSettings->npsn)
                <p style="font-size: 11pt; color: #6b7280;">NPSN: {{ $schoolSettings->npsn }}</p>
                @endif
            </div>
            <div style="border-top: 2px solid #27a38a; margin-bottom: 20px;"></div>
            @endif
            
            <h1>MODUL AJAR</h1>
            <h2>{{ $rpp->kurikulum ?? 'Kurikulum Merdeka' }}</h2>
            
            <div class="subject-box">
                {{ strtoupper($rpp->mata_pelajaran) }}
            </div>
            
            <p class="topic">"{{ $rpp->topik }}"</p>
            
            <table class="info-table" style="margin: 30px auto;">
                <tr>
                    <td>Fase / Kelas</td>
                    <td>: {{ $rpp->fase }}{{ $rpp->kelas ? ' / Kelas ' . $rpp->kelas : '' }}</td>
                </tr>
                @if($rpp->semester)
                <tr>
                    <td>Semester</td>
                    <td>: {{ $rpp->semester }}</td>
                </tr>
                @endif
                <tr>
                    <td>Alokasi Waktu</td>
                    <td>: {{ $rpp->alokasi_waktu }}</td>
                </tr>
                <tr>
                    <td>Model Pembelajaran</td>
                    <td>: {{ $rpp->model_pembelajaran }}</td>
                </tr>
            </table>
            
            <div class="author">
                <p><strong>Disusun Oleh:</strong></p>
                <p style="font-size: 14pt; margin-top: 10px;"><strong>{{ $rpp->nama_guru }}</strong></p>
            </div>
        </div>
    </div>

    <!-- ==================== CONTENT PAGES ==================== -->
    
    <div class="page-title">
        <h1>MODUL AJAR</h1>
        <h2>{{ $rpp->kurikulum ?? 'Kurikulum Merdeka' }}</h2>
    </div>

    <!-- A. INFORMASI UMUM -->
    <div class="section">
        <div class="section-header">A. INFORMASI UMUM</div>
        <div class="section-body">
            <table class="info-table-content">
                <tr>
                    <td>Nama Penyusun</td>
                    <td>{{ $rpp->nama_guru }}</td>
                </tr>
                <tr>
                    <td>Mata Pelajaran</td>
                    <td>{{ $rpp->mata_pelajaran }}</td>
                </tr>
                <tr>
                    <td>Fase / Kelas</td>
                    <td>{{ $rpp->fase }}{{ $rpp->kelas ? ' / Kelas ' . $rpp->kelas : '' }}</td>
                </tr>
                @if($rpp->semester)
                <tr>
                    <td>Semester</td>
                    <td>{{ $rpp->semester }}</td>
                </tr>
                @endif
                <tr>
                    <td>Topik / Materi</td>
                    <td>{{ $rpp->topik }}</td>
                </tr>
                <tr>
                    <td>Alokasi Waktu</td>
                    <td>{{ $rpp->alokasi_waktu }}</td>
                </tr>
                @if($rpp->jumlah_pertemuan)
                <tr>
                    <td>Jumlah Pertemuan</td>
                    <td>{{ $rpp->jumlah_pertemuan }} Pertemuan</td>
                </tr>
                @endif
                <tr>
                    <td>Model Pembelajaran</td>
                    <td>{{ $rpp->model_pembelajaran }}</td>
                </tr>
                <tr>
                    <td>Jenis Asesmen</td>
                    <td>{{ $rpp->jenis_asesmen ?? 'Formatif dan Sumatif' }}</td>
                </tr>
                @if($rpp->target_peserta_didik)
                <tr>
                    <td>Target Peserta Didik</td>
                    <td>{{ $rpp->target_peserta_didik }}</td>
                </tr>
                @endif
            </table>
        </div>
    </div>

    <!-- B. KOMPETENSI AWAL -->
    @if(isset($content['kompetensi_awal']) && $content['kompetensi_awal'])
    <div class="section">
        <div class="section-header">B. KOMPETENSI AWAL</div>
        <div class="section-body">
            <p>{{ $content['kompetensi_awal'] }}</p>
        </div>
    </div>
    @endif

    <!-- C. TUJUAN PEMBELAJARAN -->
    @if(isset($content['tujuan_pembelajaran']))
    <div class="section">
        <div class="section-header">C. TUJUAN PEMBELAJARAN</div>
        <div class="section-body">
            <p>Setelah mengikuti pembelajaran ini, peserta didik diharapkan mampu:</p>
            <ol class="mt-15">
                @foreach($content['tujuan_pembelajaran'] as $tujuan)
                <li>{{ $tujuan }}</li>
                @endforeach
            </ol>
        </div>
    </div>
    @endif

    <!-- D. PEMAHAMAN BERMAKNA -->
    @if(isset($content['pemahaman_bermakna']) && $content['pemahaman_bermakna'])
    <div class="section">
        <div class="section-header">D. PEMAHAMAN BERMAKNA</div>
        <div class="section-body">
            <p>{{ $content['pemahaman_bermakna'] }}</p>
        </div>
    </div>
    @endif

    <!-- E. PERTANYAAN PEMANTIK -->
    @if(isset($content['pertanyaan_pemantik']))
    <div class="section">
        <div class="section-header">E. PERTANYAAN PEMANTIK</div>
        <div class="section-body">
            <ol>
                @foreach($content['pertanyaan_pemantik'] as $pertanyaan)
                <li>{{ $pertanyaan }}</li>
                @endforeach
            </ol>
        </div>
    </div>
    @endif

    <!-- F. PROFIL PELAJAR PANCASILA -->
    @if(isset($content['profil_pelajar_pancasila']))
    <div class="section">
        <div class="section-header">F. PROFIL PELAJAR PANCASILA</div>
        <div class="section-body">
            <p class="mb-10">Dimensi Profil Pelajar Pancasila yang dikembangkan:</p>
            @foreach($content['profil_pelajar_pancasila'] as $profil)
                @if(is_array($profil))
                <div class="profil-item">
                    <h4>{{ $profil['dimensi'] ?? '' }}</h4>
                    <p>{{ $profil['deskripsi'] ?? '' }}</p>
                </div>
                @else
                <span class="badge">{{ $profil }}</span>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <!-- G. SARANA DAN PRASARANA -->
    @if(isset($content['sarana_prasarana']))
    <div class="section">
        <div class="section-header">G. SARANA DAN PRASARANA</div>
        <div class="section-body">
            @if(is_array($content['sarana_prasarana']) && isset($content['sarana_prasarana']['alat']))
            <table class="info-table-content">
                @if(isset($content['sarana_prasarana']['alat']) && count($content['sarana_prasarana']['alat']) > 0)
                <tr>
                    <td>Alat</td>
                    <td>{{ implode(', ', $content['sarana_prasarana']['alat']) }}</td>
                </tr>
                @endif
                @if(isset($content['sarana_prasarana']['bahan']) && count($content['sarana_prasarana']['bahan']) > 0)
                <tr>
                    <td>Bahan</td>
                    <td>{{ implode(', ', $content['sarana_prasarana']['bahan']) }}</td>
                </tr>
                @endif
                @if(isset($content['sarana_prasarana']['media']) && count($content['sarana_prasarana']['media']) > 0)
                <tr>
                    <td>Media</td>
                    <td>{{ implode(', ', $content['sarana_prasarana']['media']) }}</td>
                </tr>
                @endif
                @if(isset($content['sarana_prasarana']['sumber_belajar']) && count($content['sarana_prasarana']['sumber_belajar']) > 0)
                <tr>
                    <td>Sumber Belajar</td>
                    <td>{{ implode(', ', $content['sarana_prasarana']['sumber_belajar']) }}</td>
                </tr>
                @endif
            </table>
            @else
            <ul>
                @foreach($content['sarana_prasarana'] as $sarana)
                <li>{{ is_array($sarana) ? json_encode($sarana) : $sarana }}</li>
                @endforeach
            </ul>
            @endif
        </div>
    </div>
    @endif

    <!-- H. KEGIATAN PEMBELAJARAN -->
    @if(isset($content['kegiatan_pembelajaran']))
    <div class="section">
        <div class="section-header">H. KEGIATAN PEMBELAJARAN</div>
        <div class="section-body">
            <div class="activity-container">
                
                @if(isset($content['kegiatan_pembelajaran']['pendahuluan']))
                <div class="activity-box">
                    <div class="activity-header">
                        <span>1. PENDAHULUAN</span>
                        <span>{{ $content['kegiatan_pembelajaran']['pendahuluan']['durasi'] ?? '± 15 menit' }}</span>
                    </div>
                    <div class="activity-content">
                        @php $aktivitasPendahuluan = $content['kegiatan_pembelajaran']['pendahuluan']['aktivitas'] ?? []; @endphp
                        @if(count($aktivitasPendahuluan) > 0 && is_array($aktivitasPendahuluan[0] ?? null))
                            @foreach($aktivitasPendahuluan as $akt)
                            <div class="activity-detail">
                                <p><strong>Kegiatan Guru:</strong> {{ $akt['kegiatan_guru'] ?? '' }}</p>
                                <p><strong>Kegiatan Siswa:</strong> {{ $akt['kegiatan_siswa'] ?? '' }}</p>
                            </div>
                            @endforeach
                        @else
                            <ul>
                                @foreach($aktivitasPendahuluan as $aktivitas)
                                <li>{{ is_array($aktivitas) ? ($aktivitas['kegiatan_guru'] ?? json_encode($aktivitas)) : $aktivitas }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                @endif

                @if(isset($content['kegiatan_pembelajaran']['inti']))
                <div class="activity-box">
                    <div class="activity-header">
                        <span>2. KEGIATAN INTI</span>
                        <span>{{ $content['kegiatan_pembelajaran']['inti']['durasi'] ?? '± 60 menit' }}</span>
                    </div>
                    <div class="activity-content">
                        @if(isset($content['kegiatan_pembelajaran']['inti']['sintaks_model']))
                        <p class="mb-10"><strong>Model:</strong> {{ $content['kegiatan_pembelajaran']['inti']['sintaks_model'] }}</p>
                        @endif
                        @php $aktivitasInti = $content['kegiatan_pembelajaran']['inti']['aktivitas'] ?? []; @endphp
                        @if(count($aktivitasInti) > 0 && is_array($aktivitasInti[0] ?? null))
                            @foreach($aktivitasInti as $akt)
                            <div class="activity-detail">
                                @if(isset($akt['fase_sintaks']))
                                <p><strong>Fase:</strong> {{ $akt['fase_sintaks'] }} {{ isset($akt['durasi']) ? '(' . $akt['durasi'] . ')' : '' }}</p>
                                @endif
                                <p><strong>Kegiatan Guru:</strong> {{ $akt['kegiatan_guru'] ?? '' }}</p>
                                <p><strong>Kegiatan Siswa:</strong> {{ $akt['kegiatan_siswa'] ?? '' }}</p>
                            </div>
                            @endforeach
                        @else
                            <ul>
                                @foreach($aktivitasInti as $aktivitas)
                                <li>{{ is_array($aktivitas) ? ($aktivitas['kegiatan_guru'] ?? json_encode($aktivitas)) : $aktivitas }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                @endif

                @if(isset($content['kegiatan_pembelajaran']['penutup']))
                <div class="activity-box">
                    <div class="activity-header">
                        <span>3. PENUTUP</span>
                        <span>{{ $content['kegiatan_pembelajaran']['penutup']['durasi'] ?? '± 10 menit' }}</span>
                    </div>
                    <div class="activity-content">
                        @php $aktivitasPenutup = $content['kegiatan_pembelajaran']['penutup']['aktivitas'] ?? []; @endphp
                        @if(count($aktivitasPenutup) > 0 && is_array($aktivitasPenutup[0] ?? null))
                            @foreach($aktivitasPenutup as $akt)
                            <div class="activity-detail">
                                <p><strong>Kegiatan Guru:</strong> {{ $akt['kegiatan_guru'] ?? '' }}</p>
                                <p><strong>Kegiatan Siswa:</strong> {{ $akt['kegiatan_siswa'] ?? '' }}</p>
                            </div>
                            @endforeach
                        @else
                            <ul>
                                @foreach($aktivitasPenutup as $aktivitas)
                                <li>{{ is_array($aktivitas) ? ($aktivitas['kegiatan_guru'] ?? json_encode($aktivitas)) : $aktivitas }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                @endif

            </div>
        </div>
    </div>
    @endif

    <!-- I. ASESMEN -->
    @if(isset($content['asesmen']))
    <div class="section">
        <div class="section-header">I. ASESMEN</div>
        <div class="section-body">
            <table class="info-table-content">
                <tr>
                    <td>Jenis Asesmen</td>
                    <td>{{ $content['asesmen']['jenis'] ?? ($rpp->jenis_asesmen ?? '-') }}</td>
                </tr>
                <tr>
                    <td>Teknik Asesmen</td>
                    <td>
                        @if(is_array($content['asesmen']['teknik'] ?? null))
                            {{ implode(', ', $content['asesmen']['teknik']) }}
                        @else
                            {{ $content['asesmen']['teknik'] ?? '-' }}
                        @endif
                    </td>
                </tr>
                @if(isset($content['asesmen']['bentuk']))
                <tr>
                    <td>Bentuk Asesmen</td>
                    <td>{{ $content['asesmen']['bentuk'] }}</td>
                </tr>
                @endif
            </table>

            @if(isset($content['asesmen']['instrumen']))
            <p class="mt-20 text-bold">Instrumen Asesmen:</p>
            @foreach($content['asesmen']['instrumen'] as $instrumen)
                @if(is_array($instrumen))
                <div style="margin: 10px 0; padding: 12px; background: #f9fafb; border-radius: 6px;">
                    <p><strong>{{ $instrumen['jenis'] ?? '' }}</strong></p>
                    <p>{{ $instrumen['deskripsi'] ?? '' }}</p>
                    @if(isset($instrumen['contoh_soal']))
                    <p class="mt-10"><em>Contoh Soal:</em></p>
                    <ol style="margin-left: 15px;">
                        @foreach($instrumen['contoh_soal'] as $soal)
                        <li>{{ $soal }}</li>
                        @endforeach
                    </ol>
                    @endif
                </div>
                @else
                <li>{{ $instrumen }}</li>
                @endif
            @endforeach
            @endif

            @php $rubrikData = $content['asesmen']['rubrik_penilaian'] ?? $content['asesmen']['rubrik'] ?? []; @endphp
            @if(count($rubrikData) > 0)
            <p class="mt-20 text-bold">Rubrik Penilaian:</p>
            <table class="rubrik-table">
                <thead>
                    <tr>
                        <th>Kriteria</th>
                        <th>Sangat Baik (4)</th>
                        <th>Baik (3)</th>
                        <th>Cukup (2)</th>
                        <th>Perlu Perbaikan (1)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rubrikData as $rubrik)
                    <tr>
                        <td>{{ $rubrik['kriteria'] ?? '-' }}</td>
                        <td>{{ $rubrik['skor_4'] ?? '-' }}</td>
                        <td>{{ $rubrik['skor_3'] ?? '-' }}</td>
                        <td>{{ $rubrik['skor_2'] ?? '-' }}</td>
                        <td>{{ $rubrik['skor_1'] ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @endif
        </div>
    </div>
    @endif

    <!-- J. PENGAYAAN & REMEDIAL -->
    @if(isset($content['pengayaan_remedial']))
    <div class="section">
        <div class="section-header">J. PENGAYAAN DAN REMEDIAL</div>
        <div class="section-body">
            <div class="two-column">
                @if(isset($content['pengayaan_remedial']['pengayaan']))
                <div>
                    <div class="pengayaan-box">
                        <h4 style="color: #059669; margin-bottom: 10px;">PENGAYAAN</h4>
                        <p><em>{{ $content['pengayaan_remedial']['pengayaan']['sasaran'] ?? '' }}</em></p>
                        <ul class="mt-10">
                            @foreach($content['pengayaan_remedial']['pengayaan']['kegiatan'] ?? [] as $kegiatan)
                            <li>{{ $kegiatan }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
                @if(isset($content['pengayaan_remedial']['remedial']))
                <div>
                    <div class="remedial-box">
                        <h4 style="color: #d97706; margin-bottom: 10px;">REMEDIAL</h4>
                        <p><em>{{ $content['pengayaan_remedial']['remedial']['sasaran'] ?? '' }}</em></p>
                        <ul class="mt-10">
                            @foreach($content['pengayaan_remedial']['remedial']['kegiatan'] ?? [] as $kegiatan)
                            <li>{{ $kegiatan }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif

    <!-- K. REFLEKSI -->
    @if(isset($content['refleksi']))
    <div class="section">
        <div class="section-header">K. REFLEKSI</div>
        <div class="section-body">
            @if(isset($content['refleksi']['refleksi_siswa']))
            <p class="text-bold">Refleksi Peserta Didik:</p>
            <ol class="mt-10">
                @foreach($content['refleksi']['refleksi_siswa'] as $item)
                <li>{{ $item }}</li>
                @endforeach
            </ol>
            @endif
            
            @if(isset($content['refleksi']['refleksi_guru']))
            <p class="text-bold mt-20">Refleksi Guru:</p>
            <ol class="mt-10">
                @foreach($content['refleksi']['refleksi_guru'] as $item)
                <li>{{ $item }}</li>
                @endforeach
            </ol>
            @endif
        </div>
    </div>
    @elseif(isset($content['refleksi_guru']))
    <!-- Fallback for old format -->
    <div class="section">
        <div class="section-header">K. REFLEKSI GURU</div>
        <div class="section-body">
            <p>Pertanyaan refleksi untuk evaluasi pembelajaran:</p>
            <ol class="mt-15">
                @foreach($content['refleksi_guru'] as $refleksi)
                <li>{{ $refleksi }}</li>
                @endforeach
            </ol>
        </div>
    </div>
    @endif

    <!-- L. GLOSARIUM -->
    @if(isset($content['glosarium']) && count($content['glosarium']) > 0)
    <div class="section">
        <div class="section-header">L. GLOSARIUM</div>
        <div class="section-body">
            <table class="info-table-content">
                @foreach($content['glosarium'] as $item)
                <tr>
                    <td>{{ $item['istilah'] ?? '' }}</td>
                    <td>{{ $item['definisi'] ?? '' }}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
    @endif

    <!-- M. DAFTAR PUSTAKA -->
    @if(isset($content['daftar_pustaka']) && count($content['daftar_pustaka']) > 0)
    <div class="section">
        <div class="section-header">M. DAFTAR PUSTAKA</div>
        <div class="section-body">
            <ol>
                @foreach($content['daftar_pustaka'] as $pustaka)
                <li>{{ $pustaka }}</li>
                @endforeach
            </ol>
        </div>
    </div>
    @endif

    <!-- N. LEMBAR KERJA PESERTA DIDIK (LKPD) -->
    @if(isset($content['lkpd']))
    <div class="section" style="page-break-before: always;">
        <div class="section-header">N. LEMBAR KERJA PESERTA DIDIK (LKPD)</div>
        <div class="section-body">
            @if(isset($content['lkpd']['judul']))
            <div style="text-align: center; margin-bottom: 20px;">
                <h3 style="font-size: 14pt; font-weight: bold; color: #27a38a;">{{ $content['lkpd']['judul'] }}</h3>
            </div>
            @endif

            <table class="info-table-content" style="margin-bottom: 20px;">
                <tr>
                    <td style="width: 20%;">Nama</td>
                    <td>: ................................................................</td>
                </tr>
                <tr>
                    <td>Kelas</td>
                    <td>: ................................................................</td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td>: ................................................................</td>
                </tr>
            </table>

            @if(isset($content['lkpd']['tujuan']))
            <div style="margin-bottom: 15px;">
                <p style="font-weight: bold; margin-bottom: 5px;">Tujuan:</p>
                <p>{{ $content['lkpd']['tujuan'] }}</p>
            </div>
            @endif

            @php $petunjuk = $content['lkpd']['petunjuk_umum'] ?? $content['lkpd']['petunjuk_pengerjaan'] ?? []; @endphp
            @if(count($petunjuk) > 0)
            <div style="margin-bottom: 15px;">
                <p style="font-weight: bold; margin-bottom: 5px;">Petunjuk Pengerjaan:</p>
                <ol>
                    @foreach($petunjuk as $p)
                    <li>{{ $p }}</li>
                    @endforeach
                </ol>
            </div>
            @endif

            @if(isset($content['lkpd']['kegiatan']))
            <div style="margin-bottom: 15px;">
                <p style="font-weight: bold; margin-bottom: 10px;">Kegiatan:</p>
                @foreach($content['lkpd']['kegiatan'] as $kegiatan)
                <div style="margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; background: #fafafa;">
                    @if(isset($kegiatan['judul_kegiatan']))
                    <p style="font-weight: bold; margin-bottom: 5px;">
                        {{ $kegiatan['nomor'] ?? $loop->iteration }}. {{ $kegiatan['judul_kegiatan'] }}
                    </p>
                    @if(isset($kegiatan['petunjuk']))
                    <p style="font-style: italic; margin-bottom: 10px;">{{ $kegiatan['petunjuk'] }}</p>
                    @endif
                    @if(isset($kegiatan['soal_tugas']))
                        @foreach($kegiatan['soal_tugas'] as $soal)
                        <div style="margin: 10px 0;">
                            <p style="font-weight: 500;">{{ $soal['nomor'] ?? '' }}. {{ $soal['pertanyaan'] ?? '' }}</p>
                            <div style="border: 1px dashed #ccc; min-height: 60px; background: white; padding: 10px; border-radius: 4px; margin-top: 5px;">
                                <p style="color: #999; font-size: 10pt;">Jawaban:</p>
                            </div>
                        </div>
                        @endforeach
                    @endif
                    @else
                    <p style="font-weight: bold; margin-bottom: 10px;">
                        {{ $kegiatan['nomor'] ?? $loop->iteration }}. {{ $kegiatan['pertanyaan'] ?? '' }}
                    </p>
                    <div style="border: 1px dashed #ccc; min-height: 80px; background: white; padding: 10px; border-radius: 4px;">
                        <p style="color: #999; font-size: 10pt;">Jawaban:</p>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            @if(isset($content['lkpd']['kesimpulan']))
            <div style="margin-bottom: 15px;">
                <p style="font-weight: bold; margin-bottom: 10px;">Kesimpulan:</p>
                <p style="margin-bottom: 10px;">{{ $content['lkpd']['kesimpulan'] }}</p>
                <div style="border: 1px dashed #ccc; min-height: 60px; background: white; padding: 10px; border-radius: 4px;">
                    <p style="color: #999; font-size: 10pt;">Tulis kesimpulanmu di sini:</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- ==================== SIGNATURE ==================== -->
    <div class="signature-section">
        <table class="signature-table">
            <tr>
                <td>
                    <p>Mengetahui,</p>
                    <p><strong>Kepala Sekolah</strong></p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ $rpp->kepala_sekolah ?? '.................................' }}</p>
                    <p class="signature-nip">NIP. {{ $rpp->nip_kepala_sekolah ?? '.................................' }}</p>
                </td>
                <td>
                    <p>{{ $rpp->kota ?? '.........................' }}, {{ $rpp->tanggal ? $rpp->tanggal->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</p>
                    <p><strong>Guru Mata Pelajaran</strong></p>
                    <div class="signature-space"></div>
                    <p class="signature-name">{{ $rpp->nama_guru }}</p>
                    <p class="signature-nip">NIP. .................................</p>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
