<?php

namespace App\Support;

use App\Models\Assignment;
use App\Models\User;
use App\Notifications\NewAssignmentNotification;
use Illuminate\Support\Collection;

class AssignmentNotifier
{
    public static function notifyStudentsAboutNewAssignment(Assignment $assignment): void
    {
        $assignment->loadMissing(['course.students']);

        /** @var Collection<int, User> $students */
        $students = $assignment->course->students;

        foreach ($students as $student) {
            $student->notify(new NewAssignmentNotification($assignment));
        }
    }
}
