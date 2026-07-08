const fs = require('fs');

const path = 'resources/views/rpp/pdf_deep_learning.blade.php';
let content = fs.readFileSync(path, 'utf8');

// 1. Update Title and Subject
content = content.replace(
    /class="cover-title-main">Rencana Pelaksanaan<br>Pembelajaran Mendalam<\/div>/g,
    'class="cover-title-main">RENCANA PELAKSANAAN<br>PEMBELAJARAN MENDALAM</div>'
);

content = content.replace(
    /class="cover-subject">{{ strtoupper\(\$rpp->mata_pelajaran\) }}<\/div>/g,
    'class="cover-subject">{{ strtoupper($rpp->mata_pelajaran) }}</div>'
);

// We need to inject the premium CSS and shapes.
// It's easier to just replace the whole <style> block and the <div class="cover"> block.

const newStyle = `
    <style>
        @page {
            margin: 2.5cm 2.5cm 2.5cm 2.5cm; /* atas, kanan, bawah, kiri */
            size: A4;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            line-height: 1.55;
            color: #1a1a1a;
        }

        /* ============== DECORATIONS ============== */
        .decor-header { position: fixed; top: -2.5cm; left: -2.5cm; right: -2.5cm; height: 100px; z-index: -1; }
        .decor-footer { position: fixed; bottom: -2.5cm; left: -2.5cm; right: -2.5cm; height: 100px; z-index: -1; }
        
        .dots-tl { position: absolute; top: 1cm; left: 1cm; width: 60px; height: 60px; }
        .triangle-tr { position: absolute; top: 0; right: 0; width: 200px; height: 200px; }
        .triangle-bl { position: absolute; bottom: 0; left: 0; width: 200px; height: 200px; }
        .triangle-br { position: absolute; bottom: 0; right: 0; width: 150px; height: 150px; }

        /* ============== COVER PAGE ============== */
        .cover {
            page-break-after: always;
            text-align: center;
            padding: 30px 20px 40px;
            position: relative;
            background: #ffffff;
            height: 100%;
            z-index: 10; /* Hide fixed decorations */
        }
        .cover-tl { position: absolute; top: -2.5cm; left: -2.5cm; width: 300px; height: 300px; }
        .cover-tr { position: absolute; top: -2.5cm; right: -2.5cm; width: 300px; height: 300px; }
        .cover-bl { position: absolute; bottom: -2.5cm; left: -2.5cm; width: 300px; height: 300px; }
        .cover-br { position: absolute; bottom: -2.5cm; right: -2.5cm; width: 300px; height: 300px; }

        .cover-school-logo { margin-bottom: 8px; margin-top: 50px; position: relative; z-index: 2; }
        .cover-school-logo img { max-height: 80px; max-width: 80px; }
        .cover-school-name { font-size: 11pt; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; position: relative; z-index: 2; }
        .cover-school-sub { font-size: 9pt; color: #555; letter-spacing: 1px; position: relative; z-index: 2; margin-bottom: 30px; }
        
        .cover-title-main { font-size: 24pt; font-weight: bold; text-transform: uppercase; line-height: 1.1; margin: 20px 0 0; letter-spacing: 1px; color: #4b5563; position: relative; z-index: 2;}
        .cover-subject { font-size: 34pt; font-weight: bold; color: #facc15; text-transform: uppercase; letter-spacing: 1px; margin: 0; line-height: 1.1; position: relative; z-index: 2;}
        .cover-topic { font-size: 18pt; font-weight: bold; color: #b91c1c; text-transform: uppercase; margin-top: 5px; margin-bottom: 10px; padding: 0 25px; position: relative; z-index: 2;}
        .cover-semester { font-size: 14pt; color: #6b7280; margin-bottom: 30px; position: relative; z-index: 2;}
        
        .cover-garuda { margin: 20px auto; width: 220px; position: relative; z-index: 2;}
        .cover-garuda img { width: 100%; max-width: 250px; }
        
        .cover-author-label { font-size: 12pt; color: #374151; margin-top: 30px; margin-bottom: 5px; position: relative; z-index: 2;}
        .cover-author-name { font-size: 18pt; font-weight: bold; color: #b91c1c; position: relative; z-index: 2;}

        /* ============== TITLES ============== */
        .page-title { text-align: center; font-size: 14pt; font-weight: bold; margin-bottom: 20px; margin-top: 5px; }
        .section-letter { font-size: 12pt; font-weight: bold; margin: 18px 0 8px; }
        .subsection-num { font-weight: bold; font-size: 11pt; margin: 14px 0 6px; }

        /* ============== KATA PENGANTAR ============== */
        .kata-pengantar-body { page-break-after: always; position: relative; z-index: 2;}
        .kata-pengantar-body p { text-align: justify; text-indent: 35px; margin-bottom: 10px; line-height: 1.7; font-size: 11pt; }
        .kata-pengantar-signature { text-align: right; margin-top: 35px; font-size: 11pt; }
        .kata-pengantar-signature .space { height: 55px; }

        /* ============== DAFTAR ISI ============== */
        .daftar-isi { width: 100%; border-collapse: collapse; page-break-after: always; position: relative; z-index: 2;}
        .daftar-isi td { border: none; padding: 6px 0; font-size: 11pt; vertical-align: bottom; }
        .daftar-isi .dots { border-bottom: 1px dotted #555; padding: 0 8px 4px 8px; }
        .daftar-isi .page-col { text-align: right; width: 35px; }
        .daftar-isi .level-1 { font-weight: bold; }
        .daftar-isi .level-2 td:first-child { padding-left: 20px; }
        .daftar-isi .level-3 td:first-child { padding-left: 40px; }

        /* ============== TABLES (base) ============== */
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; position: relative; z-index: 2; background: white;}
        th, td { border: 1px solid #666; padding: 6px 9px; vertical-align: top; font-size: 10.5pt; line-height: 1.5; }

        /* Info block (no borders) */
        .tbl-info td { border: none; padding: 3px 0; font-size: 11pt; background: transparent;}
        .tbl-info td:first-child { width: 32%; }

        /* Red-header table */
        .tbl-red thead th, .tbl-red tbody .row-head td { background-color: #b91c1c; color: #ffffff; font-weight: bold; text-align: center; padding: 7px 9px; border: 1px solid #b91c1c; }
        .tbl-red tbody .row-sub td { background-color: #f5f5f5; font-weight: bold; }
        .label-cell { font-weight: bold; background-color: #fafafa; width: 25%; }

        /* Langkah table */
        .tbl-langkah th { background-color: #b91c1c; color: #ffffff; text-align: center; font-weight: bold; padding: 8px; border: 1px solid #b91c1c; }
        .tbl-langkah .phase-header { background-color: #f0f0f0; font-weight: bold; text-align: center; padding: 7px; font-style: italic; }
        .tbl-langkah .col-pengalaman { width: 22%; text-align: center; vertical-align: middle; background-color: #fff6f6; font-weight: bold; color: #b91c1c; font-size: 13pt; padding: 15px 8px; }
        .tbl-langkah .sub-section { font-weight: bold; font-style: italic; margin: 6px 0 3px; }
        .tbl-langkah ol { margin-left: 22px; padding-left: 0; }
        .tbl-langkah ol li { margin-bottom: 3px; }
        .tbl-langkah .activity-list { list-style: none; margin: 0; padding: 0; }
        .tbl-langkah .activity-list li { margin-bottom: 5px; padding-left: 0; }
        .kse-tag { display: inline; background-color: #dbeafe; color: #1e3a8a; padding: 1px 4px; border-radius: 3px; font-size: 9.5pt; margin-left: 4px; font-style: italic; }
    </style>
`;

// Extract SVG strings to be injected
const decorHeader = `
<div class="decor-header">
    <svg class="dots-tl" viewBox="0 0 40 60" xmlns="http://www.w3.org/2000/svg">
        <circle cx="5" cy="5" r="3" fill="#b91c1c"/><circle cx="20" cy="5" r="3" fill="#b91c1c"/><circle cx="35" cy="5" r="3" fill="#b91c1c"/>
        <circle cx="5" cy="20" r="3" fill="#b91c1c"/><circle cx="20" cy="20" r="3" fill="#b91c1c"/><circle cx="35" cy="20" r="3" fill="#b91c1c"/>
        <circle cx="5" cy="35" r="3" fill="#b91c1c"/><circle cx="20" cy="35" r="3" fill="#b91c1c"/><circle cx="35" cy="35" r="3" fill="#b91c1c"/>
        <circle cx="5" cy="50" r="3" fill="#b91c1c"/><circle cx="20" cy="50" r="3" fill="#b91c1c"/><circle cx="35" cy="50" r="3" fill="#b91c1c"/>
    </svg>
    <svg class="triangle-tr" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <polygon points="100,0 200,0 200,100" fill="#7f1d1d"/>
        <polygon points="20,0 200,0 200,180" fill="#b91c1c"/>
    </svg>
</div>
`;

const decorFooter = `
<div class="decor-footer">
    <svg class="triangle-bl" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <polygon points="0,50 150,200 0,200" fill="#facc15"/>
        <polygon points="0,100 100,200 0,200" fill="#7f1d1d"/>
        <polygon points="0,150 50,200 0,200" fill="#b91c1c"/>
    </svg>
    <svg class="triangle-br" viewBox="0 0 150 150" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
        <polygon points="0,150 150,0 150,150" fill="#7f1d1d"/>
    </svg>
</div>
`;

const newCover = `
<div class="cover">
    <!-- Cover Ornaments -->
    <svg class="cover-tl" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <rect x="20" y="20" width="60" height="60" fill="#7f1d1d" transform="rotate(45 50 50)"/>
        <rect x="50" y="-10" width="60" height="60" fill="#b91c1c" transform="rotate(45 80 20)"/>
        <rect x="-10" y="50" width="60" height="60" fill="#b91c1c" transform="rotate(45 20 80)"/>
    </svg>
    <svg class="cover-tr" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <polygon points="20,0 200,0 200,180" fill="#b91c1c"/>
        <polygon points="100,0 200,0 200,100" fill="#7f1d1d"/>
        <polygon points="0,0 200,0 200,200" fill="none" stroke="#facc15" stroke-width="4"/>
    </svg>
    <svg class="cover-bl" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <rect x="40" y="120" width="50" height="50" fill="#b91c1c" transform="rotate(45 65 145)"/>
        <rect x="-10" y="150" width="80" height="80" fill="#7f1d1d" transform="rotate(45 30 190)"/>
    </svg>
    <svg class="cover-br" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
        <polygon points="0,200 200,0 200,200" fill="#b91c1c"/>
        <rect x="120" y="120" width="40" height="40" fill="#ffffff" transform="rotate(45 140 140)"/>
        <rect x="150" y="150" width="30" height="30" fill="#facc15" transform="rotate(45 165 165)"/>
    </svg>

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
        <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj48Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI0NSIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjZmFjYzE1IiBzdHJva2Utd2lkdGg9IjQiLz48dGV4dCB4PSI1MCIgeT0iNTUiIGZvbnQtZmFtaWx5PSJzYW5zLXNlcmlmIiBmb250LXNpemU9IjEycHgiIGZpbGw9IiM2NjYiIHRleHQtYW5jaG9yPSJtaWRkbGUiPkxhbWJhbmcgR2FydWRhPC90ZXh0Pjwvc3ZnPg==" alt="Garuda">
    </div>

    <div class="cover-author-label">Disusun oleh:</div>
    <div class="cover-author-name">{{ $rpp->nama_guru }}</div>
</div>
`;

// Now apply the replacements safely
// 1. Replace <style> block
content = content.replace(/<style>[\s\S]*?<\/style>/, newStyle);

// 2. Replace cover div
content = content.replace(/<div class="cover">[\s\S]*?<\/div>/, newCover);

// 3. Inject decor headers just after <body>
content = content.replace('<body>', '<body>\n' + decorHeader + '\n' + decorFooter);

// Wait, the prompt says "ubah kata2nya menjadi RPPM (RENCANA PELAKSANAAN PEMBELAJARAN MENDALAM)"
// I'll make sure "Rencana Pelaksanaan Pembelajaran (RPP)" in Kata Pengantar is updated.
content = content.replace(/Rencana Pelaksanaan Pembelajaran Mendalam \(RPPM\)/g, 'Rencana Pelaksanaan Pembelajaran Mendalam (RPPM)');
// If the text says Rencana Pelaksanaan Pembelajaran (RPP) somewhere, update it:
content = content.replace(/Rencana Pelaksanaan Pembelajaran \(RPP\)/g, 'Rencana Pelaksanaan Pembelajaran Mendalam (RPPM)');
content = content.replace(/Rencana Pelaksanaan Pembelajaran/g, 'Rencana Pelaksanaan Pembelajaran Mendalam');

fs.writeFileSync(path, content, 'utf8');
console.log('Updated pdf_deep_learning.blade.php');
