<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_device_token_with_capabilities(): void
    {
        $user = User::factory()->create([
            'email' => 'guru@example.com',
            'password' => Hash::make('password-rahasia'),
            'role' => 'guru',
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'guru@example.com',
            'password' => 'password-rahasia',
            'device_name' => 'Pixel Guru',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonPath('data.user.id', $user->id)
            ->assertJsonPath('data.user.role', 'guru')
            ->assertJsonPath('data.user.capabilities.0', 'dashboard.view')
            ->assertJsonStructure(['data' => ['token', 'token_type', 'user' => ['id', 'name', 'email', 'role', 'capabilities']]]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'Pixel Guru',
        ]);
    }

    public function test_login_rejects_invalid_credentials_without_creating_token(): void
    {
        User::factory()->create([
            'email' => 'guru@example.com',
            'password' => Hash::make('password-rahasia'),
        ]);

        $this->postJson('/api/v1/auth/login', [
            'email' => 'guru@example.com',
            'password' => 'salah',
            'device_name' => 'Pixel Guru',
        ])->assertUnprocessable()
            ->assertJsonPath('code', 'INVALID_CREDENTIALS')
            ->assertJsonStructure(['message', 'code', 'errors']);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_login_validates_required_fields_with_contract_error_envelope(): void
    {
        $this->postJson('/api/v1/auth/login', [])
            ->assertUnprocessable()
            ->assertJsonPath('code', 'VALIDATION_FAILED')
            ->assertJsonValidationErrors(['email', 'password', 'device_name']);
    }

    public function test_authenticated_user_can_read_me_with_admin_capabilities(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test-device')->plainTextToken;

        $this->withToken($token)->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonPath('data.role', 'admin')
            ->assertJsonFragment(['admin.users.manage']);
    }

    public function test_me_rejects_unauthenticated_request_with_contract_error(): void
    {
        $this->getJson('/api/v1/auth/me')
            ->assertUnauthorized()
            ->assertExactJson([
                'message' => 'Unauthenticated.',
                'code' => 'UNAUTHENTICATED',
            ]);
    }

    public function test_logout_revokes_only_current_access_token(): void
    {
        $user = User::factory()->create();
        $current = $user->createToken('current-device');
        $other = $user->createToken('other-device');

        $this->withToken($current->plainTextToken)
            ->postJson('/api/v1/auth/logout')
            ->assertNoContent();

        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $current->accessToken->id]);
        $this->assertDatabaseHas('personal_access_tokens', ['id' => $other->accessToken->id]);
    }
}
