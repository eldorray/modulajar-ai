const fs = require('fs');

const path = 'resources/views/rpp/pdf.blade.php';
let content = fs.readFileSync(path, 'utf8');

// The styling and structure are mostly similar. Let's do similar replacements.
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
            display: block; /* Removing flex to align with design */
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
        .page-title { text-align: center; font-size: 14pt; font-weight: bold; margin-bottom: 20px; margin-top: 5px; padding-bottom: 15px; border-bottom: 2px solid #b91c1c; }
        .page-title h1 { color: #b91c1c; }
        .section-header { background: #b91c1c; color: white; padding: 10px 20px; font-size: 12pt; font-weight: bold; border-radius: 6px 6px 0 0; margin-bottom: 0; }
        .section-body { border: 1px solid #e5e7eb; border-top: none; border-radius: 0 0 6px 6px; padding: 20px; background: #ffffff; margin-bottom: 25px; }

        /* Tables */
        .info-table-content { width: 100%; border-collapse: collapse; }
        .info-table-content tr { border-bottom: 1px solid #e5e7eb; }
        .info-table-content td { padding: 12px 15px; vertical-align: top; }
        .info-table-content td:first-child { width: 35%; font-weight: 600; color: #374151; background: #f3f4f6; }
        
        /* Activity Boxes */
        .activity-box { margin-bottom: 20px; border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden; background: white; }
        .activity-header { background: linear-gradient(90deg, #f3f4f6, #e5e7eb); padding: 12px 20px; font-weight: bold; color: #1f2937; }
        .activity-content { padding: 15px 20px; }
    </style>
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
        <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDAgMTAwIj48Y2lyY2xlIGN4PSI1MCIgY3k9IjUwIiByPSI0NSIgZmlsbD0ibm9uZSIgc3Ryb2tlPSIjZmFjYzE1IiBzdHJva2Utd2lkdGg9IjQiLz48dGV4dCB4PSI1MCIgeT0iNTUiIGZvbnQtZmFtaWx5PSJzYW5zLXNlcmlmIiBmb250LXNpemU9IjEycHgiIGZpbGw9IiM2NjYiIHRleHQtYW5jaG9yPSJtaWRkbGUiPkxhbWJhbmcgR2FydWRhPC90ZXh0Pjwvc3ZnPg==" alt="Garuda">
    </div>

    <div class="cover-author-label">Disusun oleh:</div>
    <div class="cover-author-name">{{ $rpp->nama_guru }}</div>
</div>
`;

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

// Apply Replacements
content = content.replace(/<style>[\s\S]*?<\/style>/, newStyle);
content = content.replace(/<div class="cover">[\s\S]*?<\/div>/, newCover);
content = content.replace('<body>', '<body>\n' + decorHeader + '\n' + decorFooter);

// Wording changes
content = content.replace(/Rencana Pelaksanaan Pembelajaran Mendalam \(RPPM\)/g, 'Rencana Pelaksanaan Pembelajaran Mendalam (RPPM)');
content = content.replace(/Rencana Pelaksanaan Pembelajaran \(RPP\)/g, 'Rencana Pelaksanaan Pembelajaran Mendalam (RPPM)');
content = content.replace(/Rencana Pelaksanaan Pembelajaran/g, 'Rencana Pelaksanaan Pembelajaran Mendalam');

fs.writeFileSync(path, content, 'utf8');
console.log('Updated pdf.blade.php');
