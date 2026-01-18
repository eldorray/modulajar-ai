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
            'fase' => 'required|string|in:A,B,C,D,E,F',
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
}

