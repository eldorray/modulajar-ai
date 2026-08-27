<?php

namespace Tests\Feature;

use App\Models\Rpp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PwaGuruTest extends TestCase
{
    use RefreshDatabase;

    private function rppFor(User $user, array $override = []): Rpp
    {
        return Rpp::create(array_merge([
            'user_id' => $user->id,
            'jenjang' => 'MI',
            'nama_guru' => $user->name,
            'fase' => 'C',
            'mata_pelajaran' => 'Matematika',
            'topik' => 'Bilangan bulat',
            'alokasi_waktu' => '2 x 35 menit',
            'kurikulum' => 'Kurikulum Merdeka',
            'status' => 'completed',
        ], $override));
    }

    public function test_halaman_pwa_bisa_dibuka_guru(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $rpp = $this->rppFor($guru);

        $this->actingAs($guru)->get(route('pwa.home'))->assertOk()->assertSee('Modul Ajar dibuat');
        $this->actingAs($guru)->get(route('pwa.rpp.index'))->assertOk()->assertSee('Matematika');
        $this->actingAs($guru)->get(route('pwa.rpp.create'))->assertOk()->assertSee('Generate Modul Ajar');
        $this->actingAs($guru)->get(route('pwa.rpp.show', $rpp))->assertOk()->assertSee('Bilangan bulat');
        $this->actingAs($guru)->get(route('pwa.akun'))->assertOk()->assertSee('Unit sekolah');
    }

    public function test_banner_pasang_aplikasi_tampil_di_halaman_pwa(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->get(route('pwa.home'))
            ->assertOk()
            ->assertSee('Pasang RPP Guru')
            ->assertSee('Tambahkan ke Layar Utama')
            ->assertSee('beforeinstallprompt', false);
    }

    public function test_guru_tidak_bisa_membuka_modul_guru_lain(): void
    {
        $pemilik = User::factory()->create(['role' => 'guru']);
        $lain = User::factory()->create(['role' => 'guru']);

        $this->actingAs($lain)
            ->get(route('pwa.rpp.show', $this->rppFor($pemilik)))
            ->assertForbidden();
    }

    public function test_manifest_dan_service_worker_tersedia(): void
    {
        $this->assertFileExists(public_path('manifest.webmanifest'));
        $this->assertFileExists(public_path('sw.js'));
        $this->assertFileExists(public_path('icons/icon-192.png'));
        $this->assertFileExists(public_path('icons/icon-512.png'));

        // Halaman offline tidak butuh login (dipakai service worker)
        $this->get(route('pwa.offline'))->assertOk()->assertSee('Tidak ada koneksi');
    }

    public function test_generate_dari_pwa_diarahkan_ke_tampilan_mobile(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        Http::fake(['*' => Http::response([
            'choices' => [['message' => ['content' => '{"informasi_umum":{"mata_pelajaran":"IPA"}}'], 'finish_reason' => 'stop']],
            'usage' => ['prompt_tokens' => 10, 'completion_tokens' => 10, 'total_tokens' => 20],
        ], 200)]);

        $response = $this->actingAs($guru)->postJson(route('rpp.store'), [
            'from' => 'pwa',
            'jenjang' => 'SMP',
            'nama_guru' => $guru->name,
            'mata_pelajaran' => 'IPA',
            'fase' => 'D',
            'topik' => 'Zat dan perubahannya',
            'alokasi_waktu' => '2 x 40 menit',
            'kurikulum' => 'Kurikulum Merdeka',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $rpp = Rpp::latest('id')->first();
        $this->assertSame(route('pwa.rpp.show', $rpp), $response->json('redirect_url'));
        $this->assertSame('SMP', $rpp->jenjang);
    }
}
