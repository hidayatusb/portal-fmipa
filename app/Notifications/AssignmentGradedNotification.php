<?php

namespace App\Notifications;

use App\Models\AssignmentSubmission;
use App\Notifications\Concerns\DeliversPushNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AssignmentGradedNotification extends Notification
{
    use DeliversPushNotification;
    use Queueable;

    public function __construct(public AssignmentSubmission $submission) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $this->submission->loadMissing(['assignment.course']);

        $assignment = $this->submission->assignment;
        $course = $assignment->course;

        return [
            'type' => 'assignment_graded',
            'title' => 'Tugas Dinilai',
            'message' => sprintf(
                'Tugas "%s" (%s) telah dinilai. Skor: %s',
                $assignment->title,
                $course->code,
                $this->submission->score,
            ),
            'course_id' => $course->id,
            'assignment_id' => $assignment->id,
            'submission_id' => $this->submission->id,
            'score' => $this->submission->score,
            'feedback' => $this->submission->feedback,
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
                'type' => 'assignment_graded',
                'course_id' => (string) $payload['course_id'],
                'assignment_id' => (string) $payload['assignment_id'],
                'submission_id' => (string) $payload['submission_id'],
                'score' => (string) $payload['score'],
            ],
        );
    }
}
