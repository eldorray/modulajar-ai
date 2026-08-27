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
