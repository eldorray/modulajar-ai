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
        set_time_limit(300);

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

        if ($result['success']) {
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
        
        $pdf->setPaper('A4', 'portrait');
        
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
        
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        // Title styles
        $phpWord->addTitleStyle(1, ['bold' => true, 'size' => 14], ['alignment' => Jc::CENTER]);
        $phpWord->addTitleStyle(2, ['bold' => true, 'size' => 12], ['alignment' => Jc::LEFT]);

        $section = $phpWord->addSection([
            'marginTop' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
            'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
            'marginLeft' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
            'marginRight' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2),
        ]);

        // ========== KOP SURAT / HEADER ==========
        if ($schoolSettings->kop_surat && file_exists(storage_path('app/public/' . $schoolSettings->kop_surat))) {
            // Use uploaded kop surat image - maintain proportions
            $imagePath = storage_path('app/public/' . $schoolSettings->kop_surat);
            $imageInfo = getimagesize($imagePath);
            $originalWidth = $imageInfo[0];
            $originalHeight = $imageInfo[1];
            
            // Scale to fit width of ~450px while maintaining aspect ratio
            $targetWidth = 450;
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
            // Generate header from components
            $headerTable = $section->addTable(['alignment' => Jc::CENTER]);
            $headerTable->addRow();
            
            // Logo Kiri
            $cellLeft = $headerTable->addCell(1500);
            if ($schoolSettings->logo && file_exists(storage_path('app/public/' . $schoolSettings->logo))) {
                $cellLeft->addImage(
                    storage_path('app/public/' . $schoolSettings->logo),
                    ['width' => 50, 'height' => 50, 'alignment' => Jc::CENTER]
                );
            }
            
            // Center - School Info
            $cellCenter = $headerTable->addCell(6000);
            if ($schoolSettings->nama_sekolah) {
                $cellCenter->addText(
                    strtoupper($schoolSettings->nama_sekolah),
                    ['bold' => true, 'size' => 14],
                    ['alignment' => Jc::CENTER]
                );
            }
            if ($schoolSettings->npsn || $schoolSettings->nsm) {
                $info = [];
                if ($schoolSettings->npsn) $info[] = "NPSN: {$schoolSettings->npsn}";
                if ($schoolSettings->nsm) $info[] = "NSM: {$schoolSettings->nsm}";
                $cellCenter->addText(implode(' | ', $info), ['size' => 10], ['alignment' => Jc::CENTER]);
            }
            if ($schoolSettings->alamat) {
                $cellCenter->addText($schoolSettings->alamat, ['size' => 10], ['alignment' => Jc::CENTER]);
            }
            
            // Logo Kanan
            $cellRight = $headerTable->addCell(1500);
            if ($schoolSettings->logo_kanan && file_exists(storage_path('app/public/' . $schoolSettings->logo_kanan))) {
                $cellRight->addImage(
                    storage_path('app/public/' . $schoolSettings->logo_kanan),
                    ['width' => 50, 'height' => 50, 'alignment' => Jc::CENTER]
                );
            }
        }

        // Line separator
        $section->addText('');
        $section->addText('_______________________________________________________________________________', [], ['alignment' => Jc::CENTER]);
        $section->addText('');

        // ========== TITLE ==========
        $section->addText(
            'SUMATIF TENGAH SEMESTER (STS)',
            ['bold' => true, 'size' => 14],
            ['alignment' => Jc::CENTER]
        );
        $section->addText(
            'TAHUN PELAJARAN ' . date('Y') . '/' . (date('Y') + 1),
            ['size' => 11],
            ['alignment' => Jc::CENTER]
        );
        $section->addText('');

        // ========== INFO TABLE ==========
        $infoTable = $section->addTable();
        $infoTable->addRow();
        $infoTable->addCell(2500)->addText('Mata Pelajaran', ['bold' => true]);
        $infoTable->addCell(200)->addText(':');
        $infoTable->addCell(3000)->addText($sts->mata_pelajaran);
        $infoTable->addCell(2000)->addText('Nama', ['bold' => true]);
        $infoTable->addCell(200)->addText(':');
        $infoTable->addCell(2500)->addText('.............................');
        
        $infoTable->addRow();
        $infoTable->addCell(2500)->addText('Kelas', ['bold' => true]);
        $infoTable->addCell(200)->addText(':');
        $infoTable->addCell(3000)->addText($sts->kelas);
        $infoTable->addCell(2000)->addText('Hari, Tanggal', ['bold' => true]);
        $infoTable->addCell(200)->addText(':');
        $infoTable->addCell(2500)->addText('.............................');
        
        $section->addText('');

        // ========== INSTRUCTION ==========
        $section->addText(
            'Berilah tanda silang (X) pada huruf A, B, C, atau D di depan jawaban yang paling tepat!',
            ['bold' => true],
            ['shading' => ['fill' => 'EEEEEE'], 'indentation' => ['left' => 200, 'right' => 200]]
        );
        $section->addText('');

        // ========== SOAL PILIHAN GANDA (2 Columns) ==========
        if (!empty($content['soal_pilihan_ganda'])) {
            $section->addText('I. PILIHAN GANDA', ['bold' => true, 'size' => 12], ['shading' => ['fill' => 'DDDDDD']]);
            $section->addText('');

            $pgSoal = $content['soal_pilihan_ganda'];
            $totalPg = count($pgSoal);
            $halfPoint = ceil($totalPg / 2);

            // Create 2-column table
            $pgTable = $section->addTable();
            $pgTable->addRow();
            
            // Left column
            $leftCell = $pgTable->addCell(5000);
            for ($i = 0; $i < $halfPoint && $i < $totalPg; $i++) {
                $soal = $pgSoal[$i];
                $leftCell->addText(($i + 1) . '. ' . ($soal['pertanyaan'] ?? ''), ['size' => 11]);
                if (!empty($soal['pilihan'])) {
                    $options = [];
                    foreach ($soal['pilihan'] as $key => $pilihan) {
                        $options[] = "{$key}. {$pilihan}";
                    }
                    $leftCell->addText('    ' . implode('   ', $options), ['size' => 10]);
                }
                $leftCell->addText('');
            }
            
            // Right column
            $rightCell = $pgTable->addCell(5000);
            for ($i = $halfPoint; $i < $totalPg; $i++) {
                $soal = $pgSoal[$i];
                $rightCell->addText(($i + 1) . '. ' . ($soal['pertanyaan'] ?? ''), ['size' => 11]);
                if (!empty($soal['pilihan'])) {
                    $options = [];
                    foreach ($soal['pilihan'] as $key => $pilihan) {
                        $options[] = "{$key}. {$pilihan}";
                    }
                    $rightCell->addText('    ' . implode('   ', $options), ['size' => 10]);
                }
                $rightCell->addText('');
            }
            
            $section->addText('');
        }

        // ========== SOAL PG KOMPLEKS ==========
        if (!empty($content['soal_pg_kompleks'])) {
            $section->addText('II. PILIHAN GANDA KOMPLEKS', ['bold' => true, 'size' => 12], ['shading' => ['fill' => 'DDDDDD']]);
            $section->addText('Tentukan pernyataan berikut Benar atau Salah!', ['italic' => true]);
            $section->addText('');

            foreach ($content['soal_pg_kompleks'] as $index => $soal) {
                $section->addText(($index + 1) . '. ' . ($soal['pertanyaan'] ?? ''));
                if (!empty($soal['pernyataan'])) {
                    foreach ($soal['pernyataan'] as $p) {
                        $section->addText('    • ' . ($p['teks'] ?? '') . ' (................)');
                    }
                }
                $section->addText('');
            }
        }

        // ========== SOAL MENJODOHKAN ==========
        if (!empty($content['soal_menjodohkan'])) {
            $section->addText('III. MENJODOHKAN', ['bold' => true, 'size' => 12], ['shading' => ['fill' => 'DDDDDD']]);
            $section->addText('Jodohkan pernyataan di kolom kiri dengan jawaban di kolom kanan!', ['italic' => true]);
            $section->addText('');

            $matchTable = $section->addTable(['borderSize' => 6, 'borderColor' => '000000']);
            $matchTable->addRow();
            $matchTable->addCell(500, ['shading' => ['fill' => 'DDDDDD']])->addText('No', ['bold' => true]);
            $matchTable->addCell(4000, ['shading' => ['fill' => 'DDDDDD']])->addText('Soal', ['bold' => true]);
            $matchTable->addCell(1000, ['shading' => ['fill' => 'DDDDDD']])->addText('Jawaban', ['bold' => true]);
            $matchTable->addCell(4000, ['shading' => ['fill' => 'DDDDDD']])->addText('Pilihan', ['bold' => true]);

            foreach ($content['soal_menjodohkan'] as $index => $soal) {
                $matchTable->addRow();
                $matchTable->addCell(500)->addText($index + 1);
                $matchTable->addCell(4000)->addText($soal['soal'] ?? '');
                $matchTable->addCell(1000)->addText('(........)');
                $matchTable->addCell(4000)->addText(chr(65 + $index) . '. ' . ($soal['jawaban'] ?? ''));
            }
            $section->addText('');
        }

        // ========== SOAL URAIAN ==========
        if (!empty($content['soal_uraian'])) {
            $section->addText('IV. URAIAN', ['bold' => true, 'size' => 12], ['shading' => ['fill' => 'DDDDDD']]);
            $section->addText('Jawablah pertanyaan berikut dengan jelas dan lengkap!', ['italic' => true]);
            $section->addText('');

            foreach ($content['soal_uraian'] as $index => $soal) {
                $section->addText(($index + 1) . '. ' . ($soal['pertanyaan'] ?? ''));
                $section->addText('');
                $section->addText('');
            }
        }

        // ========== FOOTER ==========
        $section->addText('');
        $section->addText('*** Selamat Mengerjakan ***', ['italic' => true], ['alignment' => Jc::RIGHT]);

        // ========== PAGE BREAK - KUNCI JAWABAN ==========
        $section->addPageBreak();
        $section->addTitle('KUNCI JAWABAN', 1);
        $section->addText('');

        if (!empty($content['kunci_jawaban'])) {
            // PG
            if (!empty($content['kunci_jawaban']['pilihan_ganda'])) {
                $section->addText('A. Pilihan Ganda', ['bold' => true]);
                $answers = [];
                foreach ($content['kunci_jawaban']['pilihan_ganda'] as $i => $kunci) {
                    $answers[] = ($i + 1) . '. ' . $kunci;
                }
                $section->addText(implode('  |  ', $answers));
                $section->addText('');
            }

            // PG Kompleks
            if (!empty($content['kunci_jawaban']['pg_kompleks'])) {
                $section->addText('B. Pilihan Ganda Kompleks', ['bold' => true]);
                foreach ($content['kunci_jawaban']['pg_kompleks'] as $i => $item) {
                    $section->addText(($i + 1) . '. ' . ($item['jawaban'] ?? ''));
                }
                $section->addText('');
            }

            // Menjodohkan
            if (!empty($content['kunci_jawaban']['menjodohkan'])) {
                $section->addText('C. Menjodohkan', ['bold' => true]);
                $section->addText(implode(', ', $content['kunci_jawaban']['menjodohkan']));
                $section->addText('');
            }

            // Uraian
            if (!empty($content['kunci_jawaban']['uraian'])) {
                $section->addText('D. Uraian', ['bold' => true]);
                foreach ($content['kunci_jawaban']['uraian'] as $i => $item) {
                    $section->addText(($i + 1) . '. ' . ($item['jawaban'] ?? ''));
                    $section->addText('');
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
