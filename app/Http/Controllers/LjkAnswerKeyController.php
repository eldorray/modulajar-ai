<?php

namespace App\Http\Controllers;

use App\Models\LjkAnswerKey;
use App\Models\LjkTemplate;
use Illuminate\Http\Request;

class LjkAnswerKeyController extends Controller
{
    /**
     * Display a listing of answer keys.
     */
    public function index()
    {
        $answerKeys = LjkAnswerKey::where('user_id', auth()->id())
            ->with('template')
            ->latest()
            ->paginate(15);

        return view('ljk.answer-keys.index', compact('answerKeys'));
    }

    /**
     * Show the form for creating a new answer key.
     */
    public function create(Request $request)
    {
        $templates = LjkTemplate::where('user_id', auth()->id())->get();
        $selectedTemplate = null;

        if ($request->has('template_id')) {
            $selectedTemplate = LjkTemplate::find($request->template_id);
        }

        return view('ljk.answer-keys.create', compact('templates', 'selectedTemplate'));
    }

    /**
     * Store a newly created answer key.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'ljk_template_id' => ['nullable', 'exists:ljk_templates,id'],
            'mata_pelajaran' => ['required', 'string', 'max:255'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'jumlah_soal' => ['required', 'integer', 'min:5', 'max:100'],
            'jumlah_pilihan' => ['required', 'integer', 'min:2', 'max:5'],
            'kunci_jawaban' => ['required', 'array', 'min:1'],
            'kunci_jawaban.*' => ['required', 'string', 'max:1'],
        ]);

        $validated['user_id'] = auth()->id();

        $answerKey = LjkAnswerKey::create($validated);

        return redirect()
            ->route('ljk-answer-keys.show', $answerKey)
            ->with('success', 'Kunci jawaban berhasil dibuat.');
    }

    /**
     * Display the specified answer key.
     */
    public function show(LjkAnswerKey $ljkAnswerKey)
    {
        $this->authorize('view', $ljkAnswerKey);

        $ljkAnswerKey->load('template', 'results');

        return view('ljk.answer-keys.show', compact('ljkAnswerKey'));
    }

    /**
     * Show the form for editing the answer key.
     */
    public function edit(LjkAnswerKey $ljkAnswerKey)
    {
        $this->authorize('update', $ljkAnswerKey);

        $templates = LjkTemplate::where('user_id', auth()->id())->get();

        return view('ljk.answer-keys.edit', compact('ljkAnswerKey', 'templates'));
    }

    /**
     * Update the specified answer key.
     */
    public function update(Request $request, LjkAnswerKey $ljkAnswerKey)
    {
        $this->authorize('update', $ljkAnswerKey);

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'ljk_template_id' => ['nullable', 'exists:ljk_templates,id'],
            'mata_pelajaran' => ['required', 'string', 'max:255'],
            'kelas' => ['nullable', 'string', 'max:50'],
            'jumlah_soal' => ['required', 'integer', 'min:5', 'max:100'],
            'jumlah_pilihan' => ['required', 'integer', 'min:2', 'max:5'],
            'kunci_jawaban' => ['required', 'array', 'min:1'],
            'kunci_jawaban.*' => ['required', 'string', 'max:1'],
        ]);

        $ljkAnswerKey->update($validated);

        return redirect()
            ->route('ljk-answer-keys.show', $ljkAnswerKey)
            ->with('success', 'Kunci jawaban berhasil diperbarui.');
    }

    /**
     * Remove the specified answer key.
     */
    public function destroy(LjkAnswerKey $ljkAnswerKey)
    {
        $this->authorize('delete', $ljkAnswerKey);

        $ljkAnswerKey->delete();

        return redirect()
            ->route('ljk-answer-keys.index')
            ->with('success', 'Kunci jawaban berhasil dihapus.');
    }
}
