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

        $this->actingAs($guru)->get(route('pwa.home'))->assertOk()->assertSee('Modul ajar dibuat');
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
            ->assertSee('Pasang aplikasi RPP Guru')
            ->assertSee('Tambahkan ke Layar Utama')
            ->assertSee('beforeinstallprompt', false);
    }

    public function test_banner_install_menyediakan_fallback_chrome_android(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('androidChrome', false)
            ->assertSee('samsungbrowser', false)
            ->assertSee('!this.snoozed()', false)
            ->assertSee('Pasang aplikasi melalui menu Chrome', false)
            ->assertSee('Tambahkan ke layar utama', false);
    }

    public function test_menu_detail_membuka_halaman_pwa_dan_akun_di_pojok(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $rpp = $this->rppFor($guru);

        $response = $this->actingAs($guru)->get(route('pwa.home'))->assertOk();

        // Menu Detail mengarah ke halaman isi modul khusus PWA, bukan versi desktop
        $response->assertSee(route('pwa.rpp.detail', $rpp), false);

        // Urutan navigasi: Home, Modul, tombol generate, Detail, Akun (pojok)
        $response->assertSeeInOrder(['>Home<', '>Modul<', 'pwa-fab', '>Detail<', '>Akun<'], false);

        // Menu Desktop tidak lagi ada di navigasi bawah
        $response->assertDontSee('>Desktop<', false);
    }

    public function test_halaman_isi_modul_pwa_menampilkan_seluruh_bagian(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $rpp = $this->rppFor($guru, ['content_result' => [
            'informasi_umum' => ['mata_pelajaran' => 'Matematika', 'jenjang' => 'MI'],
            'komponen_inti' => [
                'tujuan_pembelajaran' => ['Menjelaskan bilangan bulat', 'Menghitung operasi campuran'],
            ],
            'asesmen' => [
                'formatif' => [['nomor' => 1, 'teknik' => 'Observasi diskusi kelompok']],
            ],
            'lampiran' => ['lkpd' => ['judul' => 'LKPD Bilangan Bulat']],
        ]]);

        $this->actingAs($guru)->get(route('pwa.rpp.detail', $rpp))
            ->assertOk()
            ->assertSee('Isi modul lengkap')
            // Judul bagian dan isinya ikut ter-render
            ->assertSeeInOrder(['Informasi umum', 'Komponen inti', 'Asesmen', 'Lampiran'])
            ->assertSee('Menjelaskan bilangan bulat')
            ->assertSee('Observasi diskusi kelompok')
            ->assertSee('LKPD Bilangan Bulat')
            // Label kunci JSON diubah jadi teks yang enak dibaca
            ->assertSee('Tujuan Pembelajaran')
            ->assertDontSee('tujuan_pembelajaran')
            // Halaman ini memakai layout PWA, bukan layout desktop
            ->assertSee('pwa-nav', false)
            ->assertDontSee('sidebarCollapsed', false);
    }

    public function test_guru_tidak_bisa_membuka_isi_modul_guru_lain(): void
    {
        $pemilik = User::factory()->create(['role' => 'guru']);
        $lain = User::factory()->create(['role' => 'guru']);

        $this->actingAs($lain)
            ->get(route('pwa.rpp.detail', $this->rppFor($pemilik)))
            ->assertForbidden();
    }

    public function test_halaman_isi_modul_tidak_memantulkan_sesi_standalone(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $rpp = $this->rppFor($guru);

        // Halaman modul lengkap boleh dibuka dari PWA terpasang
        $this->actingAs($guru)->get(route('rpp.show', $rpp))
            ->assertOk()
            ->assertDontSee('window.location.replace', false)
            ->assertSee('Kembali ke aplikasi');

        // Halaman desktop lain tetap dipantulkan ke aplikasi
        $this->actingAs($guru)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('window.location.replace', false);
    }

    public function test_banner_install_muncul_di_halaman_welcome(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('manifest.webmanifest', false)
            ->assertSee('Pasang aplikasi RPP Guru')
            ->assertSee('beforeinstallprompt', false);
    }

    public function test_akses_desktop_ditutup_saat_aplikasi_terpasang(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);

        // Tautan ke tampilan desktop ditandai untuk disembunyikan saat standalone
        $this->actingAs($guru)->get(route('pwa.home'))
            ->assertSee('data-hide-standalone', false)
            ->assertSee('display-mode: standalone', false);

        $this->actingAs($guru)->get(route('pwa.akun'))
            ->assertSee('data-hide-standalone', false);

        // Halaman desktop mengembalikan sesi standalone ke aplikasi
        $this->actingAs($guru)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('display-mode: standalone', false)
            ->assertSee(route('pwa.home'), false);
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

    public function test_login_tetap_berada_di_dalam_jendela_pwa_standalone(): void
    {
        $manifest = json_decode(file_get_contents(public_path('manifest.webmanifest')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('/app', $manifest['start_url']);
        $this->assertSame('/', $manifest['scope']);
        $this->assertSame('standalone', $manifest['display']);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('manifest.webmanifest', false)
            ->assertSee('mobile-web-app-capable', false)
            ->assertSee('apple-mobile-web-app-capable', false);
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
