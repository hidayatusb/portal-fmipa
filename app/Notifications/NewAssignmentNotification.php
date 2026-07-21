<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAssignmentNotification extends Notification
{
    use Queueable;

    public function __construct(public Assignment $assignment) {}

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
        $this->assignment->loadMissing('course');

        $course = $this->assignment->course;

        return [
            'type' => 'assignment_new',
            'title' => 'Tugas Baru',
            'message' => sprintf(
                'Tugas baru "%s" di kelas %s. Deadline: %s',
                $this->assignment->title,
                $course->code,
                $this->assignment->due_date->locale('id')->translatedFormat('d M Y, H:i'),
            ),
            'course_id' => $course->id,
            'assignment_id' => $this->assignment->id,
            'due_date' => $this->assignment->due_date->toIso8601String(),
            'url' => route('mahasiswa.elearning.assignments.show', [
                'course' => $course,
                'assignment' => $this->assignment,
            ]),
        ];
    }
}
