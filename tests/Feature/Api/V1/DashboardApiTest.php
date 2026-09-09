<?php

namespace Tests\Feature\Api\V1;

use App\Models\Rpp;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_returns_owner_scoped_stats_and_five_recent_rpps(): void
    {
        $user = User::factory()->create(['role' => 'guru']);
        $other = User::factory()->create();
        foreach (['completed', 'completed', 'processing', 'failed', 'draft', 'completed'] as $status) {
            Rpp::factory()->create(['user_id' => $user->id, 'status' => $status]);
        }
        Rpp::factory()->create(['user_id' => $other->id, 'status' => 'completed']);
        $token = $user->createToken('android')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonPath('data.stats.total', 6)
            ->assertJsonPath('data.stats.completed', 3)
            ->assertJsonPath('data.stats.processing', 1)
            ->assertJsonPath('data.stats.failed', 1)
            ->assertJsonPath('data.stats.draft', 1)
            ->assertJsonCount(5, 'data.recent_rpps')
            ->assertJsonMissing(['user_id' => $other->id]);
    }

    public function test_empty_dashboard_returns_zero_stats_and_empty_recent_list(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('android')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonPath('data.stats.total', 0)
            ->assertJsonPath('data.recent_rpps', []);
    }
}
