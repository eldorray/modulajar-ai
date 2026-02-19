<?php

namespace App\Http\Controllers;

use App\Models\Sts;
use App\Models\SchoolSetting;
use App\Services\DeepSeekService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\SimpleType\Jc;
use PhpOffice\PhpWord\Style\ListItem;

class StsController extends Controller
{
    protected DeepSeekService $deepSeekService;

    public function __construct(DeepSeekService $deepSeekService)
    {
        $this->deepSeekService = $deepSeekService;
    }

    /**
     * Display a listing of STS.
     */
    public function index()
    {
        $stsList = Sts::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('sts.index', compact('stsList'));
    }

    /**
     * Show the form for creating a new STS.
     */
    public function create()
    {
        return view('sts.create');
    }

    /**
     * Store a newly created STS.
     */
    public function store(Request $request)
    {
        // Extend PHP execution time for AI processing
        set_time_limit(180);

        $validated = $request->validate([
            'mata_pelajaran' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'fase' => 'required|string|max:50',
            'topik' => 'required|string',
            'tujuan_pembelajaran' => 'nullable|string',
            'jumlah_pg' => 'required|integer|min:0|max:30',
            'jumlah_pg_kompleks' => 'required|integer|min:0|max:10',
            'jumlah_menjodohkan' => 'required|integer|min:0|max:10',
            'jumlah_uraian' => 'required|integer|min:0|max:10',
            'materi' => 'nullable|string',
        ]);

        // Calculate total questions
        $jumlahSoal = $validated['jumlah_pg'] + $validated['jumlah_pg_kompleks'] 
                    + $validated['jumlah_menjodohkan'] + $validated['jumlah_uraian'];

        // Create STS record
        $sts = Sts::create([
            'user_id' => Auth::id(),
            'mata_pelajaran' => $validated['mata_pelajaran'],
            'kelas' => $validated['kelas'],
            'fase' => $validated['fase'],
            'topik' => $validated['topik'],
            'tujuan_pembelajaran' => $validated['tujuan_pembelajaran'] ?? '',
            'jumlah_soal' => $jumlahSoal,
            'jumlah_pg' => $validated['jumlah_pg'],
            'jumlah_pg_kompleks' => $validated['jumlah_pg_kompleks'],
            'jumlah_menjodohkan' => $validated['jumlah_menjodohkan'],
            'jumlah_uraian' => $validated['jumlah_uraian'],
            'status' => 'processing',
        ]);

        // Generate STS using AI
        $result = $this->deepSeekService->generateSTS([
            'mata_pelajaran' => $validated['mata_pelajaran'],
            'kelas' => $validated['kelas'],
            'fase' => $validated['fase'],
            'topik' => $validated['topik'],
            'tujuan_pembelajaran' => $validated['tujuan_pembelajaran'] ?? '',
            'jumlah_pg' => $validated['jumlah_pg'],
            'jumlah_pg_kompleks' => $validated['jumlah_pg_kompleks'],
            'jumlah_menjodohkan' => $validated['jumlah_menjodohkan'],
            'jumlah_uraian' => $validated['jumlah_uraian'],
            'materi' => $validated['materi'] ?? '',
        ], Auth::id(), $sts->id);

        if ($result['success'] && !empty($result['content'])) {
            $sts->update([
                'content_result' => $result['content'],
                'status' => 'completed',
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => route('sts.show', $sts),
                ]);
            }

            return redirect()->route('sts.show', $sts)
                ->with('success', 'Soal STS berhasil dibuat!');
        }

        $sts->update(['status' => 'failed']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'error' => $result['error'] ?? 'Gagal menghasilkan soal STS',
            ]);
        }

        return back()->with('error', $result['error'] ?? 'Gagal menghasilkan soal STS');
    }

    /**
     * Display the specified STS.
     */
    public function show(Sts $sts)
    {
        // Ensure user can only view their own STS
        if ($sts->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('sts.show', compact('sts'));
    }

    /**
     * Delete the specified STS.
     */
    public function destroy(Sts $sts)
    {
        // Ensure user can only delete their own STS
        if ($sts->user_id != Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $sts->forceDelete();

        return redirect()->route('sts.index')
            ->with('success', 'Soal STS berhasil dihapus!');
    }

    /**
     * Download STS as PDF document with letterhead.
     */
    public function downloadPdf(Sts $sts)
    {
        if ($sts->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $content = $sts->content_result;
        if (!$content) {
            return back()->with('error', 'Data soal tidak tersedia');
        }

        $schoolSettings = SchoolSetting::getSettings();
        
        $pdf = Pdf::loadView('sts.pdf', compact('sts', 'schoolSettings'));
        
        $pdf->setPaper([0, 0, 612, 936], 'landscape'); // F4 landscape (215mm x 330mm)
        
        $filename = 'STS_' . str_replace(' ', '_', $sts->mata_pelajaran) . '_' . $sts->kelas . '_' . date('Ymd') . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Download STS as Word document.
     */
    public function downloadWord(Sts $sts)
    {
        if ($sts->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $content = $sts->content_result;
        if (!$content) {
            return back()->with('error', 'Data soal tidak tersedia');
        }

        $schoolSettings = SchoolSetting::getSettings();

        $phpWord = new PhpWord();
        
        // Match PDF font: DejaVu Sans → Arial (closest sans-serif in Word), 9pt
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(9);

        // F4 Landscape: 330mm x 215mm
        $section = $phpWord->addSection([
            'orientation' => 'landscape',
            'pageSizeW' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(33.0),
            'pageSizeH' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(21.5),
            'marginTop' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(1.4),
            'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(1.4),
            'marginLeft' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(1.6),
            'marginRight' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(1.6),
        ]);

        // Full page width in twips (for table sizing)
        $pageWidthTwip = \PhpOffice\PhpWord\Shared\Converter::cmToTwip(33.0 - 1.6 - 1.6);
        $halfPageWidth = (int) ($pageWidthTwip / 2);

        // ========== KOP SURAT / HEADER ==========
        if ($schoolSettings->kop_surat && file_exists(storage_path('app/public/' . $schoolSettings->kop_surat))) {
            $imagePath = storage_path('app/public/' . $schoolSettings->kop_surat);
            $imageInfo = getimagesize($imagePath);
            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            
            // Scale to fit landscape page width
            $targetWidth = 680;
            $aspectRatio = $originalHeight / $originalWidth;
            $targetHeight = $targetWidth * $aspectRatio;
            
            $section->addImage(
                $imagePath,
                [
                    'width' => $targetWidth,
                    'height' => $targetHeight,
                    'alignment' => Jc::CENTER,
                ]
            );
        } elseif ($schoolSettings->nama_sekolah || $schoolSettings->logo) {
            $headerTable = $section->addTable(['alignment' => Jc::CENTER]);
            $headerTable->addRow();
            
            $cellLeft = $headerTable->addCell(1200);
            if ($schoolSettings->logo && file_exists(storage_path('app/public/' . $schoolSettings->logo))) {
                $cellLeft->addImage(
                    storage_path('app/public/' . $schoolSettings->logo),
                    ['width' => 45, 'height' => 45, 'alignment' => Jc::CENTER]
                );
            }
            
            $cellCenter = $headerTable->addCell(8000);
            if ($schoolSettings->nama_sekolah) {
                $cellCenter->addText(
                    strtoupper($schoolSettings->nama_sekolah),
                    ['bold' => true, 'size' => 12, 'name' => 'Arial'],
                    ['alignment' => Jc::CENTER]
                );
            }
            if ($schoolSettings->npsn || $schoolSettings->nsm) {
                $info = [];
                if ($schoolSettings->npsn) $info[] = "NPSN: {$schoolSettings->npsn}";
                if ($schoolSettings->nsm) $info[] = "NSM: {$schoolSettings->nsm}";
                $cellCenter->addText(implode(' | ', $info), ['size' => 7.5], ['alignment' => Jc::CENTER]);
            }
            if ($schoolSettings->alamat) {
                $cellCenter->addText($schoolSettings->alamat, ['size' => 7.5], ['alignment' => Jc::CENTER]);
            }
            
            $cellRight = $headerTable->addCell(1200);
            if ($schoolSettings->logo_kanan && file_exists(storage_path('app/public/' . $schoolSettings->logo_kanan))) {
                $cellRight->addImage(
                    storage_path('app/public/' . $schoolSettings->logo_kanan),
                    ['width' => 45, 'height' => 45, 'alignment' => Jc::CENTER]
                );
            }
        }

        // Double-line separator (matching PDF border-bottom: 3px double)
        $section->addText('');
        $section->addText(
            str_repeat('_', 120),
            ['size' => 6],
            ['alignment' => Jc::CENTER, 'spacing' => 0]
        );

        // ========== TITLE BOX (matching PDF: background #f0f0f0, border) ==========
        $titleTable = $section->addTable([
            'alignment' => Jc::CENTER,
            'borderSize' => 4,
            'borderColor' => 'BBBBBB',
        ]);
        $titleTable->addRow();
        $titleCell = $titleTable->addCell($pageWidthTwip, [
            'shading' => ['fill' => 'F0F0F0'],
            'valign' => 'center',
        ]);
        $titleCell->addText(
            'SUMATIF TENGAH SEMESTER (STS)',
            ['bold' => true, 'size' => 10.5, 'name' => 'Arial'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 0]
        );
        $titleCell->addText(
            'TAHUN PELAJARAN ' . date('Y') . '/' . (date('Y') + 1),
            ['size' => 8.5, 'name' => 'Arial'],
            ['alignment' => Jc::CENTER, 'spaceBefore' => 0]
        );

        // ========== INFO TABLE (matching PDF: with dotted bottom border) ==========
        $infoTable = $section->addTable(['borderSize' => 0]);
        $infoCellStyle = ['borderBottomSize' => 4, 'borderBottomColor' => '000000', 'borderBottomStyle' => 'dotted'];
        $infoLabelStyle = ['bold' => true, 'size' => 8.5, 'name' => 'Arial'];
        $infoValueStyle = ['size' => 8.5, 'name' => 'Arial'];
        
        $infoTable->addRow();
        $infoTable->addCell(1800)->addText('Mata Pelajaran', $infoLabelStyle);
        $infoTable->addCell(200)->addText(':', $infoValueStyle);
        $infoTable->addCell(3500, $infoCellStyle)->addText($sts->mata_pelajaran, $infoValueStyle);
        $infoTable->addCell(500)->addText('', $infoValueStyle);
        $infoTable->addCell(1800)->addText('Nama', $infoLabelStyle);
        $infoTable->addCell(200)->addText(':', $infoValueStyle);
        $infoTable->addCell(3500, $infoCellStyle)->addText('.........................', $infoValueStyle);
        
        $infoTable->addRow();
        $infoTable->addCell(1800)->addText('Kelas', $infoLabelStyle);
        $infoTable->addCell(200)->addText(':', $infoValueStyle);
        $infoTable->addCell(3500, $infoCellStyle)->addText($sts->kelas, $infoValueStyle);
        $infoTable->addCell(500)->addText('', $infoValueStyle);
        $infoTable->addCell(1800)->addText('Hari/Tanggal', $infoLabelStyle);
        $infoTable->addCell(200)->addText(':', $infoValueStyle);
        $infoTable->addCell(3500, $infoCellStyle)->addText('.........................', $infoValueStyle);

        // ========== PETUNJUK (matching PDF: border-left + background) ==========
        $petunjukTable = $section->addTable();
        $petunjukTable->addRow();
        $petunjukTable->addCell(60, [
            'shading' => ['fill' => '333333'],
            'vMerge' => 'restart',
        ])->addText('');
        $petunjukTable->addCell($pageWidthTwip - 60, [
            'shading' => ['fill' => 'EEEEEE'],
        ])->addText(
            'Berilah tanda silang (X) pada huruf A, B, C, atau D di depan jawaban yang paling tepat!',
            ['bold' => true, 'size' => 8, 'name' => 'Arial'],
            ['spaceAfter' => 40, 'spaceBefore' => 40]
        );

        // ========== SOAL PILIHAN GANDA (2 Columns) ==========
        if (!empty($content['soal_pilihan_ganda'])) {
            // Section title with background
            $secTable = $section->addTable();
            $secTable->addRow();
            $secTable->addCell($pageWidthTwip, ['shading' => ['fill' => 'DDDDDD']])->addText(
                'I. PILIHAN GANDA',
                ['bold' => true, 'size' => 9, 'name' => 'Arial'],
                ['spaceAfter' => 40, 'spaceBefore' => 40]
            );

            $pgSoal = $content['soal_pilihan_ganda'];
            $totalPg = count($pgSoal);
            $halfPoint = ceil($totalPg / 2);

            // 2-column table without any borders
            $noBorderStyle = ['borderSize' => 0, 'borderTopSize' => 0, 'borderBottomSize' => 0, 'borderLeftSize' => 0, 'borderRightSize' => 0];
            $pgTable = $section->addTable(['borderSize' => 0, 'borderColor' => 'FFFFFF']);
            $pgTable->addRow();
            
            // Left column
            $leftCell = $pgTable->addCell($halfPageWidth, $noBorderStyle);
            for ($i = 0; $i < $halfPoint && $i < $totalPg; $i++) {
                $soal = $pgSoal[$i];
                $leftCell->addText(($i + 1) . '. ' . ($soal['pertanyaan'] ?? ''), ['size' => 8.5, 'name' => 'Arial'], ['spaceAfter' => 20]);
                if (!empty($soal['pilihan'])) {
                    $options = [];
                    foreach ($soal['pilihan'] as $key => $pilihan) {
                        $options[] = "{$key}. {$pilihan}";
                    }
                    $leftCell->addText('    ' . implode('   ', $options), ['size' => 8, 'name' => 'Arial'], ['spaceAfter' => 60]);
                }
            }
            
            // Right column
            $rightCell = $pgTable->addCell($halfPageWidth, $noBorderStyle);
            for ($i = $halfPoint; $i < $totalPg; $i++) {
                $soal = $pgSoal[$i];
                $rightCell->addText(($i + 1) . '. ' . ($soal['pertanyaan'] ?? ''), ['size' => 8.5, 'name' => 'Arial'], ['spaceAfter' => 20]);
                if (!empty($soal['pilihan'])) {
                    $options = [];
                    foreach ($soal['pilihan'] as $key => $pilihan) {
                        $options[] = "{$key}. {$pilihan}";
                    }
                    $rightCell->addText('    ' . implode('   ', $options), ['size' => 8, 'name' => 'Arial'], ['spaceAfter' => 60]);
                }
            }
        }

        // ========== SOAL PG KOMPLEKS ==========
        if (!empty($content['soal_pg_kompleks'])) {
            $secTable = $section->addTable();
            $secTable->addRow();
            $secTable->addCell($pageWidthTwip, ['shading' => ['fill' => 'DDDDDD']])->addText(
                'II. PILIHAN GANDA KOMPLEKS',
                ['bold' => true, 'size' => 9, 'name' => 'Arial'],
                ['spaceAfter' => 40, 'spaceBefore' => 40]
            );
            $section->addText('Tentukan pernyataan berikut Benar atau Salah!', ['italic' => true, 'size' => 7.5, 'name' => 'Arial'], ['spaceAfter' => 40]);

            foreach ($content['soal_pg_kompleks'] as $index => $soal) {
                $section->addText(($index + 1) . '. ' . ($soal['pertanyaan'] ?? ''), ['size' => 8.5, 'name' => 'Arial'], ['spaceAfter' => 20]);
                if (!empty($soal['pernyataan'])) {
                    foreach ($soal['pernyataan'] as $p) {
                        // Pernyataan with left border + light background (matching PDF)
                        $pTable = $section->addTable();
                        $pTable->addRow();
                        $pTable->addCell(40, ['shading' => ['fill' => 'CCCCCC']])->addText('');
                        $pTable->addCell($pageWidthTwip - 40, ['shading' => ['fill' => 'F8F8F8']])->addText(
                            ($p['teks'] ?? '') . ' (................)',
                            ['size' => 8, 'name' => 'Arial'],
                            ['indentation' => ['left' => 100], 'spaceAfter' => 20, 'spaceBefore' => 20]
                        );
                    }
                }
                $section->addText('');
            }
        }

        // ========== SOAL MENJODOHKAN ==========
        if (!empty($content['soal_menjodohkan'])) {
            $secTable = $section->addTable();
            $secTable->addRow();
            $secTable->addCell($pageWidthTwip, ['shading' => ['fill' => 'DDDDDD']])->addText(
                'III. MENJODOHKAN',
                ['bold' => true, 'size' => 9, 'name' => 'Arial'],
                ['spaceAfter' => 40, 'spaceBefore' => 40]
            );
            $section->addText('Jodohkan pernyataan di kolom kiri dengan jawaban di kolom kanan!', ['italic' => true, 'size' => 7.5, 'name' => 'Arial'], ['spaceAfter' => 40]);

            $matchTable = $section->addTable(['borderSize' => 4, 'borderColor' => '000000']);
            $matchTable->addRow();
            $matchTable->addCell(500, ['shading' => ['fill' => 'DDDDDD']])->addText('No', ['bold' => true, 'size' => 8, 'name' => 'Arial'], ['alignment' => Jc::CENTER]);
            $matchTable->addCell(4500, ['shading' => ['fill' => 'DDDDDD']])->addText('Soal', ['bold' => true, 'size' => 8, 'name' => 'Arial'], ['alignment' => Jc::CENTER]);
            $matchTable->addCell(1000, ['shading' => ['fill' => 'DDDDDD']])->addText('Jawaban', ['bold' => true, 'size' => 8, 'name' => 'Arial'], ['alignment' => Jc::CENTER]);
            $matchTable->addCell(4500, ['shading' => ['fill' => 'DDDDDD']])->addText('Pilihan', ['bold' => true, 'size' => 8, 'name' => 'Arial'], ['alignment' => Jc::CENTER]);

            foreach ($content['soal_menjodohkan'] as $index => $soal) {
                $matchTable->addRow();
                $matchTable->addCell(500)->addText($index + 1, ['size' => 8, 'name' => 'Arial'], ['alignment' => Jc::CENTER]);
                $matchTable->addCell(4500)->addText($soal['soal'] ?? '', ['size' => 8, 'name' => 'Arial']);
                $matchTable->addCell(1000)->addText('(......)', ['size' => 8, 'name' => 'Arial'], ['alignment' => Jc::CENTER]);
                $matchTable->addCell(4500)->addText(chr(65 + $index) . '. ' . ($soal['jawaban'] ?? ''), ['size' => 8, 'name' => 'Arial']);
            }
        }

        // ========== FOOTER HALAMAN 1 ==========
        $section->addText('');
        $section->addText('*** Selamat Mengerjakan ***', ['italic' => true, 'size' => 8.5, 'name' => 'Arial'], ['alignment' => Jc::RIGHT]);

        // ========== HALAMAN 2: URAIAN + KUNCI JAWABAN ==========
        $section->addPageBreak();

        // ========== SOAL URAIAN ==========
        if (!empty($content['soal_uraian'])) {
            $secTable = $section->addTable();
            $secTable->addRow();
            $secTable->addCell($pageWidthTwip, ['shading' => ['fill' => 'DDDDDD']])->addText(
                'IV. URAIAN',
                ['bold' => true, 'size' => 9, 'name' => 'Arial'],
                ['spaceAfter' => 40, 'spaceBefore' => 40]
            );
            $section->addText('Jawablah pertanyaan berikut dengan jelas dan lengkap!', ['italic' => true, 'size' => 7.5, 'name' => 'Arial'], ['spaceAfter' => 40]);

            foreach ($content['soal_uraian'] as $index => $soal) {
                $section->addText(($index + 1) . '. ' . ($soal['pertanyaan'] ?? ''), ['size' => 8.5, 'name' => 'Arial'], ['spaceAfter' => 20]);
                // Dotted answer line (matching PDF uraian-lines)
                $section->addText(
                    str_repeat('.', 150),
                    ['color' => '999999', 'size' => 7, 'name' => 'Arial'],
                    ['spaceAfter' => 80]
                );
            }
        }

        // ========== KUNCI JAWABAN (2 KOLOM - matching PDF) ==========
        $section->addText('');
        $section->addText(
            'KUNCI JAWABAN',
            ['bold' => true, 'size' => 11, 'name' => 'Arial'],
            ['alignment' => Jc::CENTER, 'spaceAfter' => 120]
        );

        if (!empty($content['kunci_jawaban'])) {
            // 2-column layout: Left = PG + PG Kompleks, Right = Menjodohkan + Uraian
            $kunciTable = $section->addTable(['borderSize' => 0]);
            $kunciTable->addRow();
            
            // Left column
            $kunciLeft = $kunciTable->addCell($halfPageWidth, ['borderRightSize' => 4, 'borderRightColor' => 'BBBBBB', 'borderRightStyle' => 'dotted']);
            
            if (!empty($content['kunci_jawaban']['pilihan_ganda'])) {
                $kunciLeft->addText('A. Pilihan Ganda', ['bold' => true, 'size' => 8.5, 'name' => 'Arial'], ['spaceAfter' => 40]);
                $answers = [];
                foreach ($content['kunci_jawaban']['pilihan_ganda'] as $i => $kunci) {
                    $answers[] = ($i + 1) . '. ' . $kunci;
                }
                $kunciLeft->addText(implode('  |  ', $answers), ['size' => 8, 'name' => 'Arial'], ['spaceAfter' => 80]);
            }

            if (!empty($content['kunci_jawaban']['pg_kompleks'])) {
                $kunciLeft->addText('B. Pilihan Ganda Kompleks', ['bold' => true, 'size' => 8.5, 'name' => 'Arial'], ['spaceAfter' => 40]);
                foreach ($content['kunci_jawaban']['pg_kompleks'] as $i => $item) {
                    $kunciLeft->addText(($i + 1) . '. ' . ($item['jawaban'] ?? ''), ['size' => 8, 'name' => 'Arial'], ['spaceAfter' => 20]);
                }
            }

            // Right column
            $kunciRight = $kunciTable->addCell($halfPageWidth);

            if (!empty($content['kunci_jawaban']['menjodohkan'])) {
                $kunciRight->addText('C. Menjodohkan', ['bold' => true, 'size' => 8.5, 'name' => 'Arial'], ['spaceAfter' => 40]);
                $kunciRight->addText(implode(', ', $content['kunci_jawaban']['menjodohkan']), ['size' => 8, 'name' => 'Arial'], ['spaceAfter' => 80]);
            }

            if (!empty($content['kunci_jawaban']['uraian'])) {
                $kunciRight->addText('D. Uraian', ['bold' => true, 'size' => 8.5, 'name' => 'Arial'], ['spaceAfter' => 40]);
                foreach ($content['kunci_jawaban']['uraian'] as $i => $item) {
                    $kunciRight->addText(($i + 1) . '. ' . ($item['jawaban'] ?? ''), ['size' => 8, 'name' => 'Arial'], ['spaceAfter' => 20]);
                }
            }
        }

        $filename = 'STS_' . str_replace(' ', '_', $sts->mata_pelajaran) . '_' . $sts->kelas . '_' . date('Ymd') . '.docx';
        $tempFile = storage_path('app/temp/' . $filename);
        
        if (!file_exists(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempFile);

        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
