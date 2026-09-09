<?php

namespace Tests\Feature\Api\V1;

use App\Jobs\GenerateRpp;
use App\Models\Rpp;
use App\Services\DeepSeekService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class GenerateRppJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_completes_processing_rpp_using_persisted_snapshot(): void
    {
        $rpp = Rpp::factory()->create([
            'status' => 'processing',
            'generation_input' => ['topik' => 'Ekosistem', 'panca_cinta' => true],
        ]);
        $service = Mockery::mock(DeepSeekService::class);
        $service->shouldReceive('generateRPP')->once()
            ->with(['topik' => 'Ekosistem', 'panca_cinta' => true], $rpp->user_id, $rpp->id)
            ->andReturn(['success' => true, 'content' => ['tujuan' => 'Memahami ekosistem']]);

        (new GenerateRpp($rpp->id))->handle($service);

        $rpp->refresh();
        $this->assertSame('completed', $rpp->status);
        $this->assertSame(['tujuan' => 'Memahami ekosistem'], $rpp->content_result);
        $this->assertNotNull($rpp->completed_at);
    }

    public function test_job_marks_terminal_service_failure_without_leaking_raw_error(): void
    {
        $rpp = Rpp::factory()->create(['status' => 'processing', 'generation_input' => ['topik' => 'Ekosistem']]);
        $service = Mockery::mock(DeepSeekService::class);
        $service->shouldReceive('generateRPP')->once()->andReturn([
            'success' => false,
            'error' => 'API key rahasia dan detail upstream',
        ]);

        (new GenerateRpp($rpp->id))->handle($service);

        $rpp->refresh();
        $this->assertSame('failed', $rpp->status);
        $this->assertSame('AI_GENERATION_FAILED', $rpp->failure_code);
        $this->assertSame('RPP gagal dibuat. Silakan coba lagi.', $rpp->failure_message);
    }

    public function test_duplicate_job_does_not_call_ai_for_non_processing_rpp(): void
    {
        $rpp = Rpp::factory()->create(['status' => 'completed', 'content_result' => ['sudah' => true]]);
        $service = Mockery::mock(DeepSeekService::class);
        $service->shouldNotReceive('generateRPP');

        (new GenerateRpp($rpp->id))->handle($service);

        $this->assertSame(['sudah' => true], $rpp->fresh()->content_result);
    }
}
