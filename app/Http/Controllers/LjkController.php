<?php

namespace App\Http\Controllers;

use App\Models\LjkTemplate;
use App\Models\SchoolSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class LjkController extends Controller
{
    /**
     * Display a listing of LJK templates.
     */
    public function index()
    {
        $templates = LjkTemplate::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('ljk.index', compact('templates'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        $mataPelajaranList = LjkTemplate::defaultMataPelajaranList();
        
        return view('ljk.create', compact('mataPelajaranList'));
    }

    /**
     * Store a newly created template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_template' => ['required', 'string', 'max:255'],
            'kop_image' => ['nullable', 'image', 'max:2048'],
            'jenis_ujian' => ['required', 'string', 'max:50'],
            'tahun_ajaran' => ['nullable', 'string', 'max:20'],
            'jumlah_soal' => ['required', 'integer', 'min:5', 'max:100'],
            'jumlah_pilihan' => ['required', 'integer', 'min:2', 'max:5'],
            'mata_pelajaran_list' => ['nullable', 'array'],
            'show_essay_lines' => ['boolean'],
        ]);

        // Handle kop image upload
        if ($request->hasFile('kop_image')) {
            $validated['kop_image'] = $request->file('kop_image')->store('ljk/kop', 'public');
        }

        $validated['user_id'] = auth()->id();
        $validated['show_essay_lines'] = $request->boolean('show_essay_lines');

        $template = LjkTemplate::create($validated);

        return redirect()
            ->route('ljk.show', $template)
            ->with('success', 'Template LJK berhasil dibuat.');
    }

    /**
     * Display the specified template.
     */
    public function show(LjkTemplate $ljk)
    {
        $this->authorize('view', $ljk);

        return view('ljk.show', compact('ljk'));
    }

    /**
     * Show the form for editing the template.
     */
    public function edit(LjkTemplate $ljk)
    {
        $this->authorize('update', $ljk);

        $mataPelajaranList = LjkTemplate::defaultMataPelajaranList();

        return view('ljk.edit', compact('ljk', 'mataPelajaranList'));
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, LjkTemplate $ljk)
    {
        $this->authorize('update', $ljk);

        $validated = $request->validate([
            'nama_template' => ['required', 'string', 'max:255'],
            'kop_image' => ['nullable', 'image', 'max:2048'],
            'jenis_ujian' => ['required', 'string', 'max:50'],
            'tahun_ajaran' => ['nullable', 'string', 'max:20'],
            'jumlah_soal' => ['required', 'integer', 'min:5', 'max:100'],
            'jumlah_pilihan' => ['required', 'integer', 'min:2', 'max:5'],
            'mata_pelajaran_list' => ['nullable', 'array'],
            'show_essay_lines' => ['boolean'],
        ]);

        // Handle kop image upload
        if ($request->hasFile('kop_image')) {
            // Delete old image
            if ($ljk->kop_image) {
                Storage::disk('public')->delete($ljk->kop_image);
            }
            $validated['kop_image'] = $request->file('kop_image')->store('ljk/kop', 'public');
        }

        $validated['show_essay_lines'] = $request->boolean('show_essay_lines');

        $ljk->update($validated);

        return redirect()
            ->route('ljk.show', $ljk)
            ->with('success', 'Template LJK berhasil diperbarui.');
    }

    /**
     * Remove the specified template.
     */
    public function destroy(LjkTemplate $ljk)
    {
        $this->authorize('delete', $ljk);

        // Delete kop image
        if ($ljk->kop_image) {
            Storage::disk('public')->delete($ljk->kop_image);
        }

        $ljk->delete();

        return redirect()
            ->route('ljk.index')
            ->with('success', 'Template LJK berhasil dihapus.');
    }

    /**
     * Generate printable PDF LJK.
     */
    public function print(LjkTemplate $ljk)
    {
        $this->authorize('view', $ljk);

        $schoolSettings = SchoolSetting::first();

        $pdf = Pdf::loadView('ljk.print', [
            'template' => $ljk,
            'schoolSettings' => $schoolSettings,
        ]);

        $pdf->setPaper('a4', 'portrait');

        // Sanitize filename - remove invalid characters
        $filename = preg_replace('/[\/\\\\]/', '', $ljk->nama_template);
        $filename = preg_replace('/\s+/', '-', $filename);
        $filename = 'LJK-' . $filename . '.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Preview LJK in browser.
     */
    public function preview(LjkTemplate $ljk)
    {
        $this->authorize('view', $ljk);

        $schoolSettings = SchoolSetting::first();

        return view('ljk.print', [
            'template' => $ljk,
            'schoolSettings' => $schoolSettings,
            'preview' => true,
        ]);
    }
}
