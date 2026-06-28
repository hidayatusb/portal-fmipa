<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use App\Notifications\AssignmentSubmittedNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class AssignmentSubmissionNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_lecturer_receives_notification_when_student_submits_assignment(): void
    {
        Notification::fake();

        [$course, $assignment, $lecturer, $student] = $this->seedCourseWithAssignment();

        Livewire::actingAs($student)
            ->test(\App\Livewire\Mahasiswa\Elearning\ShowAssignment::class, [
                'course' => $course,
                'assignment' => $assignment,
            ])
            ->set('content', 'Ini jawaban tugas saya')
            ->call('submitAssignment')
            ->assertHasNoErrors();

        Notification::assertSentTo(
            $lecturer,
            AssignmentSubmittedNotification::class,
            function (AssignmentSubmittedNotification $notification) use ($assignment, $student) {
                return $notification->submission->assignment_id === $assignment->id
                    && $notification->submission->user_id === $student->id
                    && $notification->isUpdate === false;
            }
        );
    }

    public function test_lecturer_receives_update_notification_when_student_resubmits(): void
    {
        Notification::fake();

        [$course, $assignment, $lecturer, $student] = $this->seedCourseWithAssignment();

        $submission = $assignment->submissions()->create([
            'user_id' => $student->id,
            'content' => 'Jawaban awal',
            'submitted_at' => now(),
        ]);

        Livewire::actingAs($student)
            ->test(\App\Livewire\Mahasiswa\Elearning\ShowAssignment::class, [
                'course' => $course,
                'assignment' => $assignment,
            ])
            ->set('content', 'Jawaban diperbarui')
            ->call('submitAssignment')
            ->assertHasNoErrors();

        Notification::assertSentTo(
            $lecturer,
            AssignmentSubmittedNotification::class,
            fn (AssignmentSubmittedNotification $notification) => $notification->isUpdate === true
                && $notification->submission->id === $submission->id
        );
    }

    /**
     * @return array{0: Course, 1: Assignment, 2: User, 3: User}
     */
    protected function seedCourseWithAssignment(): array
    {
        $lecturer = User::create([
            'name' => 'Dr. Budi',
            'username' => 'dosen',
            'email' => 'dosen@lms.test',
            'password' => 'password',
            'role' => UserRole::Dosen,
        ]);

        $student = User::create([
            'name' => 'Mahasiswa Satu',
            'username' => 'mhs1',
            'email' => 'mhs1@lms.test',
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
            'due_date' => Carbon::now()->addDays(3),
            'accept_late_submissions' => false,
        ]);

        return [$course, $assignment, $lecturer, $student];
    }
}
