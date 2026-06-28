<?php

namespace App\Support;

use App\Models\AssignmentSubmission;
use App\Notifications\AssignmentSubmittedNotification;
use App\Services\CoursePushNotifier;

class SubmissionPushNotifier
{
    public static function notifyLecturer(AssignmentSubmission $submission, bool $isUpdate = false): void
    {
        $submission->loadMissing(['assignment.course.lecturer']);

        $lecturer = $submission->assignment->course->lecturer;

        if ($lecturer) {
            $lecturer->notify(new AssignmentSubmittedNotification($submission, $isUpdate));
        }

        CoursePushNotifier::pushSubmissionToLecturer($submission, $isUpdate);
    }
}
