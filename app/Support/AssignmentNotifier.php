<?php

namespace App\Support;

use App\Models\Assignment;
use App\Models\User;
use App\Notifications\NewAssignmentNotification;
use App\Services\CoursePushNotifier;

class AssignmentNotifier
{
    public static function notifyStudentsAboutNewAssignment(Assignment $assignment): void
    {
        $assignment->loadMissing(['course.students']);

        foreach ($assignment->course->students as $student) {
            /** @var User $student */
            $student->notify(new NewAssignmentNotification($assignment));
        }

        CoursePushNotifier::pushNewAssignment($assignment);
    }
}
