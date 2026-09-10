<?php

namespace Tests\Feature;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminGuruCreationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_replace([
            'nama' => 'Siti Aminah', 'nik' => '0123456789012345',
            'email' => 'siti@example.com', 'password' => 'GuruAman2026!',
            'password_confirmation' => 'GuruAman2026!',
        ], $overrides);
    }

    public function test_index_replaces_sync_with_create_link(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin/guru')->assertOk()->assertSee('Tambah Guru')
            ->assertSee('/admin/guru/create', false)->assertDontSee('syncModal')
            ->assertDontSee('Sync dari API')->assertDontSee('/admin/guru/sync');
        $this->assertFalse(Route::has('admin.guru.sync'));
    }

    public function test_admin_can_open_create_form(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']))
            ->get('/admin/guru/create')->assertOk()->assertSee('Tambah Guru')
            ->assertSee('name="nik"', false)->assertSee('name="password_confirmation"', false);
    }

    public function test_admin_creates_linked_profile_and_login_without_changing_existing_records(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $existing = Guru::create(['nik' => '9999999999999999', 'nama' => 'Guru Lama']);
        $this->actingAs($admin)->post('/admin/guru', $this->payload([
            'nip' => '00123', 'jenis_kelamin' => 'P', 'tanggal_lahir' => '1990-01-02',
            'tempat_lahir' => 'Bogor', 'alamat' => 'Jalan Sekolah', 'no_hp' => '08123456789',
            'jabatan' => 'Guru Kelas', 'status' => 'GTY', 'role' => 'admin', 'user_id' => $admin->id,
        ]))->assertRedirect('/admin/guru')->assertSessionHas('success');
        $guru = Guru::where('nik', '0123456789012345')->firstOrFail();
        $this->assertSame('Siti Aminah', $guru->user->name);
        $this->assertSame('siti@example.com', $guru->user->email);
        $this->assertSame($guru->email, $guru->user->email);
        $this->assertSame('guru', $guru->user->role);
        $this->assertSame('00123', $guru->nip);
        $this->assertSame('P', $guru->jenis_kelamin);
        $this->assertTrue(Hash::check('GuruAman2026!', $guru->user->password));
        $this->assertNull($guru->user->temp_password);
        $this->assertSame('Guru Lama', $existing->fresh()->nama);
        $this->assertDatabaseCount('gurus', 2);
        $this->assertDatabaseCount('users', 2);
        auth()->logout();
        $this->post('/login', ['email' => 'siti@example.com', 'password' => 'GuruAman2026!'])->assertSessionHasNoErrors();
        $this->assertAuthenticatedAs($guru->user);
    }

    public function test_invalid_input_is_rejected_without_partial_records(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'admin']));
        foreach ([
            ['nama' => '', 'nik' => '', 'email' => '', 'password' => ''],
            ['nik' => 'abc', 'email' => 'invalid', 'jenis_kelamin' => 'X', 'tanggal_lahir' => 'invalid'],
            ['password' => 'short', 'password_confirmation' => 'short'],
            ['password_confirmation' => 'different'],
        ] as $invalid) {
            $fields = array_keys($invalid);
            if (isset($invalid['password_confirmation'])) {
                $fields = ['password'];
            }
            $this->from('/admin/guru/create')->post('/admin/guru', $this->payload($invalid))
                ->assertRedirect('/admin/guru/create')->assertSessionHasErrors($fields);
        }
        $this->assertDatabaseCount('gurus', 0);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_duplicate_user_email_and_profile_identifiers_are_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'existing@example.com']);
        Guru::create(['nik' => '9999999999999999', 'nama' => 'Guru Lama', 'email' => 'legacy@example.com']);
        $this->actingAs($admin);
        foreach (['existing@example.com', 'legacy@example.com'] as $email) {
            $this->post('/admin/guru', $this->payload(['email' => $email]))->assertSessionHasErrors('email');
        }
        $this->post('/admin/guru', $this->payload(['nik' => '9999999999999999']))->assertSessionHasErrors('nik');
        $this->assertDatabaseCount('gurus', 1);
        $this->assertDatabaseCount('users', 1);
    }

    public function test_guests_cannot_create_guru(): void
    {
        $this->get('/admin/guru/create')->assertRedirect('/login');
        $this->post('/admin/guru', $this->payload())->assertRedirect('/login');
        $this->assertDatabaseCount('gurus', 0);
        $this->assertDatabaseCount('users', 0);
    }

    public function test_guru_role_cannot_create_guru(): void
    {
        $this->actingAs(User::factory()->create(['role' => 'guru']));
        $this->get('/admin/guru/create')->assertForbidden();
        $this->post('/admin/guru', $this->payload())->assertForbidden();
        $this->assertDatabaseCount('gurus', 0);
        $this->assertDatabaseCount('users', 1);
    }
}
