<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_via_api_and_receive_token(): void
    {
        User::create([
            'name' => 'Dr. Budi',
            'username' => 'dosen',
            'email' => 'dosen@lms.test',
            'password' => 'password',
            'role' => UserRole::Dosen,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'username' => 'dosen',
            'password' => 'password',
            'device_name' => 'test-device',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.role', 'dosen')
            ->assertJsonStructure(['data' => ['token', 'token_type', 'user']]);
    }

    public function test_dosen_can_access_protected_dashboard(): void
    {
        $user = User::create([
            'name' => 'Dr. Budi',
            'username' => 'dosen',
            'email' => 'dosen@lms.test',
            'password' => 'password',
            'role' => UserRole::Dosen,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/dosen/dashboard')
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['stats', 'recent_courses']]);
    }

    public function test_mahasiswa_cannot_access_dosen_routes(): void
    {
        $user = User::create([
            'name' => 'Mhs',
            'username' => 'mhs',
            'email' => 'mhs@lms.test',
            'password' => 'password',
            'role' => UserRole::Mahasiswa,
        ]);

        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson('/api/dosen/dashboard')
            ->assertForbidden()
            ->assertJsonPath('success', false);
    }
}
