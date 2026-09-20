<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PwaNewPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_profil_pwa_bisa_dibuka(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $this->actingAs($guru)->get(route('pwa.profil'))->assertOk()->assertSee('Ubah Profil Akun');
    }

    public function test_halaman_kop_pwa_bisa_dibuka_dan_tab_jenjang_aktif(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $this->actingAs($guru)->get(route('pwa.kop'))->assertOk()->assertSee('Logo Kiri');
        $this->actingAs($guru)->get(route('pwa.kop', ['unit' => 'SMP/MTs']))->assertOk()->assertSee('SMP/MTs');
    }

    public function test_form_kop_pwa_submit_dan_redirect_balik_ke_pwa(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $response = $this->actingAs($guru)->post(route('settings.update'), [
            'jenjang' => 'SMP/MTs',
            'nama_sekolah' => 'SMP Negeri 1 Uji',
            'form_context' => 'pwa',
        ]);
        $response->assertRedirect(route('pwa.kop', ['unit' => 'SMP/MTs']));
    }
}
