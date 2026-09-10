<?php

namespace Tests\Feature;

use App\Models\Rpp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RppEditingTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_edits_content_and_saved_style_then_downloads_without_ai(): void
    {
        Http::fake();
        $user = User::factory()->create();
        foreach (['Kurikulum Merdeka', 'Kurikulum Berbasis Cinta', 'Kurikulum Merdeka Deep Learning'] as $curriculum) {
            $rpp = Rpp::factory()->create(['user_id' => $user->id, 'status' => 'completed', 'kurikulum' => $curriculum,
                'content_result' => ['kata_pengantar' => 'Isi lama', 'tambahan' => ['daftar' => ['Satu', 'Dua'], 'angka' => 3, 'aktif' => true]]]);
            $this->actingAs($user)->get('/rpp/'.$rpp->id.'/edit')->assertOk()->assertSee('Isi lama');
            $this->get(route('rpp.show', $rpp))->assertSee('/rpp/'.$rpp->id.'/edit', false);
            $this->get(route('pwa.rpp.show', $rpp))->assertSee('/rpp/'.$rpp->id.'/edit', false);
            $this->put('/rpp/'.$rpp->id, ['topik' => 'Topik revisi', 'tema' => 'biru', 'desain' => 'modern', 'content_fields' => [0 => 'Isi revisi & aman', 2 => 'Dua revisi'], 'from' => 'pwa'])
                ->assertRedirect(route('pwa.rpp.show', $rpp))->assertSessionHasNoErrors();
            $fresh = $rpp->fresh();
            $this->assertSame('Topik revisi', $fresh->topik);
            $this->assertSame('biru', $fresh->tema);
            $this->assertSame('modern', $fresh->desain);
            $this->assertSame(['kata_pengantar' => 'Isi revisi & aman', 'tambahan' => ['daftar' => ['Satu', 'Dua revisi'], 'angka' => 3, 'aktif' => true]], $fresh->content_result);
            $this->get(route('rpp.print', $rpp))->assertOk()->assertSee('Topik revisi')->assertSee("font-family: 'DejaVu Sans'", false);
            $pdf = $this->get(route('rpp.pdf', $rpp))->assertOk();
            $this->assertStringStartsWith('%PDF-', $pdf->getContent());
            $word = $this->get(route('rpp.word', $rpp))->assertOk();
            $file = $word->baseResponse->getFile()->getPathname();
            $zip = new \ZipArchive;
            $this->assertTrue($zip->open($file));
            $xml = $zip->getFromName('word/document.xml');
            $this->assertStringContainsString('Topik revisi', $xml);
            $this->assertStringContainsString(strtoupper(config('rpp_themes.biru.primary')), $xml);
            $zip->close();
            unlink($file);
        }
        Http::assertNothingSent();
    }

    public function test_pwa_form_roundtrip_and_stale_revision_protection(): void
    {
        $user = User::factory()->create();
        $rpp = Rpp::factory()->create(['user_id' => $user->id, 'status' => 'completed', 'content_result' => ['nested' => ['text' => '<script>alert(1)</script>', 'empty' => []]]]);
        $page = $this->actingAs($user)->get('/rpp/'.$rpp->id.'/edit?from=pwa')->assertOk();
        $page->assertSee('&lt;script&gt;', false)->assertSee('name="from" value="pwa"', false);
        preg_match('/name="revision" value="([a-f0-9]+)"/', $page->getContent(), $matches);
        $this->assertNotEmpty($matches[1]);
        $this->put('/rpp/'.$rpp->id, ['revision' => $matches[1], 'content_fields' => [0 => ''], 'tema' => 'hijau'])->assertSessionHasNoErrors();
        $this->assertSame(['nested' => ['text' => '', 'empty' => []]], $rpp->fresh()->content_result);
        $this->put('/rpp/'.$rpp->id, ['revision' => $matches[1], 'tema' => 'biru'])->assertSessionHasErrors('revision');
        $this->assertSame('hijau', $rpp->fresh()->tema);
    }

    public function test_authorization_status_and_validation_guard_updates(): void
    {
        $owner = User::factory()->create();
        $rpp = Rpp::factory()->create(['user_id' => $owner->id, 'status' => 'completed', 'content_result' => ['kata_pengantar' => 'Asli']]);
        $url = '/rpp/'.$rpp->id;
        $this->get($url.'/edit')->assertRedirect('/login');
        $this->actingAs(User::factory()->create())->get($url.'/edit')->assertForbidden();
        $this->put($url, ['tema' => 'biru'])->assertForbidden();
        $this->actingAs($owner)->put($url, ['tema' => 'invalid', 'desain' => '../bad', 'content_fields' => [0 => ['bad']]])->assertSessionHasErrors(['tema', 'desain', 'content_fields.0']);
        $this->put($url, ['content_fields' => [999 => 'bad']])->assertSessionHasErrors('content_fields');
        $this->assertSame(['kata_pengantar' => 'Asli'], $rpp->fresh()->content_result);
        $this->actingAs(User::factory()->create(['role' => 'admin']))->get($url.'/edit')->assertOk();
        $this->put($url, ['tema' => 'hijau', 'user_id' => 999, 'status' => 'failed'])->assertSessionHasNoErrors();
        $this->assertSame($owner->id, $rpp->fresh()->user_id);
        $this->assertSame('completed', $rpp->fresh()->status);
        foreach (['processing', 'failed', 'pending'] as $status) {
            $rpp->update(['status' => $status]);
            $this->get($url.'/edit')->assertStatus(409);
            $this->put($url, ['tema' => 'biru'])->assertStatus(409);
        }
    }
}
