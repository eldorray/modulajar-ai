<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RppCoverPreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_renders_the_live_cover_preview(): void
    {
        $user = User::factory()->create(['name' => 'Bu Ratna']);
        SchoolSetting::getSettings()->update(['nama_sekolah' => 'MI Al-Hikmah']);

        $response = $this->actingAs($user)->get('/rpp/create');

        $response->assertOk()
            ->assertSee('Pratinjau sampul')
            ->assertSee('rppCoverPreview(', false)
            ->assertSee('MI AL-HIKMAH')
            ->assertSee('Bu Ratna');
    }

    public function test_preview_seeds_every_theme_and_the_selected_one(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/rpp/create');

        foreach (array_keys(config('rpp_themes')) as $key) {
            $response->assertSee($key, false);
        }

        // @js() escapes quotes as \u0022 inside the JSON.parse() payload.
        $response->assertSee('decorBase', false)
            ->assertSee('tema\u0022:\u0022merah', false);
    }

    /**
     * Penjaga regresi. x-bind:style bentuk string membuat Alpine menimpa SELURUH
     * atribut style elemen, sehingga transform: scale() pada pembungkus pratinjau
     * ikut terhapus dan sampul dirender seukuran aslinya (794px) di kotak kecil.
     * Semua binding style di pratinjau wajib bentuk objek.
     */
    public function test_preview_never_uses_string_form_style_bindings(): void
    {
        $user = User::factory()->create();

        $html = $this->actingAs($user)->get('/rpp/create')->assertOk()->getContent();

        $this->assertStringNotContainsString('x-bind:style="\'', $html);
        $this->assertStringContainsString('transform: scale(', $html);
    }

    /**
     * Seluruh halaman A4 harus muat di kotak pratinjau, bukan terpotong.
     */
    public function test_preview_scale_fits_a_whole_a4_page_in_the_box(): void
    {
        $user = User::factory()->create();

        $html = $this->actingAs($user)->get('/rpp/create')->assertOk()->getContent();

        preg_match('/width: (\d+)px; height: (\d+)px;">/', $html, $box);
        preg_match('/transform: scale\(([0-9.]+)\)/', $html, $scale);

        $this->assertNotEmpty($box, 'Kotak pratinjau tidak ditemukan.');
        $this->assertNotEmpty($scale, 'Skala pratinjau tidak ditemukan.');

        [$boxWidth, $boxHeight] = [(int) $box[1], (int) $box[2]];
        $factor = (float) $scale[1];

        // A4 pada 96dpi
        $this->assertLessThanOrEqual($boxWidth, 794 * $factor, 'Lebar A4 melebihi kotak: sampul terpotong.');
        $this->assertLessThanOrEqual($boxHeight, 1123 * $factor, 'Tinggi A4 melebihi kotak: sampul terpotong.');
        // ...dan tidak terlalu kecil sampai menyisakan ruang kosong besar.
        $this->assertGreaterThan($boxWidth * 0.95, 794 * $factor);
    }

    public function test_preview_keeps_old_input_after_a_validation_error(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->from('/rpp/create')
            ->post('/rpp', ['tema' => 'hijau', 'mata_pelajaran' => 'Matematika'])
            ->assertRedirect('/rpp/create');

        $this->followRedirects($response)
            ->assertSee('tema\u0022:\u0022hijau', false)
            ->assertSee('Matematika', false);
    }
}
