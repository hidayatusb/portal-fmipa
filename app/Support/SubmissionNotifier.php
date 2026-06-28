<?php

namespace App\Support;

use App\Models\AssignmentSubmission;
use App\Notifications\AssignmentGradedNotification;

class SubmissionNotifier
{
    public static function notifyStudentAboutGrade(AssignmentSubmission $submission): void
    {
        $submission->loadMissing('student');

        if ($submission->student) {
            $submission->student->notify(new AssignmentGradedNotification($submission));
        }
    }
}
