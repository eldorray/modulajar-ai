<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLayoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_sidebar_state_script_tetap_utuh_di_tag_body_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $html = $this->actingAs($admin)->get(route('dashboard'))
            ->assertOk()
            ->getContent();

        $this->assertMatchesRegularExpression(
            '/<body[^>]+x-data="\{ sidebarCollapsed: localStorage\.getItem\(\'sidebarCollapsed\'\) === \'true\' \}"[^>]+x-init="\$watch\(\'sidebarCollapsed\', val => localStorage\.setItem\(\'sidebarCollapsed\', val\)\)"[^>]*>/',
            $html,
        );

        $this->assertDoesNotMatchRegularExpression(
            '/<\/a>\s*localStorage\.setItem\(\'sidebarCollapsed\', val\)\)\">/',
            $html,
        );
    }
}
