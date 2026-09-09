<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class ApiFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware('api')->prefix('api/v1/_contract-test')->group(function (): void {
            Route::get('paginated', function () {
                return api_response()->paginated(User::query()->orderBy('id')->paginate(2));
            });

            Route::get('forbidden', function (): never {
                throw new AuthorizationException('Akses ditolak.');
            });

            Route::get('failure', function (): never {
                throw new RuntimeException('rahasia internal');
            });
        });
    }

    public function test_paginated_response_has_stable_data_meta_and_links_envelope(): void
    {
        User::factory()->count(3)->create();

        $this->getJson('/api/v1/_contract-test/paginated?page=2')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.current_page', 2)
            ->assertJsonPath('meta.last_page', 2)
            ->assertJsonPath('meta.per_page', 2)
            ->assertJsonPath('meta.total', 3)
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'from', 'last_page', 'per_page', 'to', 'total'],
                'links' => ['first', 'last', 'prev', 'next'],
            ]);
    }

    public function test_unknown_api_route_uses_not_found_error_envelope(): void
    {
        $this->getJson('/api/v1/tidak-ada')
            ->assertNotFound()
            ->assertExactJson([
                'message' => 'Resource not found.',
                'code' => 'NOT_FOUND',
            ]);
    }

    public function test_wrong_http_method_uses_method_not_allowed_error_envelope(): void
    {
        $this->putJson('/api/v1/auth/me')
            ->assertStatus(405)
            ->assertExactJson([
                'message' => 'Method not allowed.',
                'code' => 'METHOD_NOT_ALLOWED',
            ]);
    }

    public function test_authorization_failure_uses_forbidden_error_envelope(): void
    {
        $this->getJson('/api/v1/_contract-test/forbidden')
            ->assertForbidden()
            ->assertExactJson([
                'message' => 'Akses ditolak.',
                'code' => 'FORBIDDEN',
            ]);
    }

    public function test_unexpected_exception_does_not_leak_internal_message(): void
    {
        $this->getJson('/api/v1/_contract-test/failure')
            ->assertInternalServerError()
            ->assertExactJson([
                'message' => 'Internal server error.',
                'code' => 'INTERNAL_ERROR',
            ]);
    }

    public function test_login_rate_limit_uses_contract_error_envelope(): void
    {
        for ($attempt = 1; $attempt <= 5; $attempt++) {
            $this->postJson('/api/v1/auth/login', [
                'email' => 'tidak-ada@example.com',
                'password' => 'salah',
                'device_name' => 'test-device',
            ])->assertUnprocessable();
        }

        $this->postJson('/api/v1/auth/login', [
            'email' => 'tidak-ada@example.com',
            'password' => 'salah',
            'device_name' => 'test-device',
        ])->assertTooManyRequests()
            ->assertJsonPath('code', 'RATE_LIMITED')
            ->assertJsonStructure(['message', 'code', 'retry_after']);
    }
}
