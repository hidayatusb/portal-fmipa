<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileApiTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(): User
    {
        return User::create([
            'name' => 'Dr. Budi',
            'username' => 'dosen',
            'email' => 'dosen@lms.test',
            'password' => 'password',
            'role' => UserRole::Dosen,
        ]);
    }

    public function test_profile_picture_endpoint_requires_authentication(): void
    {
        $this->get('/api/profile/picture')->assertUnauthorized();
    }

    public function test_profile_picture_endpoint_returns_not_found_when_user_has_no_picture(): void
    {
        $user = $this->createUser();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->get('/api/profile/picture')
            ->assertNotFound();
    }

    public function test_authenticated_user_can_download_profile_picture(): void
    {
        Storage::fake('public');

        $user = $this->createUser();
        $path = 'profile-pictures/test.jpg';
        Storage::disk('public')->put($path, 'fake-image-content');
        $user->update(['profile_picture' => $path]);

        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->get('/api/profile/picture')
            ->assertOk()
            ->assertHeader('content-disposition');
    }

    public function test_profile_response_returns_api_profile_picture_url(): void
    {
        Storage::fake('public');

        $user = $this->createUser();
        $path = 'profile-pictures/test.jpg';
        Storage::disk('public')->put($path, 'fake-image-content');
        $user->update(['profile_picture' => $path]);

        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/profile');

        $response->assertOk()
            ->assertJsonPath('success', true);

        $url = $response->json('data.profile_picture_url');
        $this->assertIsString($url);
        $this->assertStringContainsString('/api/profile/picture', $url);
    }

    public function test_user_can_upload_profile_picture_via_api(): void
    {
        Storage::fake('public');

        $user = $this->createUser();
        $token = $user->createToken('test')->plainTextToken;

        $response = $this->withToken($token)->patch('/api/profile', [
            'profile_picture' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $user->refresh();
        $this->assertNotNull($user->profile_picture);
        Storage::disk('public')->assertExists($user->profile_picture);
    }
}
