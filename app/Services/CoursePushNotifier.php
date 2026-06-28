<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Support\FcmTopic;

class CoursePushNotifier
{
    public static function pushNewAssignment(Assignment $assignment): void
    {
        $assignment->loadMissing('course');
        $course = $assignment->course;

        app(FcmService::class)->sendToTopic(
            FcmTopic::students($course->id),
            [
                'title' => 'Tugas Baru',
                'body' => sprintf(
                    'Tugas baru "%s" di kelas %s. Deadline: %s',
                    $assignment->title,
                    $course->code,
                    $assignment->due_date->locale('id')->translatedFormat('d M Y, H:i'),
                ),
                'data' => [
                    'type' => 'assignment_new',
                    'course_id' => (string) $course->id,
                    'assignment_id' => (string) $assignment->id,
                ],
            ],
        );
    }

    public static function pushSubmissionToLecturer(AssignmentSubmission $submission, bool $isUpdate = false): void
    {
        $submission->loadMissing(['student', 'assignment.course']);
        $assignment = $submission->assignment;
        $course = $assignment->course;
        $student = $submission->student;

        app(FcmService::class)->sendToTopic(
            FcmTopic::lecturer($course->id),
            [
                'title' => $isUpdate ? 'Pengumpulan Diperbarui' : 'Tugas Dikumpulkan',
                'body' => sprintf(
                    '%s %s tugas "%s" (%s)',
                    $student->name,
                    $isUpdate ? 'memperbarui pengumpulan' : 'mengumpulkan',
                    $assignment->title,
                    $course->code,
                ),
                'data' => [
                    'type' => 'assignment_submitted',
                    'course_id' => (string) $course->id,
                    'assignment_id' => (string) $assignment->id,
                    'submission_id' => (string) $submission->id,
                    'is_update' => $isUpdate ? '1' : '0',
                ],
            ],
        );
    }

    public static function pushDeadlineReminder(Assignment $assignment, int $hoursBefore): void
    {
        $assignment->loadMissing('course');
        $course = $assignment->course;
        $label = $hoursBefore >= 24
            ? round($hoursBefore / 24).' hari'
            : $hoursBefore.' jam';

        app(FcmService::class)->sendToTopic(
            FcmTopic::students($course->id),
            [
                'title' => 'Deadline Mendekat',
                'body' => sprintf(
                    'Tugas "%s" (%s) deadline tinggal %s lagi (%s)',
                    $assignment->title,
                    $course->code,
                    $label,
                    $assignment->due_date->locale('id')->translatedFormat('d M Y, H:i'),
                ),
                'data' => [
                    'type' => 'assignment_deadline',
                    'course_id' => (string) $course->id,
                    'assignment_id' => (string) $assignment->id,
                    'hours_before' => (string) $hoursBefore,
                ],
            ],
        );
    }
}
