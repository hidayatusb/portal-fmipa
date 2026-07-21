<?php

namespace App\Support;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Route;

class NotificationLink
{
    /**
     * @param  array<string, mixed>|DatabaseNotification  $notification
     */
    public static function resolve(array|DatabaseNotification $notification): ?string
    {
        $data = $notification instanceof DatabaseNotification
            ? $notification->data
            : $notification;

        if (! is_array($data)) {
            return null;
        }

        if (! empty($data['url']) && is_string($data['url'])) {
            return $data['url'];
        }

        $type = $data['type'] ?? null;
        $courseId = $data['course_id'] ?? null;
        $assignmentId = $data['assignment_id'] ?? null;
        $submissionId = $data['submission_id'] ?? null;

        return match ($type) {
            'assignment_new',
            'assignment_deadline',
            'assignment_graded' => self::safeRoute('mahasiswa.elearning.assignments.show', [
                'course' => $courseId,
                'assignment' => $assignmentId,
            ]),
            'assignment_submitted' => $submissionId
                ? self::safeRoute('dosen.elearning.submissions.show', [
                    'course' => $courseId,
                    'assignment' => $assignmentId,
                    'submission' => $submissionId,
                ])
                : self::safeRoute('dosen.elearning.assignments.show', [
                    'course' => $courseId,
                    'assignment' => $assignmentId,
                ]),
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $parameters
     */
    protected static function safeRoute(string $name, array $parameters): ?string
    {
        if (! Route::has($name)) {
            return null;
        }

        foreach ($parameters as $value) {
            if (blank($value)) {
                return null;
            }
        }

        try {
            return route($name, $parameters);
        } catch (\Throwable) {
            return null;
        }
    }
}
