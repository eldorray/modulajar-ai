<?php

namespace Tests\Feature;

use App\Models\Rpp;
use App\Models\User;
use App\Services\DeepSeekService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RppSynchronousGenerationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $extra = []): array
    {
        return array_merge([
            'jenjang' => 'MI/SD', 'nama_guru' => 'Bu Ratna', 'mata_pelajaran' => 'IPA',
            'fase' => 'C', 'topik' => 'Ekosistem', 'alokasi_waktu' => '2 JP',
            'kurikulum' => 'Kurikulum Merdeka', 'tema' => 'biru', 'desain' => 'minimalis',
        ], $extra);
    }

    public function test_web_and_pwa_complete_inline_and_download_saved_style(): void
    {
        Queue::fake();
        Http::fake(['*' => Http::response(['choices' => [['message' => ['content' => json_encode([
            'informasi_umum' => ['mata_pelajaran' => 'IPA'],
        ])]]]])]);
        $this->actingAs(User::factory()->create());

        foreach ([[], ['from' => 'pwa']] as $extra) {
            $response = $this->postJson(route('rpp.store'), $this->payload($extra))
                ->assertOk()->assertJson(['success' => true, 'status' => 'completed']);
            $rpp = Rpp::latest('id')->firstOrFail();
            $this->assertSame(route(isset($extra['from']) ? 'pwa.rpp.show' : 'rpp.show', $rpp), $response->json('redirect_url'));
            $this->assertSame('completed', $rpp->status);
            $this->assertNotEmpty($rpp->content_result);
            $this->assertNotNull($rpp->started_at);
            $this->assertNotNull($rpp->completed_at);
            $this->assertNull($rpp->failed_at);
            $this->assertSame('biru', $rpp->tema);
            $this->assertSame('minimalis', $rpp->desain);
            $this->get(route('rpp.print', $rpp))->assertOk()
                ->assertViewHas('themeKey', 'biru')->assertViewHas('designKey', 'minimalis');
            $pdf = $this->get(route('rpp.pdf', $rpp))->assertOk()->assertHeader('content-type', 'application/pdf');
            $this->assertStringStartsWith('%PDF-', $pdf->getContent());
        }
        Queue::assertNothingPushed();
        Http::assertSentCount(2);
    }

    public function test_provider_failure_is_not_reported_as_success(): void
    {
        Queue::fake();
        Http::fake(['*' => Http::response([], 503)]);
        $this->actingAs(User::factory()->create());
        foreach ([[], ['from' => 'pwa']] as $extra) {
            $this->postJson(route('rpp.store'), $this->payload($extra))
                ->assertStatus(500)->assertJson(['success' => false, 'status' => 'failed']);
            $rpp = Rpp::latest('id')->firstOrFail();
            $this->assertSame('failed', $rpp->status);
            $this->assertNotNull($rpp->failed_at);
            $this->assertNull($rpp->completed_at);
            $this->assertNull($rpp->content_result);
            $this->assertNotEmpty($rpp->failure_message);
            $this->get(route('rpp.pdf', $rpp))->assertRedirect(route('rpp.show', $rpp));
        }
        Queue::assertNothingPushed();
    }

    public function test_unexpected_exception_marks_document_failed(): void
    {
        Queue::fake();
        $this->mock(DeepSeekService::class)->shouldReceive('generateRPP')->once()->andThrow(new \RuntimeException('Internal failure'));
        $this->actingAs(User::factory()->create())->postJson(route('rpp.store'), $this->payload())
            ->assertStatus(500)->assertJson(['success' => false, 'status' => 'failed']);
        $this->assertNotNull(Rpp::firstOrFail()->failed_at);
        Queue::assertNothingPushed();
    }

    public function test_connection_timeout_marks_generation_failed(): void
    {
        Queue::fake();
        Http::fake(fn () => throw new \Illuminate\Http\Client\ConnectionException('Connection timed out'));
        $this->actingAs(User::factory()->create())->postJson(route('rpp.store'), $this->payload())
            ->assertStatus(500)->assertJson(['success' => false, 'status' => 'failed']);
        $this->assertNotNull(Rpp::firstOrFail()->failed_at);
        Queue::assertNothingPushed();
    }

    public function test_forms_only_display_success_for_a_completed_response(): void
    {
        $this->actingAs(User::factory()->create());
        foreach (['rpp.create', 'pwa.rpp.create'] as $route) {
            $this->get(route($route))->assertOk()
                ->assertSee("response.ok && data.success && data.status === 'completed'", false);
        }
    }

    public function test_empty_content_cannot_complete_generation(): void
    {
        Queue::fake();
        $this->mock(DeepSeekService::class)->shouldReceive('generateRPP')->once()->andReturn(['success' => true, 'content' => []]);
        $this->actingAs(User::factory()->create())->postJson(route('rpp.store'), $this->payload())
            ->assertStatus(500)->assertJson(['success' => false]);
        $this->assertSame('failed', Rpp::firstOrFail()->status);
    }

    public function test_normal_form_redirects_only_after_completion_and_preserves_input_on_failure(): void
    {
        Queue::fake();
        Http::fake(['*' => Http::sequence()
            ->push(['choices' => [['message' => ['content' => '{"informasi_umum":{"mata_pelajaran":"IPA"}}']]]])
            ->push([], 503)]);
        $this->actingAs(User::factory()->create());
        $response = $this->post(route('rpp.store'), $this->payload());
        $rpp = Rpp::firstOrFail();
        $response->assertRedirect(route('rpp.show', $rpp))->assertSessionHas('success');
        $this->assertSame('completed', $rpp->status);
        $this->from(route('pwa.rpp.create'))->post(route('rpp.store'), $this->payload(['from' => 'pwa']))
            ->assertRedirect(route('pwa.rpp.create'))->assertSessionHas('error')
            ->assertSessionHasInput('tema', 'biru')->assertSessionHasInput('desain', 'minimalis');
    }
}
