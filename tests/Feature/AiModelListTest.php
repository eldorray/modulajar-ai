<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\AiSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AiModelListTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    public function test_non_admin_cannot_list_models(): void
    {
        $user = User::factory()->create(['role' => 'guru']);

        $this->actingAs($user)->postJson(route('admin.ai.models'))->assertForbidden();
    }

    public function test_it_returns_sorted_unique_model_ids(): void
    {
        AiSetting::getSettings()->update([
            'api_key' => 'sk-tersimpan',
            'endpoint' => 'https://api.example.com/v1/chat/completions',
        ]);

        Http::fake([
            'api.example.com/v1/models' => Http::response([
                'data' => [
                    ['id' => 'zeta-chat'],
                    ['id' => 'alpha-chat'],
                    ['id' => 'alpha-chat'],
                ],
            ]),
        ]);

        $this->actingAs($this->admin())
            ->postJson(route('admin.ai.models'))
            ->assertOk()
            ->assertExactJson(['models' => ['alpha-chat', 'zeta-chat']]);
    }

    /**
     * Endpoint yang disimpan adalah URL chat/completions; daftar model ada di
     * {base}/models. Kalau pemotongan sufiks ini salah, request nyasar.
     */
    public function test_it_derives_the_models_url_from_the_chat_endpoint(): void
    {
        AiSetting::getSettings()->update(['api_key' => 'sk-tersimpan']);

        Http::fake(['*' => Http::response(['data' => [['id' => 'a']]])]);

        $this->actingAs($this->admin())->postJson(route('admin.ai.models'), [
            'endpoint' => 'https://api.deepseek.com/v1/chat/completions',
        ])->assertOk();

        Http::assertSent(fn ($request) => $request->url() === 'https://api.deepseek.com/v1/models');
    }

    public function test_form_values_take_precedence_over_saved_ones(): void
    {
        AiSetting::getSettings()->update([
            'api_key' => 'sk-tersimpan',
            'endpoint' => 'https://tersimpan.example.com/v1/chat/completions',
        ]);

        Http::fake(['*' => Http::response(['data' => [['id' => 'a']]])]);

        $this->actingAs($this->admin())->postJson(route('admin.ai.models'), [
            'endpoint' => 'https://baru.example.com/v1/chat/completions',
            'api_key' => 'sk-baru',
        ])->assertOk();

        Http::assertSent(fn ($request) => $request->url() === 'https://baru.example.com/v1/models'
            && $request->hasHeader('Authorization', 'Bearer sk-baru'));
    }

    public function test_it_falls_back_to_the_saved_key_when_the_form_field_is_blank(): void
    {
        AiSetting::getSettings()->update([
            'api_key' => 'sk-tersimpan',
            'endpoint' => 'https://api.example.com/v1/chat/completions',
        ]);

        Http::fake(['*' => Http::response(['data' => [['id' => 'a']]])]);

        $this->actingAs($this->admin())->postJson(route('admin.ai.models'), ['api_key' => ''])->assertOk();

        Http::assertSent(fn ($request) => $request->hasHeader('Authorization', 'Bearer sk-tersimpan'));
    }

    public function test_it_reports_a_missing_api_key_without_calling_out(): void
    {
        AiSetting::getSettings()->update(['api_key' => null]);
        config(['deepseek.api_key' => null]);

        Http::fake();

        $this->actingAs($this->admin())
            ->postJson(route('admin.ai.models'))
            ->assertStatus(422);

        Http::assertNothingSent();
    }

    public function test_it_reports_provider_failure_as_bad_gateway(): void
    {
        AiSetting::getSettings()->update([
            'api_key' => 'sk-tersimpan',
            'endpoint' => 'https://api.example.com/v1/chat/completions',
        ]);

        Http::fake(['*' => Http::response(['error' => 'nope'], 401)]);

        $this->actingAs($this->admin())
            ->postJson(route('admin.ai.models'))
            ->assertStatus(502);
    }

    public function test_it_rejects_a_non_url_endpoint(): void
    {
        AiSetting::getSettings()->update(['api_key' => 'sk-tersimpan']);

        Http::fake();

        $this->actingAs($this->admin())
            ->postJson(route('admin.ai.models'), ['endpoint' => 'bukan-url'])
            ->assertStatus(422);

        Http::assertNothingSent();
    }

    public function test_the_page_shows_the_reload_button(): void
    {
        $this->actingAs($this->admin())
            ->get(route('admin.ai.edit'))
            ->assertOk()
            ->assertSee('Reload Model')
            ->assertSee('aiModelList(', false);
    }
}
