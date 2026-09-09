{{-- CSS dasar dokumen, dipakai bersama rpp/pdf.blade.php dan
     rpp/pdf_deep_learning.blade.php. Diparametrikan oleh skin ($design,
     config/rpp_designs.php) dan palet warna ($primary/$dark/$accent/$primaryTint).

     Kelas yang hanya dipakai salah satu template TIDAK tinggal di sini —
     tiap template melanjutkan blok <style>-nya sendiri setelah @include ini. --}}
        @@page {
            margin: 2.5cm;
            size: A4;
        }
        /* Reset bertarget — JANGAN reset `html` (dan jangan pakai `* {}`):
           margin:0 pada html menggeser acuan posisi elemen fixed di DomPDF,
           sehingga ornamen sudut (offset negatif) terlempar ke luar kanvas. */
        body, div, p, h1, h2, h3, h4, ol, ul, li, table, thead, tbody, tr, th, td, span { margin: 0; padding: 0; }
        body {
            {{-- Tanpa escape: font stack memuat tanda kutip tunggal, dan {{ }}
                 mengubahnya jadi &#039; sehingga deklarasi font batal diam-diam.
                 Aman karena nilainya dari config/rpp_designs.php, bukan input. --}}
            font-family: {!! $design['font'] !!};
            font-size: 11pt;
            line-height: 1.55;
            color: #1a1a1a;
        }

@if ($design['ornamen'])
        /* ============== DECORATIONS ==============
           Ornamen sudut = PNG transparan (di-generate GD, public/decor-*.png).
           SVG, gradient, dan border-top triangle tidak dirender DomPDF;
           gambar PNG dirender andal. position:fixed = berulang tiap halaman. */
        .fx-dots { position: fixed; top: -1.6cm; left: -1.6cm; width: 48px; }
        .fx-tr   { position: fixed; top: -2.5cm; right: -2.5cm; width: 180px; }
        .fx-bl   { position: fixed; bottom: -2.5cm; left: -2.5cm; width: 160px; }
        .fx-br   { position: fixed; bottom: -2.5cm; right: -2.5cm; width: 115px; }
@endif

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

@if ($design['tabel'] === 'solid')
        .tbl-red thead th, .tbl-red tbody .row-head td { background-color: {{ $primary }}; color: #ffffff; font-weight: bold; text-align: center; padding: 7px 9px; border: 1px solid {{ $primary }}; }
@else
        .tbl-red thead th, .tbl-red tbody .row-head td { background-color: #ffffff; color: {{ $primary }}; font-weight: bold; text-align: center; padding: 7px 9px; border: 1px solid #ccc; border-bottom: 2px solid {{ $primary }}; }
@endif
        .tbl-red tbody .row-sub td { background-color: #f5f5f5; font-weight: bold; }
        .label-cell { font-weight: bold; background-color: #fafafa; width: 25%; }

@if ($design['tabel'] === 'solid')
        .tbl-langkah th { background-color: {{ $primary }}; color: #ffffff; text-align: center; font-weight: bold; padding: 8px; border: 1px solid {{ $primary }}; }
        .tbl-langkah .col-pengalaman { width: 20%; text-align: center; vertical-align: middle; background-color: {{ $primaryTint }}; font-weight: bold; color: {{ $primary }}; font-size: 12pt; padding: 15px 8px; }
@else
        .tbl-langkah th { background-color: #ffffff; color: {{ $primary }}; text-align: center; font-weight: bold; padding: 8px; border: 1px solid #ccc; border-bottom: 2px solid {{ $primary }}; }
        .tbl-langkah .col-pengalaman { width: 20%; text-align: center; vertical-align: middle; background-color: #ffffff; font-weight: bold; color: {{ $primary }}; font-size: 12pt; padding: 15px 8px; }
@endif

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
