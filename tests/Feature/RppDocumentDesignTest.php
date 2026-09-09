<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Rpp;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RppDocumentDesignTest extends TestCase
{
    use RefreshDatabase;

    private function rppFor(User $user, array $attributes = []): Rpp
    {
        return Rpp::factory()->create(array_merge([
            'user_id' => $user->id,
            'status' => 'completed',
            // Harus non-kosong: print() menolak dokumen yang content_result-nya falsy.
            // Isinya sengaja minimal — yang diuji di sini sampul dan CSS, bukan isi modul.
            'content_result' => ['informasi_umum' => ['mata_pelajaran' => 'Matematika']],
            'mata_pelajaran' => 'Matematika',
            'nama_guru' => 'Bu Ratna',
            'tema' => 'merah',
            'desain' => 'klasik',
        ], $attributes));
    }

    /**
     * Tiap desain harus merender untuk kedua template, bukan hanya yang default.
     */
    public function test_every_design_renders_for_every_curriculum(): void
    {
        $user = User::factory()->create();
        SchoolSetting::getSettings()->update(['nama_sekolah' => 'MI Al-Hikmah']);

        foreach (array_keys(config('rpp_designs')) as $design) {
            foreach (['Kurikulum Merdeka', 'Kurikulum Merdeka Deep Learning'] as $curriculum) {
                $rpp = $this->rppFor($user, ['kurikulum' => $curriculum, 'desain' => $design]);

                $this->actingAs($user)
                    ->get(route('rpp.print', $rpp).'?desain='.$design)
                    ->assertOk()
                    ->assertSee('MI AL-HIKMAH')
                    ->assertSee('MATEMATIKA')
                    ->assertSee('Bu Ratna');
            }
        }
    }

    public function test_klasik_keeps_serif_font_and_corner_ornaments(): void
    {
        $user = User::factory()->create();
        $rpp = $this->rppFor($user, ['desain' => 'klasik', 'tema' => 'biru']);

        $this->actingAs($user)->get(route('rpp.print', $rpp))
            ->assertOk()
            ->assertSee("font-family: 'Times New Roman', Times, serif;", false)
            ->assertSee('decor-biru-tr.png', false);
    }

    public function test_designs_without_ornaments_emit_no_decor_image(): void
    {
        $user = User::factory()->create();

        foreach (['modern', 'minimalis'] as $design) {
            $rpp = $this->rppFor($user, ['desain' => $design]);

            $html = $this->actingAs($user)->get(route('rpp.print', $rpp))
                ->assertOk()
                ->assertSee("font-family: 'DejaVu Sans', Helvetica, Arial, sans-serif;", false)
                ->getContent();

            $this->assertStringNotContainsString('decor-', $html, "Skin {$design} seharusnya tanpa ornamen PNG.");
            $this->assertStringNotContainsString('class="fx-tr"', $html);
        }
    }

    public function test_table_header_switches_between_solid_and_outlined(): void
    {
        $user = User::factory()->create();

        $solid = $this->actingAs($user)->get(route('rpp.print', $this->rppFor($user, ['desain' => 'modern'])))->getContent();
        $outlined = $this->actingAs($user)->get(route('rpp.print', $this->rppFor($user, ['desain' => 'minimalis'])))->getContent();

        $this->assertStringContainsString('.tbl-langkah th { background-color: #b91c1c;', $solid);
        $this->assertStringContainsString('.tbl-langkah th { background-color: #ffffff;', $outlined);
        $this->assertStringContainsString('border-bottom: 2px solid #b91c1c;', $outlined);
    }

    public function test_unknown_design_in_query_falls_back_to_the_stored_one(): void
    {
        $user = User::factory()->create();
        $rpp = $this->rppFor($user, ['desain' => 'minimalis']);

        $html = $this->actingAs($user)
            ->get(route('rpp.print', $rpp).'?desain=../../etc/passwd&tema=tidak-ada')
            ->assertOk()
            ->getContent();

        // Jatuh ke desain dokumen (minimalis: sans-serif, tanpa ornamen)...
        $this->assertStringContainsString("font-family: 'DejaVu Sans'", $html);
        $this->assertStringNotContainsString('decor-', $html);
        // ...dan nilai dari URL tidak pernah muncul di dokumen.
        $this->assertStringNotContainsString('etc/passwd', $html);
        $this->assertStringNotContainsString('tidak-ada', $html);
    }

    public function test_document_without_a_design_renders_as_klasik(): void
    {
        $user = User::factory()->create();
        $rpp = $this->rppFor($user, ['desain' => null, 'tema' => 'merah']);

        $this->actingAs($user)->get(route('rpp.print', $rpp))
            ->assertOk()
            ->assertSee("font-family: 'Times New Roman', Times, serif;", false)
            ->assertSee('decor-merah-tr.png', false);
    }

    public function test_query_can_override_the_stored_design_without_touching_the_document(): void
    {
        $user = User::factory()->create();
        $rpp = $this->rppFor($user, ['desain' => 'klasik']);

        $this->actingAs($user)
            ->get(route('rpp.print', $rpp).'?desain=minimalis')
            ->assertOk()
            ->assertSee("font-family: 'DejaVu Sans'", false);

        $this->assertSame('klasik', $rpp->fresh()->desain, 'Unduhan tidak boleh mengubah dokumen.');
    }

    public function test_pdf_download_works_for_a_non_default_design(): void
    {
        $user = User::factory()->create();
        $rpp = $this->rppFor($user, ['desain' => 'klasik']);

        $response = $this->actingAs($user)->get(route('rpp.pdf', $rpp).'?desain=modern&tema=hijau');

        $response->assertOk();
        $this->assertSame('application/pdf', $response->headers->get('content-type'));
        $this->assertGreaterThan(1000, strlen($response->getContent()));
    }

    public function test_store_persists_a_valid_design_and_rejects_an_unknown_one(): void
    {
        $user = User::factory()->create();

        $payload = [
            'jenjang' => 'MI',
            'nama_guru' => 'Bu Ratna',
            'mata_pelajaran' => 'IPA',
            'fase' => 'D',
            'topik' => 'Ekosistem',
            'alokasi_waktu' => '2 JP',
            'kurikulum' => 'Kurikulum Merdeka',
            'tema' => 'biru',
        ];

        $this->actingAs($user)->post('/rpp', $payload + ['desain' => 'tidak-ada'])
            ->assertSessionHasErrors('desain');

        $this->assertDatabaseCount('rpps', 0);

        $this->actingAs($user)->post('/rpp', $payload + ['desain' => 'minimalis']);

        $this->assertSame('minimalis', Rpp::query()->latest('id')->first()?->desain);
    }

    public function test_detail_page_offers_every_design_and_colour_for_download(): void
    {
        $user = User::factory()->create();
        $rpp = $this->rppFor($user, ['desain' => 'minimalis', 'tema' => 'hijau']);

        $response = $this->actingAs($user)->get(route('rpp.show', $rpp))->assertOk();

        $response->assertSee('Unduh &amp; Cetak', false);

        foreach (config('rpp_designs') as $design) {
            $response->assertSee($design['label']);
        }

        // Dropdown harus terbuka pada nilai yang tersimpan di dokumen.
        $response->assertSee('minimalis', false)
            ->assertSee('hijau', false);
    }

    public function test_another_user_cannot_render_a_document_by_adding_query_params(): void
    {
        $owner = User::factory()->create();
        $intruder = User::factory()->create();
        $rpp = $this->rppFor($owner);

        $this->actingAs($intruder)
            ->get(route('rpp.print', $rpp).'?desain=modern&tema=hijau')
            ->assertForbidden();

        $this->actingAs($intruder)
            ->get(route('rpp.pdf', $rpp).'?desain=modern')
            ->assertForbidden();
    }
}
