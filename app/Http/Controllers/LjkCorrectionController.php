<?php

namespace App\Http\Controllers;

use App\Models\LjkAnswerKey;
use App\Models\LjkResult;
use App\Services\GroqService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LjkCorrectionController extends Controller
{
    protected GroqService $groqService;

    public function __construct(GroqService $groqService)
    {
        $this->groqService = $groqService;
    }

    /**
     * Analyze LJK image using AI
     */
    public function analyzeImage(Request $request): \Illuminate\Http\JsonResponse
    {
        $validated = $request->validate([
            'ljk_answer_key_id' => ['required', 'exists:ljk_answer_keys,id'],
            'image' => ['required', 'string'], // base64 image
        ]);

        $answerKey = LjkAnswerKey::findOrFail($validated['ljk_answer_key_id']);

        $result = $this->groqService->analyzeJawaban(
            $validated['image'],
            $answerKey->jumlah_soal,
            $answerKey->jumlah_pilihan
        );

        return response()->json($result);
    }

    /**
     * Display the correction home page.
     */
    public function index()
    {
        $answerKeys = LjkAnswerKey::where('user_id', auth()->id())
            ->latest()
            ->get();

        $recentResults = LjkResult::where('user_id', auth()->id())
            ->with('answerKey')
            ->latest()
            ->take(10)
            ->get();

        return view('ljk.correction.index', compact('answerKeys', 'recentResults'));
    }

    /**
     * Show the camera scan interface.
     */
    public function scan(LjkAnswerKey $answerKey)
    {
        return view('ljk.correction.scan', compact('answerKey'));
    }

    /**
     * Process the scanned/uploaded image and grade.
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'ljk_answer_key_id' => ['required', 'exists:ljk_answer_keys,id'],
            'nama_peserta' => ['nullable', 'string', 'max:255'],
            'nomor_peserta' => ['nullable', 'string', 'max:50'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'jawaban_siswa' => ['required', 'array'],
            'jawaban_siswa.*' => ['nullable', 'string', 'max:1'],
            'scan_image' => ['nullable', 'string'], // base64 encoded image
        ]);

        $answerKey = LjkAnswerKey::findOrFail($validated['ljk_answer_key_id']);

        // Process answers (convert empty strings to null)
        $studentAnswers = array_map(function ($answer) {
            return empty($answer) ? null : strtoupper($answer);
        }, $validated['jawaban_siswa']);

        // Grade the answers
        $gradeResult = $answerKey->gradeAnswers($studentAnswers);

        // Save scan image if provided (base64)
        $scanImagePath = null;
        if (!empty($validated['scan_image'])) {
            $scanImagePath = $this->saveBase64Image($validated['scan_image']);
        }

        // Create result record
        $result = LjkResult::create([
            'ljk_answer_key_id' => $answerKey->id,
            'user_id' => auth()->id(),
            'nama_peserta' => $validated['nama_peserta'],
            'nomor_peserta' => $validated['nomor_peserta'],
            'kelas' => $validated['kelas'],
            'jawaban_siswa' => $studentAnswers,
            'jumlah_benar' => $gradeResult['jumlah_benar'],
            'jumlah_salah' => $gradeResult['jumlah_salah'],
            'jumlah_kosong' => $gradeResult['jumlah_kosong'],
            'skor' => $gradeResult['skor'],
            'scan_image' => $scanImagePath,
        ]);

        // Return JSON for AJAX requests
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'result' => $result,
                'grade_details' => $gradeResult,
                'redirect_url' => route('ljk.correction.result', $result),
            ]);
        }

        return redirect()
            ->route('ljk.correction.result', $result)
            ->with('success', 'Koreksi berhasil! Skor: ' . $gradeResult['skor']);
    }

    /**
     * Manual input correction (without camera).
     */
    public function manual(LjkAnswerKey $answerKey)
    {
        return view('ljk.correction.manual', compact('answerKey'));
    }

    /**
     * Display the result of correction.
     */
    public function result(LjkResult $result)
    {
        $result->load('answerKey');

        $gradeDetails = $result->answerKey->gradeAnswers($result->jawaban_siswa ?? []);

        return view('ljk.correction.result', compact('result', 'gradeDetails'));
    }

    /**
     * List all results.
     */
    public function results(Request $request)
    {
        $query = LjkResult::where('user_id', auth()->id())
            ->with('answerKey');

        if ($request->has('answer_key_id')) {
            $query->where('ljk_answer_key_id', $request->answer_key_id);
        }

        $results = $query->latest()->paginate(20);

        $answerKeys = LjkAnswerKey::where('user_id', auth()->id())->get();

        return view('ljk.correction.results', compact('results', 'answerKeys'));
    }

    /**
     * Delete a result.
     */
    public function destroyResult(LjkResult $result)
    {
        if ($result->scan_image) {
            Storage::disk('public')->delete($result->scan_image);
        }

        $result->delete();

        return redirect()
            ->route('ljk.correction.results')
            ->with('success', 'Hasil koreksi berhasil dihapus.');
    }

    /**
     * Export results to Excel.
     */
    public function export(Request $request)
    {
        // TODO: Implement Excel export
        return back()->with('info', 'Fitur export sedang dalam pengembangan.');
    }

    /**
     * Save base64 encoded image.
     */
    protected function saveBase64Image(string $base64Image): string
    {
        // Remove data URL prefix if present
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            $extension = $matches[1];
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
        } else {
            $extension = 'png';
        }

        $imageData = base64_decode($base64Image);
        $filename = 'ljk/scans/' . uniqid() . '.' . $extension;

        Storage::disk('public')->put($filename, $imageData);

        return $filename;
    }
}
