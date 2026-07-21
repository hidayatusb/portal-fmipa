<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\User;
use App\Notifications\AssignmentDeadlineReminderNotification;
use App\Notifications\AssignmentGradedNotification;
use App\Notifications\AssignmentSubmittedNotification;
use App\Notifications\NewAssignmentNotification;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class NotificationSeeder extends Seeder
{
    public function run(): void
    {
        $dosen = User::query()->where('username', 'dosen')->first();
        $mahasiswa = User::query()->where('username', 'mahasiswa')->first()
            ?? User::query()->where('role', UserRole::Mahasiswa)->first();

        if (! $dosen && ! $mahasiswa) {
            $this->command?->warn('NotificationSeeder: tidak ada user dosen/mahasiswa. Lewati.');

            return;
        }

        $assignment = Assignment::query()->with('course')->latest('id')->first();

        if ($mahasiswa) {
            $mahasiswa->notifications()->delete();
            $this->seedMahasiswaNotifications($mahasiswa, $assignment);
        }

        if ($dosen) {
            $dosen->notifications()->delete();
            $this->seedDosenNotifications($dosen, $assignment, $mahasiswa);
        }

        $this->command?->info('NotificationSeeder: notifikasi demo berhasil dibuat.');
    }

    protected function seedMahasiswaNotifications(User $mahasiswa, ?Assignment $assignment): void
    {
        $courseCode = $assignment?->course?->code ?? 'PW101';
        $courseId = $assignment?->course_id ?? 1;
        $assignmentId = $assignment?->id ?? 1;
        $assignmentTitle = $assignment?->title ?? 'Tugas 1: Halaman Profil HTML';
        $dueDate = $assignment?->due_date ?? now()->addDays(7);
        $url = ($assignment?->course)
            ? route('mahasiswa.elearning.assignments.show', [
                'course' => $assignment->course,
                'assignment' => $assignment,
            ])
            : null;

        $this->createNotification(
            $mahasiswa,
            NewAssignmentNotification::class,
            [
                'type' => 'assignment_new',
                'title' => 'Tugas Baru',
                'message' => sprintf(
                    'Tugas baru "%s" di kelas %s. Deadline: %s',
                    $assignmentTitle,
                    $courseCode,
                    $dueDate->locale('id')->translatedFormat('d M Y, H:i'),
                ),
                'course_id' => $courseId,
                'assignment_id' => $assignmentId,
                'due_date' => $dueDate->toIso8601String(),
                'url' => $url,
            ],
            readAt: null,
            createdAt: now()->subHours(2),
        );

        $this->createNotification(
            $mahasiswa,
            AssignmentDeadlineReminderNotification::class,
            [
                'type' => 'assignment_deadline',
                'title' => 'Deadline Mendekat',
                'message' => sprintf(
                    'Tugas "%s" (%s) deadline tinggal 24 jam lagi (%s)',
                    $assignmentTitle,
                    $courseCode,
                    $dueDate->locale('id')->translatedFormat('d M Y, H:i'),
                ),
                'course_id' => $courseId,
                'assignment_id' => $assignmentId,
                'hours_before' => 24,
                'due_date' => $dueDate->toIso8601String(),
                'url' => $url,
            ],
            readAt: null,
            createdAt: now()->subHour(),
        );

        $this->createNotification(
            $mahasiswa,
            AssignmentGradedNotification::class,
            [
                'type' => 'assignment_graded',
                'title' => 'Tugas Dinilai',
                'message' => sprintf(
                    'Tugas "%s" (%s) telah dinilai. Skor: 88',
                    $assignmentTitle,
                    $courseCode,
                ),
                'course_id' => $courseId,
                'assignment_id' => $assignmentId,
                'submission_id' => 1,
                'score' => 88,
                'feedback' => 'Bagus, lanjutkan.',
                'url' => $url,
            ],
            readAt: now()->subMinutes(30),
            createdAt: now()->subDays(1),
        );

        $this->createNotification(
            $mahasiswa,
            NewAssignmentNotification::class,
            [
                'type' => 'assignment_new',
                'title' => 'Tugas Baru',
                'message' => sprintf(
                    'Tugas baru "Latihan Tambahan" di kelas %s. Deadline: %s',
                    $courseCode,
                    now()->addDays(10)->locale('id')->translatedFormat('d M Y, H:i'),
                ),
                'course_id' => $courseId,
                'assignment_id' => $assignmentId,
                'due_date' => now()->addDays(10)->toIso8601String(),
                'url' => $url,
            ],
            readAt: null,
            createdAt: now()->subMinutes(15),
        );
    }

    protected function seedDosenNotifications(User $dosen, ?Assignment $assignment, ?User $mahasiswa): void
    {
        $course = $assignment?->course;
        $courseCode = $course?->code ?? 'PW101';
        $courseId = $course?->id ?? 1;
        $assignmentId = $assignment?->id ?? 1;
        $assignmentTitle = $assignment?->title ?? 'Tugas 1: Halaman Profil HTML';
        $studentName = $mahasiswa?->name ?? 'Andi Pratama';
        $submissionId = 1;

        $submission = null;

        if ($assignment && $mahasiswa) {
            $submission = AssignmentSubmission::query()
                ->where('assignment_id', $assignment->id)
                ->where('user_id', $mahasiswa->id)
                ->first();

            if ($submission) {
                $submissionId = $submission->id;
            }
        }

        $url = null;

        if ($course && $assignment && $submission) {
            $url = route('dosen.elearning.submissions.show', [
                'course' => $course,
                'assignment' => $assignment,
                'submission' => $submission,
            ]);
        } elseif ($course && $assignment) {
            $url = route('dosen.elearning.assignments.show', [
                'course' => $course,
                'assignment' => $assignment,
            ]);
        }

        $this->createNotification(
            $dosen,
            AssignmentSubmittedNotification::class,
            [
                'type' => 'assignment_submitted',
                'title' => 'Tugas Dikumpulkan',
                'message' => sprintf(
                    '%s mengumpulkan tugas "%s" (%s)',
                    $studentName,
                    $assignmentTitle,
                    $courseCode,
                ),
                'course_id' => $courseId,
                'assignment_id' => $assignmentId,
                'submission_id' => $submissionId,
                'student_name' => $studentName,
                'is_late' => false,
                'is_update' => false,
                'url' => $url,
            ],
            readAt: null,
            createdAt: now()->subMinutes(45),
        );

        $this->createNotification(
            $dosen,
            AssignmentSubmittedNotification::class,
            [
                'type' => 'assignment_submitted',
                'title' => 'Pengumpulan Diperbarui',
                'message' => sprintf(
                    '%s memperbarui pengumpulan tugas "%s" (%s)',
                    $studentName,
                    $assignmentTitle,
                    $courseCode,
                ),
                'course_id' => $courseId,
                'assignment_id' => $assignmentId,
                'submission_id' => $submissionId,
                'student_name' => $studentName,
                'is_late' => true,
                'is_update' => true,
                'url' => $url,
            ],
            readAt: null,
            createdAt: now()->subMinutes(20),
        );

        $this->createNotification(
            $dosen,
            AssignmentSubmittedNotification::class,
            [
                'type' => 'assignment_submitted',
                'title' => 'Tugas Dikumpulkan',
                'message' => sprintf(
                    'Budi Santoso mengumpulkan tugas "%s" (%s)',
                    $assignmentTitle,
                    $courseCode,
                ),
                'course_id' => $courseId,
                'assignment_id' => $assignmentId,
                'submission_id' => $submissionId,
                'student_name' => 'Budi Santoso',
                'is_late' => false,
                'is_update' => false,
                'url' => $url,
            ],
            readAt: now()->subHours(3),
            createdAt: now()->subDays(2),
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    protected function createNotification(
        User $user,
        string $type,
        array $data,
        ?Carbon $readAt = null,
        ?Carbon $createdAt = null,
    ): void {
        $createdAt ??= now();

        $user->notifications()->create([
            'id' => (string) Str::uuid(),
            'type' => $type,
            'data' => $data,
            'read_at' => $readAt,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);
    }
}
