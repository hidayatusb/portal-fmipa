<?php

namespace App\Notifications;

use App\Models\Assignment;
use App\Notifications\Concerns\DeliversPushNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewAssignmentNotification extends Notification
{
    use DeliversPushNotification;
    use Queueable;

    public function __construct(public Assignment $assignment) {}

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
                'type' => 'assignment_new',
                'course_id' => (string) $payload['course_id'],
                'assignment_id' => (string) $payload['assignment_id'],
            ],
        );
    }
}
