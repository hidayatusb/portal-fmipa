<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BreadcrumbTest extends TestCase
{
    use RefreshDatabase;

    public function test_elearning_page_renders_breadcrumb_in_header(): void
    {
        $user = User::create([
            'name' => 'Dr. Budi',
            'username' => 'dosen',
            'email' => 'dosen@lms.test',
            'password' => 'password',
            'role' => UserRole::Dosen,
        ]);

        $response = $this->actingAs($user)->get(route('dosen.elearning.index'));

        $response->assertOk();
        $response->assertSee('kt-breadcrumb', false);
        $response->assertSee('E-Learning', false);
    }
}
