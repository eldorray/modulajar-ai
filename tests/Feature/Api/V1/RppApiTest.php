<?php

namespace Tests\Feature\Api\V1;

use App\Jobs\GenerateRpp;
use App\Models\Rpp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class RppApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['role' => 'guru']);
        $this->token = $this->user->createToken('android')->plainTextToken;
    }

    public function test_list_is_paginated_and_scoped_to_owner(): void
    {
        Rpp::factory()->count(11)->create(['user_id' => $this->user->id]);
        Rpp::factory()->create();

        $this->withToken($this->token)->getJson('/api/v1/rpps')
            ->assertOk()->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.total', 11)
            ->assertJsonMissing(['user_id' => Rpp::query()->where('user_id', '!=', $this->user->id)->value('user_id')]);
    }

    public function test_create_returns_202_persists_snapshot_and_dispatches_job(): void
    {
        Queue::fake();

        $response = $this->withToken($this->token)
            ->withHeader('Idempotency-Key', 'android-rpp-key-0001')
            ->postJson('/api/v1/rpps', $this->payload(['panca_cinta' => true]));

        $response->assertAccepted()
            ->assertJsonPath('data.status', 'processing')
            ->assertJsonPath('data.mata_pelajaran', 'IPA');

        $rpp = Rpp::query()->sole();
        $this->assertTrue($rpp->generation_input['panca_cinta']);
        $this->assertSame(['Formatif', 'Sumatif'], $rpp->generation_input['jenis_asesmen_array']);
        Queue::assertPushed(GenerateRpp::class, fn (GenerateRpp $job) => $job->rppId === $rpp->id);
    }

    public function test_same_idempotency_key_and_payload_returns_same_resource_without_second_job(): void
    {
        Queue::fake();
        $headers = ['Idempotency-Key' => 'android-rpp-key-0002'];

        $first = $this->withToken($this->token)->withHeaders($headers)->postJson('/api/v1/rpps', $this->payload());
        $second = $this->withToken($this->token)->withHeaders($headers)->postJson('/api/v1/rpps', $this->payload());

        $first->assertAccepted();
        $second->assertOk()->assertJsonPath('data.id', $first->json('data.id'));
        $this->assertDatabaseCount('rpps', 1);
        Queue::assertPushed(GenerateRpp::class, 1);
    }

    public function test_same_idempotency_key_with_different_payload_returns_conflict(): void
    {
        Queue::fake();
        $headers = ['Idempotency-Key' => 'android-rpp-key-0003'];
        $this->withToken($this->token)->withHeaders($headers)->postJson('/api/v1/rpps', $this->payload())->assertAccepted();

        $this->withToken($this->token)->withHeaders($headers)
            ->postJson('/api/v1/rpps', $this->payload(['topik' => 'Topik berbeda']))
            ->assertConflict()->assertJsonPath('code', 'IDEMPOTENCY_CONFLICT');
    }

    public function test_detail_status_delete_and_download_are_owner_only(): void
    {
        $other = Rpp::factory()->create(['status' => 'completed', 'content_result' => ['foo' => 'bar']]);

        foreach (["/api/v1/rpps/{$other->id}", "/api/v1/rpps/{$other->id}/status", "/api/v1/rpps/{$other->id}/pdf", "/api/v1/rpps/{$other->id}/docx"] as $uri) {
            $this->withToken($this->token)->getJson($uri)->assertNotFound();
        }
        $this->withToken($this->token)->deleteJson("/api/v1/rpps/{$other->id}")->assertNotFound();
    }

    public function test_processing_document_cannot_be_downloaded(): void
    {
        $rpp = Rpp::factory()->create(['user_id' => $this->user->id, 'status' => 'processing']);

        $this->withToken($this->token)->getJson("/api/v1/rpps/{$rpp->id}/pdf")
            ->assertConflict()->assertJsonPath('code', 'RESOURCE_NOT_READY');
    }

    public function test_completed_document_can_be_rendered_as_pdf_and_docx(): void
    {
        $rpp = Rpp::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'completed',
            'content_result' => [
                'tujuan_pembelajaran' => ['Memahami ekosistem'],
                'pertanyaan_pemantik' => ['Apa hubungan makhluk hidup?'],
            ],
        ]);

        $this->withToken($this->token)->get("/api/v1/rpps/{$rpp->id}/pdf")
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertDownload("ModulAjar_IPA_{$rpp->id}.pdf");

        $this->withToken($this->token)->get("/api/v1/rpps/{$rpp->id}/docx")
            ->assertOk()
            ->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            ->assertDownload("RPPM_IPA_{$rpp->id}.docx");
    }

    private function payload(array $overrides = []): array
    {
        return [...[
            'nama_guru' => 'Guru IPA', 'mata_pelajaran' => 'IPA', 'fase' => 'D',
            'topik' => 'Ekosistem', 'alokasi_waktu' => '2 JP',
            'kurikulum' => 'Kurikulum Merdeka',
        ], ...$overrides];
    }
}
