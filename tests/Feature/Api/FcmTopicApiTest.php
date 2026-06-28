<?php

namespace Tests\Feature\Api;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use App\Support\FcmTopic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FcmTopicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_gets_student_topics_for_enrolled_courses(): void
    {
        $lecturer = User::create([
            'name' => 'Dosen',
            'username' => 'dosen',
            'email' => 'dosen@lms.test',
            'password' => 'password',
            'role' => UserRole::Dosen,
        ]);

        $student = User::create([
            'name' => 'Mahasiswa',
            'username' => 'mhs1',
            'email' => 'mhs1@lms.test',
            'password' => 'password',
            'role' => UserRole::Mahasiswa,
        ]);

        $course = Course::create([
            'user_id' => $lecturer->id,
            'title' => 'PW',
            'code' => 'PW101',
        ]);

        $course->students()->attach($student->id);

        Sanctum::actingAs($student);

        $this->getJson('/api/fcm/topics')
            ->assertOk()
            ->assertJsonPath('data.topics.0', FcmTopic::students($course->id));
    }

    public function test_dosen_gets_lecturer_topics_for_owned_courses(): void
    {
        $lecturer = User::create([
            'name' => 'Dosen',
            'username' => 'dosen',
            'email' => 'dosen@lms.test',
            'password' => 'password',
            'role' => UserRole::Dosen,
        ]);

        $course = Course::create([
            'user_id' => $lecturer->id,
            'title' => 'PW',
            'code' => 'PW101',
        ]);

        Sanctum::actingAs($lecturer);

        $this->getJson('/api/fcm/topics')
            ->assertOk()
            ->assertJsonPath('data.topics.0', FcmTopic::lecturer($course->id));
    }
}
