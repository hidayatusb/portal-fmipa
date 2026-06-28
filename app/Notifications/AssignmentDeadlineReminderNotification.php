<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Notifications\Concerns\DeliversPushNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignmentDeadlineReminderNotification extends Notification
{
    use DeliversPushNotification;
    use Queueable;

    public function __construct(
        public Assignment $assignment,
        public int $hoursBefore,
    ) {}

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
        ];
    }

    /**
     * @return array{title: string, body: string, data: array<string, string|int|bool|null>}
     */
    public function pushPayload(object $notifiable): array
    {
        $payload = $this->toArray($notifiable);

        return $this->pushMessage(
            $payload['title'],
            $payload['message'],
            [
                'type' => 'assignment_deadline',
                'course_id' => (string) $payload['course_id'],
                'assignment_id' => (string) $payload['assignment_id'],
                'hours_before' => (string) $this->hoursBefore,
            ],
        );
    }
}
