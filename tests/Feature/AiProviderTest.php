<?php

namespace Tests\Feature;

use App\Models\AiUsageLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AiProviderTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_ai_provider_and_usage_log_defaults_are_deepseek(): void
    {
        $user = User::factory()->create();

        $usageLog = AiUsageLog::create([
            'user_id' => $user->id,
        ])->refresh();

        $this->assertSame('deepseek', config('ai.default'));
        $this->assertSame('deepseek', $usageLog->provider);
        $this->assertSame('deepseek-chat', $usageLog->model_name);
    }

    public function test_admin_dashboard_does_not_show_gemini_provider_stats(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        AiUsageLog::create([
            'user_id' => $admin->id,
            'provider' => 'deepseek',
            'model_name' => 'deepseek-chat',
            'input_tokens' => 1000,
            'output_tokens' => 500,
            'total_tokens' => 1500,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('DeepSeek Chat');
        $response->assertDontSee('Gemini');
        $response->assertDontSee('2.5 Flash');
    }
}
