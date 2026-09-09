<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiSettingController extends Controller
{
    /**
     * Form pengaturan AI.
     */
    public function edit()
    {
        return view('admin.ai.edit', [
            'settings' => AiSetting::getSettings(),
            'effective' => AiSetting::resolved(),
            'envKeySet' => filled(config('deepseek.api_key')),
        ]);
    }

    /**
     * Simpan pengaturan AI.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'api_key' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:100',
            'endpoint' => 'nullable|url|max:255',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'max_tokens' => 'nullable|integer|min:256|max:32768',
        ]);

        $settings = AiSetting::getSettings();

        // API key kosong = pertahankan yang tersimpan
        if (filled($validated['api_key'] ?? null)) {
            $settings->api_key = trim($validated['api_key']);
        }

        $settings->model = $validated['model'] ?? null;
        $settings->endpoint = $validated['endpoint'] ?? null;
        $settings->temperature = $validated['temperature'] ?? null;
        $settings->max_tokens = $validated['max_tokens'] ?? null;
        $settings->save();

        return redirect()->route('admin.ai.edit')
            ->with('success', 'Pengaturan AI berhasil disimpan!');
    }

    /**
     * Hapus API key dari database (kembali memakai nilai .env).
     */
    public function destroyApiKey()
    {
        $settings = AiSetting::getSettings();
        $settings->api_key = null;
        $settings->save();

        return redirect()->route('admin.ai.edit')
            ->with('success', 'API key di database dihapus. Sistem kembali memakai nilai dari .env.');
    }

    /**
     * Ambil daftar model yang tersedia dari provider.
     *
     * Endpoint yang disimpan adalah URL chat/completions penuh, sedangkan daftar
     * model ada di {base}/models — jadi sufiksnya dipotong dulu. Endpoint dan key
     * boleh dikirim dari form supaya admin bisa mengecek nilai baru sebelum
     * menyimpannya; yang kosong diambil dari nilai efektif.
     */
    public function models(Request $request)
    {
        $validated = $request->validate([
            'endpoint' => 'nullable|url:http,https|max:255',
            'api_key' => 'nullable|string|max:255',
        ]);

        $config = AiSetting::resolved();
        $endpoint = filled($validated['endpoint'] ?? null) ? $validated['endpoint'] : $config['endpoint'];
        $apiKey = filled($validated['api_key'] ?? null) ? $validated['api_key'] : $config['api_key'];

        if (blank($apiKey)) {
            return response()->json(['message' => 'API key belum diisi.'], 422);
        }

        if (blank($endpoint)) {
            return response()->json(['message' => 'Endpoint belum diisi.'], 422);
        }

        try {
            $response = Http::withToken($apiKey)
                ->acceptJson()
                ->timeout(20)
                ->get($this->modelsUrl($endpoint));
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Gagal menghubungi provider: '.$e->getMessage()], 502);
        }

        if (! $response->successful()) {
            return response()->json([
                'message' => 'Provider menolak permintaan (HTTP '.$response->status().').',
            ], 502);
        }

        $models = collect($response->json('data') ?? $response->json() ?? [])
            ->map(fn ($item) => is_array($item) ? ($item['id'] ?? $item['name'] ?? null) : $item)
            ->filter(fn ($id) => is_string($id) && $id !== '')
            ->unique()
            ->sort()
            ->values();

        if ($models->isEmpty()) {
            return response()->json(['message' => 'Provider tidak mengembalikan daftar model.'], 502);
        }

        return response()->json(['models' => $models]);
    }

    /**
     * Turunkan URL daftar model dari URL chat/completions.
     */
    private function modelsUrl(string $endpoint): string
    {
        $base = preg_replace('#/chat/completions/?$#', '', rtrim($endpoint, '/'));

        // Endpoint tak berpola standar → buang satu segmen terakhir sebagai perkiraan.
        if ($base === rtrim($endpoint, '/')) {
            $base = rtrim(substr($base, 0, (int) strrpos($base, '/')), '/');
        }

        return $base.'/models';
    }

    /**
     * Tes koneksi ke endpoint AI dengan prompt minimal.
     */
    public function test()
    {
        $config = AiSetting::resolved();

        if (blank($config['api_key'])) {
            return back()->with('error', 'API key belum diisi.');
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$config['api_key'],
                'Content-Type' => 'application/json',
            ])->timeout(20)->post($config['endpoint'], [
                'model' => $config['model'],
                'messages' => [['role' => 'user', 'content' => 'Balas dengan satu kata: OK']],
                'max_tokens' => 10,
            ]);
        } catch (\Throwable $e) {
            return back()->with('error', 'Koneksi gagal: '.$e->getMessage());
        }

        if (! $response->successful()) {
            return back()->with('error', "Gagal ({$response->status()}): ".mb_substr($response->body(), 0, 200));
        }

        $reply = trim($response->json('choices.0.message.content') ?? '');

        return back()->with('success', "Koneksi berhasil. Model {$config['model']} menjawab: \"{$reply}\"");
    }
}
