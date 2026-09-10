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

    public function test_tiga_unit_sekolah_punya_profil_terpisah(): void
    {
        $user = User::factory()->create();

        $units = [
            'MI/SD' => 'MI/SD Daarul Hikmah',
            'SMP/MTs' => 'SMP/MTs Daarul Hikmah',
            'SMA/SMK' => 'SMA/SMK Daarul Hikmah',
        ];

        foreach ($units as $unit => $namaSekolah) {
            $this->actingAs($user)
                ->post(route('settings.update'), [
                    'jenjang' => $unit,
                    'nama_sekolah' => $namaSekolah,
                ])
                ->assertRedirect(route('settings.index', ['unit' => $unit]));

            $this->assertSame($namaSekolah, SchoolSetting::getSettings($unit)->nama_sekolah);
        }

        // RPP lama tanpa jenjang tetap memakai profil unit awal MI/SD.
        $rpp = Rpp::create([
            'user_id' => $user->id,
            'nama_guru' => 'Guru',
            'fase' => 'D',
            'mata_pelajaran' => 'IPA',
            'topik' => 'Zat',
            'alokasi_waktu' => '2 x 35 menit',
            'kurikulum' => 'Kurikulum Merdeka',
        ]);

        $this->assertSame('MI/SD Daarul Hikmah', SchoolSetting::getSettings($rpp->jenjang)->nama_sekolah);

        $rpp->update(['jenjang' => 'SMP/MTs']);
        $this->assertSame('SMP/MTs Daarul Hikmah', SchoolSetting::getSettings($rpp->fresh()->jenjang)->nama_sekolah);

        $this->actingAs($user)
            ->get(route('settings.index', ['unit' => 'SMA/SMK']))
            ->assertOk()
            ->assertSee('SMA/SMK Daarul Hikmah');
    }

    public function test_nama_unit_lama_dinormalisasi_ke_unit_baru(): void
    {
        $this->assertSame('MI/SD', SchoolSetting::normalizeJenjang('MI'));
        $this->assertSame('MI/SD', SchoolSetting::normalizeJenjang('SD'));
        $this->assertSame('SMP/MTs', SchoolSetting::normalizeJenjang('SMP'));
        $this->assertSame('SMP/MTs', SchoolSetting::normalizeJenjang('MTs'));
        $this->assertSame('SMA/SMK', SchoolSetting::normalizeJenjang('SMA'));
        $this->assertSame('SMA/SMK', SchoolSetting::normalizeJenjang('SMK'));
    }
}
