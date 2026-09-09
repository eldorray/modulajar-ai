<?php

namespace Tests\Feature\Api\V1;

use App\Models\Guru;
use App\Models\Rpp;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileSettingsAdminApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_can_be_read_updated_and_password_changed(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password')]);
        $token = $user->createToken('android')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/profile')->assertOk()->assertJsonPath('data.id', $user->id);
        $this->withToken($token)->patchJson('/api/v1/profile', ['name' => 'Nama Baru', 'email' => 'baru@example.com'])
            ->assertOk()->assertJsonPath('data.name', 'Nama Baru');
        $this->assertNull($user->fresh()->email_verified_at);
        $this->withToken($token)->putJson('/api/v1/profile/password', [
            'current_password' => 'password', 'password' => 'Password-Baru-123!', 'password_confirmation' => 'Password-Baru-123!',
        ])->assertNoContent();
        $this->assertTrue(Hash::check('Password-Baru-123!', $user->fresh()->password));
    }

    public function test_guru_can_read_but_cannot_modify_settings(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $token = $guru->createToken('android')->plainTextToken;
        SchoolSetting::query()->create(['nama_sekolah' => 'Madrasah']);

        $this->withToken($token)->getJson('/api/v1/settings')->assertOk()->assertJsonPath('data.nama_sekolah', 'Madrasah');
        $this->withToken($token)->patchJson('/api/v1/settings', ['nama_sekolah' => 'Diretas'])->assertForbidden();
    }

    public function test_admin_can_update_settings_and_manage_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('android')->plainTextToken;

        $this->withToken($token)->patchJson('/api/v1/settings', ['nama_sekolah' => 'Madrasah API'])->assertOk();
        $created = $this->withToken($token)->postJson('/api/v1/admin/users', [
            'name' => 'Guru Baru', 'email' => 'guru-baru@example.com', 'password' => 'Password-123!',
            'password_confirmation' => 'Password-123!', 'role' => 'guru',
        ])->assertCreated()->json('data.id');
        $this->withToken($token)->getJson('/api/v1/admin/users')->assertOk()->assertJsonPath('meta.total', 2);
        $this->withToken($token)->patchJson("/api/v1/admin/users/{$created}", [
            'name' => 'Guru Diperbarui', 'email' => 'guru-baru@example.com', 'role' => 'guru',
        ])->assertOk()->assertJsonPath('data.name', 'Guru Diperbarui');
    }

    public function test_non_admin_is_denied_admin_endpoints(): void
    {
        $guru = User::factory()->create(['role' => 'guru']);
        $token = $guru->createToken('android')->plainTextToken;
        foreach (['/api/v1/admin/users', '/api/v1/admin/guru', '/api/v1/admin/rpps'] as $uri) {
            $this->withToken($token)->getJson($uri)->assertForbidden()->assertJsonPath('code', 'FORBIDDEN');
        }
    }

    public function test_admin_guru_and_rpp_lists_include_real_data_without_sensitive_generation_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guruUser = User::factory()->create(['role' => 'guru']);
        Guru::query()->create(['user_id' => $guruUser->id, 'nama' => 'Guru IPA', 'nik' => '123']);
        Rpp::factory()->create(['user_id' => $guruUser->id, 'generation_input' => ['secret' => true], 'idempotency_key' => 'secret-key']);
        $token = $admin->createToken('android')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/admin/guru')->assertOk()->assertJsonPath('meta.total', 1);
        $this->withToken($token)->getJson('/api/v1/admin/rpps')->assertOk()->assertJsonPath('meta.total', 1)
            ->assertJsonMissing(['generation_input' => ['secret' => true]])->assertJsonMissing(['idempotency_key' => 'secret-key']);
    }
}
