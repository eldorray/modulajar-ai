<?php

namespace Tests\Feature;

use App\Models\AiSetting;
use App\Models\User;
use App\Services\DeepSeekService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiSettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_hanya_admin_boleh_membuka_pengaturan_ai(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'guru']))
            ->get(route('admin.ai.edit'))
            ->assertForbidden();

        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get(route('admin.ai.edit'))
            ->assertOk()
            ->assertSee('Konfigurasi Model AI');
    }

    public function test_admin_simpan_pengaturan_dan_api_key_kosong_tidak_menghapus_key(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.ai.update'), [
            'api_key' => 'sk-rahasia-123',
            'model' => 'deepseek-reasoner',
            'endpoint' => 'https://api.deepseek.com/v1/chat/completions',
            'temperature' => 0.3,
            'max_tokens' => 4096,
        ])->assertRedirect(route('admin.ai.edit'));

        $settings = AiSetting::getSettings();
        $this->assertSame('sk-rahasia-123', $settings->api_key);
        $this->assertSame('deepseek-reasoner', $settings->model);
        $this->assertSame(4096, $settings->max_tokens);

        // Key mentah tidak tersimpan sebagai plaintext di kolom
        $this->assertNotSame('sk-rahasia-123', $settings->getRawOriginal('api_key'));

        // Submit tanpa api_key = key lama dipertahankan
        $this->actingAs($admin)->put(route('admin.ai.update'), [
            'model' => 'deepseek-chat',
            'temperature' => 0.9,
        ])->assertRedirect(route('admin.ai.edit'));

        $this->assertSame('sk-rahasia-123', AiSetting::getSettings()->api_key);
        $this->assertSame('deepseek-chat', AiSetting::getSettings()->model);

        // Halaman tidak membocorkan key
        $this->actingAs($admin)->get(route('admin.ai.edit'))->assertDontSee('sk-rahasia-123');
    }

    public function test_service_memakai_pengaturan_dari_database(): void
    {
        AiSetting::getSettings()->update([
            'api_key' => 'sk-db',
            'model' => 'deepseek-reasoner',
            'temperature' => 0.1,
            'max_tokens' => 1024,
        ]);

        Http::fake(['*' => Http::response(['choices' => [['message' => ['content' => '{}']]]], 200)]);

        app(DeepSeekService::class)->generateRPP([
            'mata_pelajaran' => 'IPA', 'fase' => 'D', 'topik' => 'Zat', 'kurikulum' => 'Kurikulum Merdeka',
        ]);

        Http::assertSent(function ($request) {
            return $request->data()['model'] === 'deepseek-reasoner'
                && $request->data()['temperature'] === 0.1
                && $request->data()['max_tokens'] === 1024
                && $request->hasHeader('Authorization', 'Bearer sk-db');
        });
    }

    public function test_admin_bisa_hapus_api_key_dari_database(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        AiSetting::getSettings()->update(['api_key' => 'sk-db']);

        $this->actingAs($admin)->delete(route('admin.ai.delete-key'))
            ->assertRedirect(route('admin.ai.edit'));

        $this->assertNull(AiSetting::getSettings()->api_key);
    }
}
