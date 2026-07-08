const fs = require('fs');

function processTemplate(path) {
    let content = fs.readFileSync(path, 'utf8');

    // Remove the entire <style> block and replace it
    content = content.replace(/<style>[\s\S]*?<\/style>/, `
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
    `);

    // Replace <title>
    content = content.replace(/<title>.*?<\/title>/, '<title>RPPM - {{ $rpp->mata_pelajaran }}</title>');

    // Safely replace Cover section
    // We will extract everything between `<div class="cover">` and its corresponding closing `</div>`.
    // Wait, since both have a very clear Cover Page comment, we can use that.
    const coverRegex = /<div class="cover">[\s\S]*?<\/div>\s*(?=\{\{-- =+[\r\n]+     KATA PENGANTAR|\{\{-- =+[\r\n]+     A\. IDENTITAS|<div class="page-title">)/;
    
    const newCover = `
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
        <img src="data:image/png;base64,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" alt="Garuda">
    </div>

    <div class="cover-author-label">Disusun oleh:</div>
    <div class="cover-author-name">{{ $rpp->nama_guru }}</div>
</div>
    `;

    content = content.replace(coverRegex, newCover);

    // Inject fixed header/footer right after <body>
    content = content.replace('<body>', '<body>\n<div class="decor-header"></div>\n<div class="decor-footer"></div>');

    // Rename words
    content = content.replace(/Rencana Pelaksanaan Pembelajaran Mendalam \(RPPM\)/g, 'Rencana Pelaksanaan Pembelajaran Mendalam (RPPM)');
    content = content.replace(/Rencana Pelaksanaan Pembelajaran \(RPP\)/g, 'Rencana Pelaksanaan Pembelajaran Mendalam (RPPM)');
    content = content.replace(/Rencana Pelaksanaan Pembelajaran/g, 'Rencana Pelaksanaan Pembelajaran Mendalam');

    // Remove old header colors if any specific elements
    content = content.replace(/#27a38a/g, '#b91c1c');
    
    fs.writeFileSync(path, content, 'utf8');
    console.log('Updated', path);
}

processTemplate('resources/views/rpp/pdf_deep_learning.blade.php');
processTemplate('resources/views/rpp/pdf.blade.php');

