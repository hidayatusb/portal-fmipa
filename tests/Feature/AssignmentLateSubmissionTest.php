<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AssignmentLateSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_mahasiswa_cannot_submit_when_closed_after_deadline(): void
    {
        [$course, $assignment, $student] = $this->seedAssignment(acceptLate: false);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Mahasiswa\Elearning\ShowAssignment::class, [
                'course' => $course,
                'assignment' => $assignment,
            ])
            ->set('content', 'Jawaban saya')
            ->call('submitAssignment')
            ->assertHasErrors('file');
    }

    public function test_mahasiswa_can_submit_when_late_submissions_allowed(): void
    {
        [$course, $assignment, $student] = $this->seedAssignment(acceptLate: true);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Mahasiswa\Elearning\ShowAssignment::class, [
                'course' => $course,
                'assignment' => $assignment,
            ])
            ->set('content', 'Jawaban terlambat')
            ->call('submitAssignment')
            ->assertHasNoErrors()
            ->assertSet('submission.content', 'Jawaban terlambat');

        $submission = $assignment->fresh()->submissionFor($student->id);

        $this->assertTrue($submission->isLate());
    }

    /**
     * @return array{0: Course, 1: Assignment, 2: User}
     */
    protected function seedAssignment(bool $acceptLate): array
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
            'username' => 'mhs',
            'email' => 'mhs@lms.test',
            'password' => 'password',
            'role' => UserRole::Mahasiswa,
        ]);

        $course = Course::create([
            'user_id' => $lecturer->id,
            'title' => 'Pemrograman Web',
            'code' => 'PW101',
            'description' => null,
        ]);

        $course->students()->attach($student->id);

        $assignment = Assignment::create([
            'course_id' => $course->id,
            'title' => 'Tugas 1',
            'description' => 'Kerjakan',
            'due_date' => Carbon::now()->subDay(),
            'accept_late_submissions' => $acceptLate,
        ]);

        return [$course, $assignment, $student];
    }
}
