<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserPasswordTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_kolom_password_mengganti_kolom_tanggal_daftar(): void
    {
        $admin = $this->admin();
        User::factory()->create(['role' => 'guru']);

        $this->actingAs($admin)->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('<th>Password</th>', false)
            ->assertDontSee('Tanggal Daftar')
            ->assertSee('Belum direset');
    }

    public function test_admin_reset_password_satu_user(): void
    {
        $admin = $this->admin();
        $guru = User::factory()->create(['role' => 'guru', 'password' => Hash::make('lamasekali')]);

        $this->actingAs($admin)
            ->post(route('admin.users.reset-password', $guru))
            ->assertRedirect();

        $guru->refresh();

        $this->assertNotNull($guru->temp_password);
        $this->assertSame(10, strlen($guru->temp_password));
        $this->assertTrue(Hash::check($guru->temp_password, $guru->password), 'Password baru harus bisa dipakai login.');
        $this->assertFalse(Hash::check('lamasekali', $guru->password));
        $this->assertNotNull($guru->temp_password_at);

        // Tersimpan terenkripsi, bukan plaintext
        $this->assertNotSame($guru->temp_password, $guru->getRawOriginal('temp_password'));

        // Password baru terlihat di daftar
        $this->actingAs($admin)->get(route('admin.users.index'))->assertSee($guru->temp_password);
    }

    public function test_admin_reset_password_batch_dengan_password_seragam(): void
    {
        $admin = $this->admin();
        $guru = User::factory()->create(['role' => 'guru']);
        $guruLain = User::factory()->create(['role' => 'guru']);

        $this->actingAs($admin)->post(route('admin.users.reset-password-batch'), [
            'users' => [$guru->id, $guruLain->id, $admin->id],
            'password' => 'sekolah2026',
        ])->assertRedirect();

        foreach ([$guru, $guruLain] as $user) {
            $user->refresh();
            $this->assertSame('sekolah2026', $user->temp_password);
            $this->assertTrue(Hash::check('sekolah2026', $user->password));
        }

        // Akun admin sendiri dilewati
        $this->assertNull($admin->fresh()->temp_password);
    }

    public function test_batch_tanpa_password_menghasilkan_password_acak_berbeda(): void
    {
        $admin = $this->admin();
        $a = User::factory()->create(['role' => 'guru']);
        $b = User::factory()->create(['role' => 'guru']);

        $this->actingAs($admin)->post(route('admin.users.reset-password-batch'), [
            'users' => [$a->id, $b->id],
        ])->assertRedirect();

        $this->assertNotSame($a->fresh()->temp_password, $b->fresh()->temp_password);
    }

    public function test_admin_bisa_menyembunyikan_password_dari_daftar(): void
    {
        $admin = $this->admin();
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($admin)->post(route('admin.users.reset-password', $guru));
        $password = $guru->fresh()->temp_password;

        $this->actingAs($admin)->delete(route('admin.users.clear-temp-password', $guru))->assertRedirect();

        $guru->refresh();
        $this->assertNull($guru->temp_password);
        // Password login tetap berlaku meski tidak lagi ditampilkan
        $this->assertTrue(Hash::check($password, $guru->password));
    }

    public function test_password_sementara_hilang_saat_user_mengganti_password_sendiri(): void
    {
        $admin = $this->admin();
        $guru = User::factory()->create(['role' => 'guru']);

        $this->actingAs($admin)->post(route('admin.users.reset-password', $guru), ['password' => 'sementara123']);
        $this->assertSame('sementara123', $guru->fresh()->temp_password);

        $this->actingAs($guru->fresh())->put(route('password.update'), [
            'current_password' => 'sementara123',
            'password' => 'pilihansendiri99',
            'password_confirmation' => 'pilihansendiri99',
        ]);

        $this->assertNull($guru->fresh()->temp_password);
    }

    public function test_daftar_guru_menampilkan_kolom_password(): void
    {
        $admin = $this->admin();
        $akun = User::factory()->create(['role' => 'guru']);
        \App\Models\Guru::create(['user_id' => $akun->id, 'nik' => '3671104407920002', 'nama' => 'Guru Uji', 'nip' => '198501012010011001']);

        $this->actingAs($admin)->post(route('admin.users.reset-password', $akun), ['password' => 'guru2026x']);

        $this->actingAs($admin)->get(route('admin.guru.index'))
            ->assertOk()
            ->assertSee('<th>Password</th>', false)
            ->assertSee('guru2026x');
    }

    public function test_guru_tidak_boleh_reset_password_user_lain(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $korban = User::factory()->create(['role' => 'guru']);

        $this->actingAs($guru)->post(route('admin.users.reset-password', $korban))->assertForbidden();
        $this->actingAs($guru)->post(route('admin.users.reset-password-batch'), ['users' => [$korban->id]])->assertForbidden();

        $this->assertNull($korban->fresh()->temp_password);
    }

    public function test_admin_tidak_bisa_reset_akun_sendiri_lewat_daftar(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post(route('admin.users.reset-password', $admin))
            ->assertRedirect();

        $this->assertNull($admin->fresh()->temp_password);
    }
}
