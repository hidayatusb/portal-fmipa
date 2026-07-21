<?php

namespace App\Notifications;

use App\Models\Assignment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignmentDeadlineReminderNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Assignment $assignment,
        public int $hoursBefore,
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
        $this->assignment->loadMissing('course');

        $course = $this->assignment->course;
        $label = $this->hoursBefore >= 24
            ? round($this->hoursBefore / 24).' hari'
            : $this->hoursBefore.' jam';

        return [
            'type' => 'assignment_deadline',
            'title' => 'Deadline Mendekat',
            'message' => sprintf(
                'Tugas "%s" (%s) deadline tinggal %s lagi (%s)',
                $this->assignment->title,
                $course->code,
                $label,
                $this->assignment->due_date->locale('id')->translatedFormat('d M Y, H:i'),
            ),
            'course_id' => $course->id,
            'assignment_id' => $this->assignment->id,
            'hours_before' => $this->hoursBefore,
            'due_date' => $this->assignment->due_date->toIso8601String(),
            'url' => route('mahasiswa.elearning.assignments.show', [
                'course' => $course,
                'assignment' => $this->assignment,
            ]),
        ];
    }
}
