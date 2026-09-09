<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AiSetting;
use App\Services\DeepSeekService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Regresi untuk kegagalan produksi: model menjawab HTTP 200 tetapi jawabannya
 * terpotong karena kuota max_tokens habis, sehingga JSON tidak valid.
 *
 * Sebelum perbaikan, jalur ini menghasilkan success=true dengan content null,
 * dan controller mengubahnya menjadi "Gagal membuat RPP. Silakan coba lagi." —
 * pesan default yang tidak menyebut sebab apa pun. Admin tidak punya petunjuk
 * bahwa yang perlu dinaikkan adalah max_tokens.
 */
class DeepSeekTruncationTest extends TestCase
{
    use RefreshDatabase;

    private function setUpAi(int $maxTokens = 8192): void
    {
        AiSetting::getSettings()->update([
            'api_key' => 'sk-uji',
            'endpoint' => 'https://api.example.com/v1/chat/completions',
            'model' => 'model-uji',
            'max_tokens' => $maxTokens,
        ]);
    }

    private function payload(): array
    {
        return [
            'mata_pelajaran' => 'Matematika',
            'fase' => 'D',
            'kelas' => '7',
            'topik' => 'Operasi hitung bilangan bulat',
            'alokasi_waktu' => '2 JP',
            'kurikulum' => 'Kurikulum Merdeka',
        ];
    }

    public function test_truncated_answer_reports_the_token_limit_not_a_generic_error(): void
    {
        $this->setUpAi(8192);

        // Persis bentuk kegagalan produksi: JSON berhenti di tengah string.
        Http::fake(['*' => Http::response([
            'choices' => [[
                'finish_reason' => 'length',
                'message' => ['role' => 'assistant', 'content' => '{"identifikasi":{"mata_pelajaran":"Matem'],
            ]],
            'usage' => ['prompt_tokens' => 900, 'completion_tokens' => 8192, 'total_tokens' => 9092],
        ])]);

        $result = (new DeepSeekService)->generateRPP($this->payload());

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result, 'Kegagalan WAJIB membawa pesan, kalau tidak controller memakai pesan generik.');
        $this->assertStringContainsString('max_tokens', $result['error']);
        $this->assertStringContainsString('8192', $result['error'], 'Pesan harus menyebut batas yang berlaku sekarang.');
    }

    public function test_empty_content_from_exhausted_reasoning_budget_is_reported(): void
    {
        $this->setUpAi(2048);

        // Model reasoning: kuota habis di reasoning_content, content tak pernah ditulis.
        Http::fake(['*' => Http::response([
            'choices' => [[
                'finish_reason' => 'length',
                'message' => [
                    'role' => 'assistant',
                    'content' => '',
                    'reasoning_content' => str_repeat('berpikir panjang. ', 200),
                ],
            ]],
            'usage' => ['completion_tokens' => 2048],
        ])]);

        $result = (new DeepSeekService)->generateRPP($this->payload());

        $this->assertFalse($result['success']);
        $this->assertStringContainsString('max_tokens', $result['error']);
        $this->assertStringContainsString('2048', $result['error']);
    }

    public function test_a_complete_answer_still_succeeds(): void
    {
        $this->setUpAi(8192);

        Http::fake(['*' => Http::response([
            'choices' => [[
                'finish_reason' => 'stop',
                'message' => ['role' => 'assistant', 'content' => '{"identifikasi":{"mata_pelajaran":"Matematika"}}'],
            ]],
            'usage' => ['prompt_tokens' => 900, 'completion_tokens' => 1200, 'total_tokens' => 2100],
        ])]);

        $result = (new DeepSeekService)->generateRPP($this->payload());

        $this->assertTrue($result['success']);
        $this->assertSame(['identifikasi' => ['mata_pelajaran' => 'Matematika']], $result['content']);
    }

    /**
     * Default lama 8192 adalah nilai yang gagal di produksi. Test ini menjaga
     * agar default tidak turun kembali ke bawah batas itu.
     */
    public function test_default_token_budget_exceeds_the_value_that_failed_in_production(): void
    {
        AiSetting::getSettings()->update(['max_tokens' => null]);

        $this->assertGreaterThan(8192, AiSetting::resolved()['max_tokens']);
    }

    /**
     * Nilai yang disimpan admin tetap menang atas default.
     */
    public function test_an_explicit_token_budget_is_respected(): void
    {
        AiSetting::getSettings()->update(['max_tokens' => 32768]);

        $this->assertSame(32768, AiSetting::resolved()['max_tokens']);
    }

    /**
     * Jawaban utuh tapi JSON-nya memang rusak bukan soal kuota — pesannya harus
     * berbeda, supaya admin tidak menaikkan max_tokens untuk masalah yang lain.
     */
    public function test_malformed_json_without_truncation_reports_a_different_cause(): void
    {
        $this->setUpAi(8192);

        Http::fake(['*' => Http::response([
            'choices' => [[
                'finish_reason' => 'stop',
                'message' => ['role' => 'assistant', 'content' => 'Berikut RPP-nya: bukan JSON'],
            ]],
            'usage' => ['completion_tokens' => 20],
        ])]);

        $result = (new DeepSeekService)->generateRPP($this->payload());

        $this->assertFalse($result['success']);
        $this->assertArrayHasKey('error', $result);
        $this->assertStringNotContainsString('max_tokens', $result['error']);
    }
}
