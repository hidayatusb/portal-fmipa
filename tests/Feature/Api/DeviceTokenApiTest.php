<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\DeviceToken;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeviceTokenApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_fcm_token_after_login(): void
    {
        $user = User::create([
            'name' => 'Mahasiswa',
            'username' => 'mhs1',
            'email' => 'mhs1@lms.test',
            'password' => 'password',
            'role' => UserRole::Mahasiswa,
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/device-tokens', [
            'token' => 'fcm-token-android-123',
            'platform' => 'android',
            'device_name' => 'Pixel 8',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('device_tokens', [
            'user_id' => $user->id,
            'token' => 'fcm-token-android-123',
            'platform' => 'android',
        ]);
    }

    public function test_user_can_delete_own_fcm_token(): void
    {
        $user = User::create([
            'name' => 'Dosen',
            'username' => 'dosen',
            'email' => 'dosen@lms.test',
            'password' => 'password',
            'role' => UserRole::Dosen,
        ]);

        DeviceToken::create([
            'user_id' => $user->id,
            'token' => 'token-to-delete',
            'platform' => 'android',
        ]);

        Sanctum::actingAs($user);

        $this->deleteJson('/api/device-tokens', [
            'token' => 'token-to-delete',
        ])->assertOk();

        $this->assertDatabaseMissing('device_tokens', [
            'token' => 'token-to-delete',
        ]);
    }
}
