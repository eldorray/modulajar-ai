<?php

namespace Tests\Feature\Api\V1;

use App\Models\Guru;
use App\Models\Rpp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AndroidCompletionContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_rpp_options_expose_verified_closed_domain_values(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('android')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/rpp-options')->assertOk()
            ->assertJsonPath('data.semesters', ['Ganjil', 'Genap'])
            ->assertJsonPath('data.roles', ['admin', 'guru'])
            ->assertJsonPath('data.meetings.0', 1)
            ->assertJsonStructure(['data' => ['phases', 'semesters', 'assessments', 'curricula', 'themes', 'roles', 'guru_statuses', 'genders']]);
    }

    public function test_rpp_list_supports_search_status_and_pagination(): void
    {
        $user = User::factory()->create();
        Rpp::factory()->count(11)->create(['user_id' => $user->id, 'status' => 'completed', 'mata_pelajaran' => 'Matematika']);
        Rpp::factory()->create(['user_id' => $user->id, 'status' => 'failed', 'mata_pelajaran' => 'IPA']);
        $token = $user->createToken('android')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/rpps?search=Matematika&status=completed&page=2')
            ->assertOk()->assertJsonPath('meta.total', 11)->assertJsonCount(1, 'data');
    }

    public function test_admin_lists_support_search_filters_and_rpp_owner_summary(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru', 'name' => 'Guru Matematika']);
        Guru::query()->create(['user_id' => $guru->id, 'nik' => '1234567890', 'nama' => 'Guru Matematika', 'status' => 'aktif']);
        $rpp = Rpp::factory()->create(['user_id' => $guru->id, 'status' => 'completed', 'mata_pelajaran' => 'Matematika']);
        $token = $admin->createToken('android')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/admin/users?search=Guru&role=guru')->assertJsonPath('meta.total', 1);
        $this->withToken($token)->getJson('/api/v1/admin/guru?search=Matematika&status=aktif')->assertJsonPath('meta.total', 1);
        $this->withToken($token)->getJson('/api/v1/admin/rpps?search=Matematika&status=completed')
            ->assertJsonPath('data.0.id', $rpp->id)
            ->assertJsonPath('data.0.owner.name', 'Guru Matematika');
    }
}
