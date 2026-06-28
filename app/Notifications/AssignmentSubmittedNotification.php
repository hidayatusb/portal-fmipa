<?php

namespace App\Notifications;

use App\Models\AssignmentSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignmentSubmittedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public AssignmentSubmission $submission,
        public bool $isUpdate = false,
    ) {}

    /**
     * @return list<string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->submission->loadMissing(['student', 'assignment.course']);

        $assignment = $this->submission->assignment;
        $course = $assignment->course;
        $student = $this->submission->student;

        return [
            'title' => $this->isUpdate ? 'Pengumpulan Diperbarui' : 'Tugas Dikumpulkan',
            'message' => sprintf(
                '%s %s tugas "%s" (%s)',
                $student->name,
                $this->isUpdate ? 'memperbarui pengumpulan' : 'mengumpulkan',
                $assignment->title,
                $course->code,
            ),
            'course_id' => $course->id,
            'assignment_id' => $assignment->id,
            'submission_id' => $this->submission->id,
            'student_name' => $student->name,
            'is_late' => $this->submission->isLate(),
            'is_update' => $this->isUpdate,
            'url' => route('dosen.elearning.submissions.show', [
                'course' => $course,
                'assignment' => $assignment,
                'submission' => $this->submission,
            ]),
        ];
    }
}
