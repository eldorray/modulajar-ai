<?php

namespace App\Http\Controllers;

use App\Models\Rpp;
use App\Models\SchoolSetting;
use App\Services\GeminiService;
use App\Services\DeepSeekService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RppController extends Controller
{
    protected $aiService;

    public function __construct(GeminiService $geminiService, DeepSeekService $deepSeekService)
    {
        // Select AI provider based on configuration
        $provider = config('ai.default', 'gemini');
        
        $this->aiService = match ($provider) {
            'deepseek' => $deepSeekService,
            default => $geminiService,
        };
    }

    /**
     * Display a listing of RPPs.
     */
    public function index()
    {
        $rpps = Rpp::forUser(Auth::id())
            ->latest()
            ->paginate(10);

        return view('rpp.index', compact('rpps'));
    }

    /**
     * Show the form for creating a new RPP.
     */
    public function create()
    {
        return view('rpp.create');
    }

    /**
     * Store a newly created RPP.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_guru' => 'required|string|max:255',
            'kepala_sekolah' => 'nullable|string|max:255',
            'nip_kepala_sekolah' => 'nullable|string|max:50',
            'kota' => 'nullable|string|max:100',
            'tanggal' => 'nullable|date',
            'mata_pelajaran' => 'required|string|max:255',
            'fase' => 'required|string|in:A,B,C,D,E,F,RA,MI Rendah,MI Tinggi,MTs,MA',
            'kelas' => 'nullable|string|max:20',
            'semester' => 'nullable|string|in:Ganjil,Genap',
            'target_peserta_didik' => 'nullable|string|max:100',
            'topik' => 'required|string|max:1000',
            'alokasi_waktu' => 'required|string|max:100',
            'jumlah_pertemuan' => 'nullable|integer|min:1|max:10',
            'kompetensi_awal' => 'nullable|string|max:1000',
            'kata_kunci' => 'nullable|string|max:500',
            'model_pembelajaran' => 'nullable|string|max:255',
            'jenis_asesmen' => 'nullable|string|max:255',
            'kurikulum' => 'required|string|max:255',
        ]);

        // Create RPP with processing status
        $rpp = Rpp::create([
            'user_id' => Auth::id(),
            'nama_guru' => $validated['nama_guru'],
            'kepala_sekolah' => $validated['kepala_sekolah'] ?? null,
            'nip_kepala_sekolah' => $validated['nip_kepala_sekolah'] ?? null,
            'kota' => $validated['kota'] ?? null,
            'tanggal' => $validated['tanggal'] ?? now(),
            'mata_pelajaran' => $validated['mata_pelajaran'],
            'fase' => $validated['fase'],
            'kelas' => $validated['kelas'] ?? null,
            'semester' => $validated['semester'] ?? null,
            'target_peserta_didik' => $validated['target_peserta_didik'] ?? 'Reguler',
            'topik' => $validated['topik'],
            'alokasi_waktu' => $validated['alokasi_waktu'],
            'jumlah_pertemuan' => $validated['jumlah_pertemuan'] ?? 1,
            'kompetensi_awal' => $validated['kompetensi_awal'] ?? null,
            'kata_kunci' => $validated['kata_kunci'] ?? null,
            'model_pembelajaran' => $validated['model_pembelajaran'] ?? 'Problem Based Learning',
            'jenis_asesmen' => $validated['jenis_asesmen'] ?? 'Formatif dan Sumatif',
            'kurikulum' => $validated['kurikulum'],
            'status' => 'processing',
        ]);

        // Increase time limit for AI generation (3 minutes)
        set_time_limit(180);

        // Generate RPP using AI
        $result = $this->aiService->generateRPP(
            $validated,
            Auth::id(),
            $rpp->id
        );

        if ($result['success'] && $result['content']) {
            $rpp->update([
                'content_result' => $result['content'],
                'status' => 'completed',
            ]);

            // Return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'redirect_url' => route('rpp.show', $rpp),
                    'message' => 'RPP berhasil dibuat!',
                ]);
            }

            return redirect()
                ->route('rpp.show', $rpp)
                ->with('success', 'RPP berhasil dibuat!');
        }

        $rpp->update(['status' => 'failed']);

        $errorMessage = $result['error'] ?? 'Gagal membuat RPP. Silakan coba lagi.';

        // Return JSON for AJAX requests
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => false,
                'error' => $errorMessage,
            ], 422);
        }

        return back()
            ->withInput()
            ->with('error', $errorMessage);
    }

    /**
     * Display the specified RPP.
     */
    public function show(Rpp $rpp)
    {
        // Ensure user can only view their own RPPs
        if ($rpp->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('rpp.show', compact('rpp'));
    }

    /**
     * Download RPP as PDF.
     */
    public function downloadPdf(Rpp $rpp)
    {
        // Ensure user can only download their own RPPs
        if ($rpp->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $schoolSettings = SchoolSetting::getSettings();
        
        $pdf = Pdf::loadView('rpp.pdf', compact('rpp', 'schoolSettings'));
        
        // Set paper size and margins (in points: 1cm = 28.35pt)
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOption('margin-top', 85);      // 3cm = ~85pt
        $pdf->setOption('margin-bottom', 71);   // 2.5cm = ~71pt
        $pdf->setOption('margin-left', 85);     // 3cm = ~85pt
        $pdf->setOption('margin-right', 71);    // 2.5cm = ~71pt
        
        $filename = 'ModulAjar_' . str_replace(' ', '_', $rpp->mata_pelajaran) . '_' . $rpp->id . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Delete the specified RPP.
     */
    public function destroy(Rpp $rpp)
    {
        if ($rpp->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $rpp->delete();

        return redirect()
            ->route('rpp.index')
            ->with('success', 'RPP berhasil dihapus.');
    }

    /**
     * Download RPP as Word document.
     */
    public function downloadWord(Rpp $rpp)
    {
        if ($rpp->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($rpp->status !== 'completed' || !$rpp->content_result) {
            return redirect()->route('rpp.show', $rpp)
                ->with('error', 'Modul Ajar belum selesai di-generate.');
        }

        $schoolSettings = SchoolSetting::getSettings();
        $content = $rpp->content_result;

        // Create new PHPWord instance
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        
        // Set default font
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(12);

        // Add a section
        $section = $phpWord->addSection([
            'marginTop' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(3),
            'marginBottom' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2.5),
            'marginLeft' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(3),
            'marginRight' => \PhpOffice\PhpWord\Shared\Converter::cmToTwip(2.5),
        ]);

        // Title styles
        $titleStyle = ['bold' => true, 'size' => 14, 'allCaps' => true];
        $headingStyle = ['bold' => true, 'size' => 12];
        $subHeadingStyle = ['bold' => true, 'size' => 11];

        // Title
        $section->addText('MODUL AJAR', $titleStyle, ['alignment' => 'center']);
        $section->addText($rpp->mata_pelajaran, $titleStyle, ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Informasi Umum
        $section->addText('INFORMASI UMUM', $headingStyle);
        $section->addText('Nama Penyusun: ' . $rpp->nama_guru);
        $section->addText('Mata Pelajaran: ' . $rpp->mata_pelajaran);
        $section->addText('Fase/Kelas: ' . $rpp->fase . ($rpp->kelas ? ' / Kelas ' . $rpp->kelas : ''));
        $section->addText('Alokasi Waktu: ' . $rpp->alokasi_waktu);
        $section->addText('Model Pembelajaran: ' . $rpp->model_pembelajaran);
        if ($rpp->kurikulum) {
            $section->addText('Kurikulum: ' . $rpp->kurikulum);
        }
        $section->addTextBreak(1);

        // Kompetensi Awal
        if (isset($content['kompetensi_awal']) && $content['kompetensi_awal']) {
            $section->addText('KOMPETENSI AWAL', $headingStyle);
            $section->addText($content['kompetensi_awal']);
            $section->addTextBreak(1);
        }

        // Profil Pelajar Pancasila
        if (isset($content['profil_pelajar_pancasila'])) {
            $section->addText('PROFIL PELAJAR PANCASILA', $headingStyle);
            foreach ($content['profil_pelajar_pancasila'] as $profil) {
                if (is_array($profil)) {
                    $section->addListItem(($profil['dimensi'] ?? '') . ': ' . ($profil['deskripsi'] ?? ''));
                } else {
                    $section->addListItem($profil);
                }
            }
            $section->addTextBreak(1);
        }

        // Tujuan Pembelajaran
        if (isset($content['tujuan_pembelajaran'])) {
            $section->addText('TUJUAN PEMBELAJARAN', $headingStyle);
            foreach ($content['tujuan_pembelajaran'] as $tujuan) {
                $section->addListItem($tujuan);
            }
            $section->addTextBreak(1);
        }

        // Pemahaman Bermakna
        if (isset($content['pemahaman_bermakna']) && $content['pemahaman_bermakna']) {
            $section->addText('PEMAHAMAN BERMAKNA', $headingStyle);
            $section->addText($content['pemahaman_bermakna']);
            $section->addTextBreak(1);
        }

        // Pertanyaan Pemantik
        if (isset($content['pertanyaan_pemantik'])) {
            $section->addText('PERTANYAAN PEMANTIK', $headingStyle);
            foreach ($content['pertanyaan_pemantik'] as $i => $pertanyaan) {
                $section->addListItem($pertanyaan, 0, null, ['listType' => \PhpOffice\PhpWord\Style\ListItem::TYPE_NUMBER]);
            }
            $section->addTextBreak(1);
        }

        // Kegiatan Pembelajaran
        if (isset($content['kegiatan_pembelajaran'])) {
            $section->addText('KEGIATAN PEMBELAJARAN', $headingStyle);
            
            // Pendahuluan
            if (isset($content['kegiatan_pembelajaran']['pendahuluan'])) {
                $pendahuluan = $content['kegiatan_pembelajaran']['pendahuluan'];
                $section->addText('A. Pendahuluan (' . ($pendahuluan['durasi'] ?? '') . ')', $subHeadingStyle);
                $aktivitas = $pendahuluan['aktivitas'] ?? [];
                foreach ($aktivitas as $akt) {
                    if (is_array($akt)) {
                        $section->addText('• Guru: ' . ($akt['kegiatan_guru'] ?? ''));
                        $section->addText('  Siswa: ' . ($akt['kegiatan_siswa'] ?? ''));
                    } else {
                        $section->addListItem($akt);
                    }
                }
            }

            // Inti
            if (isset($content['kegiatan_pembelajaran']['inti'])) {
                $inti = $content['kegiatan_pembelajaran']['inti'];
                $section->addTextBreak(1);
                $section->addText('B. Kegiatan Inti (' . ($inti['durasi'] ?? '') . ')', $subHeadingStyle);
                $aktivitas = $inti['aktivitas'] ?? [];
                foreach ($aktivitas as $akt) {
                    if (is_array($akt)) {
                        if (isset($akt['fase_sintaks'])) {
                            $section->addText($akt['fase_sintaks'], ['italic' => true]);
                        }
                        $section->addText('• Guru: ' . ($akt['kegiatan_guru'] ?? ''));
                        $section->addText('  Siswa: ' . ($akt['kegiatan_siswa'] ?? ''));
                    } else {
                        $section->addListItem($akt);
                    }
                }
            }

            // Penutup
            if (isset($content['kegiatan_pembelajaran']['penutup'])) {
                $penutup = $content['kegiatan_pembelajaran']['penutup'];
                $section->addTextBreak(1);
                $section->addText('C. Penutup (' . ($penutup['durasi'] ?? '') . ')', $subHeadingStyle);
                $aktivitas = $penutup['aktivitas'] ?? [];
                foreach ($aktivitas as $akt) {
                    if (is_array($akt)) {
                        $section->addText('• Guru: ' . ($akt['kegiatan_guru'] ?? ''));
                        $section->addText('  Siswa: ' . ($akt['kegiatan_siswa'] ?? ''));
                    } else {
                        $section->addListItem($akt);
                    }
                }
            }
            $section->addTextBreak(1);
        }

        // Asesmen
        if (isset($content['asesmen'])) {
            $section->addText('ASESMEN', $headingStyle);
            $section->addText('Jenis: ' . ($content['asesmen']['jenis'] ?? '-'));
            $teknik = $content['asesmen']['teknik'] ?? '-';
            if (is_array($teknik)) {
                $teknik = implode(', ', $teknik);
            }
            $section->addText('Teknik: ' . $teknik);
            if (isset($content['asesmen']['bentuk'])) {
                $section->addText('Bentuk: ' . $content['asesmen']['bentuk']);
            }
            $section->addTextBreak(1);
        }

        // Refleksi
        if (isset($content['refleksi'])) {
            $section->addText('REFLEKSI', $headingStyle);
            if (isset($content['refleksi']['refleksi_siswa'])) {
                $section->addText('Refleksi Siswa:', $subHeadingStyle);
                foreach ($content['refleksi']['refleksi_siswa'] as $item) {
                    $section->addListItem($item);
                }
            }
            if (isset($content['refleksi']['refleksi_guru'])) {
                $section->addText('Refleksi Guru:', $subHeadingStyle);
                foreach ($content['refleksi']['refleksi_guru'] as $item) {
                    $section->addListItem($item);
                }
            }
            $section->addTextBreak(1);
        }

        // Daftar Pustaka
        if (isset($content['daftar_pustaka']) && count($content['daftar_pustaka']) > 0) {
            $section->addText('DAFTAR PUSTAKA', $headingStyle);
            foreach ($content['daftar_pustaka'] as $pustaka) {
                $section->addListItem($pustaka);
            }
        }

        // Generate filename
        $filename = 'ModulAjar_' . str_replace(' ', '_', $rpp->mata_pelajaran) . '_' . $rpp->id . '.docx';

        // Save to temp file and download
        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }
}

