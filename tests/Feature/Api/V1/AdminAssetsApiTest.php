<?php

namespace Tests\Feature\Api\V1;

use App\Models\Rpp;
use App\Models\SchoolSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAssetsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_upload_and_delete_school_logo(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('android')->plainTextToken;

        $this->withToken($token)->post('/api/v1/settings', [
            '_method' => 'PATCH',
            'nama_sekolah' => 'Madrasah',
            'logo' => UploadedFile::fake()->image('logo.png'),
        ], ['Accept' => 'application/json'])->assertOk();

        $settings = SchoolSetting::getSettings();
        Storage::disk('public')->assertExists($settings->logo);
        $this->withToken($token)->deleteJson('/api/v1/settings/logo')->assertNoContent();
        $this->assertNull($settings->fresh()->logo);
    }

    public function test_admin_can_download_completed_guru_rpp(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $guru = User::factory()->create(['role' => 'guru']);
        $rpp = Rpp::factory()->create(['user_id' => $guru->id, 'status' => 'completed', 'content_result' => ['tujuan_pembelajaran' => ['Tujuan']]]);
        $token = $admin->createToken('android')->plainTextToken;

        $this->withToken($token)->get("/api/v1/admin/rpps/{$rpp->id}/pdf")
            ->assertOk()->assertHeader('content-type', 'application/pdf');
    }
}
