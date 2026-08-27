<?php

namespace Tests\Feature;

use App\Models\Rpp;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchoolSettingUnitTest extends TestCase
{
    use RefreshDatabase;

    public function test_mi_dan_smp_punya_profil_terpisah(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('settings.update'), ['jenjang' => 'MI', 'nama_sekolah' => 'MIS Daarul Hikmah'])
            ->assertRedirect(route('settings.index', ['unit' => 'MI']));

        $this->actingAs($user)
            ->post(route('settings.update'), ['jenjang' => 'SMP', 'nama_sekolah' => 'SMP Daarul Hikmah'])
            ->assertRedirect(route('settings.index', ['unit' => 'SMP']));

        $this->assertSame('MIS Daarul Hikmah', SchoolSetting::getSettings('MI')->nama_sekolah);
        $this->assertSame('SMP Daarul Hikmah', SchoolSetting::getSettings('SMP')->nama_sekolah);

        // RPP lama tanpa jenjang tetap memakai profil MI
        $rpp = Rpp::create(['user_id' => $user->id, 'nama_guru' => 'Guru', 'fase' => 'D', 'mata_pelajaran' => 'IPA', 'topik' => 'Zat', 'alokasi_waktu' => '2 x 35 menit', 'kurikulum' => 'Kurikulum Merdeka']);
        $this->assertSame('MIS Daarul Hikmah', SchoolSetting::getSettings($rpp->jenjang)->nama_sekolah);

        $rpp->update(['jenjang' => 'SMP']);
        $this->assertSame('SMP Daarul Hikmah', SchoolSetting::getSettings($rpp->fresh()->jenjang)->nama_sekolah);

        $this->actingAs($user)->get(route('settings.index', ['unit' => 'SMP']))
            ->assertOk()->assertSee('SMP Daarul Hikmah');
    }
}
