<?php

namespace App\Http\Controllers;

use App\Models\Rpp;
use App\Models\SchoolSetting;
use App\Services\DeepSeekService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RppController extends Controller
{
    protected $aiService;

    public function __construct(DeepSeekService $deepSeekService)
    {
        $this->aiService = $deepSeekService;
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
            'jenis_asesmen' => 'nullable',
            'jenis_asesmen.*' => 'string|in:Diagnostik Kognitif,Diagnostik Non-Kognitif,Formatif,Sumatif',
            'kurikulum' => 'required|string|max:255',
            'tema' => 'nullable|string|in:'.implode(',', array_keys(config('rpp_themes'))),
            'panca_cinta' => 'nullable|boolean',
            'adiwiyata' => 'nullable|boolean',
            'kka' => 'nullable|boolean',
        ]);

        // Optional value-integration flags (checkboxes: present only when checked)
        $validated['panca_cinta'] = $request->boolean('panca_cinta');
        $validated['adiwiyata'] = $request->boolean('adiwiyata');
        $validated['kka'] = $request->boolean('kka');

        // Normalize jenis_asesmen to array → comma-separated string for storage
        $jenisAsesmenRaw = $validated['jenis_asesmen'] ?? [];
        if (is_string($jenisAsesmenRaw)) {
            $jenisAsesmenArray = array_filter(array_map('trim', explode(',', $jenisAsesmenRaw)));
        } else {
            $jenisAsesmenArray = is_array($jenisAsesmenRaw) ? $jenisAsesmenRaw : [];
        }
        if (empty($jenisAsesmenArray)) {
            $jenisAsesmenArray = ['Formatif', 'Sumatif'];
        }
        $validated['jenis_asesmen'] = implode(', ', $jenisAsesmenArray);
        $validated['jenis_asesmen_array'] = $jenisAsesmenArray;

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
            'tema' => $validated['tema'] ?? 'merah',
            'status' => 'processing',
        ]);

        // Increase time limit for AI generation
        $isDeepLearning = ($validated['kurikulum'] ?? '') === 'Kurikulum Merdeka Deep Learning';
        set_time_limit($isDeepLearning ? 360 : 180);

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
        if ($rpp->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
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
        if ($rpp->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $schoolSettings = SchoolSetting::getSettings();

        $isDeepLearning = $rpp->kurikulum === 'Kurikulum Merdeka Deep Learning';
        $viewName = $isDeepLearning ? 'rpp.pdf_deep_learning' : 'rpp.pdf';

        $pdf = Pdf::loadView($viewName, compact('rpp', 'schoolSettings'));

        // Set paper size; margins come from @page CSS rule in the template
        $pdf->setPaper('A4', 'portrait');
        if (! $isDeepLearning) {
            // Modul Ajar: 3cm top/left, 2.5cm right/bottom
            $pdf->setOption('margin-top', 85);
            $pdf->setOption('margin-bottom', 71);
            $pdf->setOption('margin-left', 85);
            $pdf->setOption('margin-right', 71);
        }
        // Deep Learning: margins dari @page rule di template (2cm atas/bawah, 2.5cm kiri/kanan)

        $prefix = $isDeepLearning ? 'RPPM_' : 'ModulAjar_';
        $filename = $prefix.str_replace(' ', '_', $rpp->mata_pelajaran).'_'.$rpp->id.'.pdf';

        return $pdf->download($filename);
    }

    /**
     * Show a print-friendly HTML view that auto-opens the browser print dialog.
     */
    public function print(Rpp $rpp)
    {
        if ($rpp->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($rpp->status !== 'completed' || ! $rpp->content_result) {
            return redirect()->route('rpp.show', $rpp)
                ->with('error', 'Modul Ajar belum selesai di-generate.');
        }

        $schoolSettings = SchoolSetting::getSettings();
        $viewName = $rpp->kurikulum === 'Kurikulum Merdeka Deep Learning'
            ? 'rpp.pdf_deep_learning'
            : 'rpp.pdf';

        return view($viewName, [
            'rpp' => $rpp,
            'schoolSettings' => $schoolSettings,
            'print' => true,
        ]);
    }

    /**
     * Delete the specified RPP.
     */
    public function destroy(Rpp $rpp)
    {
        if ($rpp->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
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
        if ($rpp->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        if ($rpp->status !== 'completed' || ! $rpp->content_result) {
            return redirect()->route('rpp.show', $rpp)
                ->with('error', 'Modul Ajar belum selesai di-generate.');
        }

        $phpWord = app(\App\Services\RppWordExporter::class)
            ->export($rpp, SchoolSetting::getSettings());

        $filename = 'RPPM_'.str_replace(' ', '_', $rpp->mata_pelajaran).'_'.$rpp->id.'.docx';

        $tempFile = tempnam(sys_get_temp_dir(), 'word');
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($tempFile);

        return response()->download($tempFile, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])->deleteFileAfterSend(true);
    }
}
