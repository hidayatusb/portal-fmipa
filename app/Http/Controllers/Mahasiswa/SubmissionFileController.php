<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Support\CourseStorage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionFileController extends Controller
{
    public function show(Course $course, Assignment $assignment, AssignmentSubmission $submission): StreamedResponse
    {
        $this->authorizeAccess($course, $assignment, $submission);

        return CourseStorage::diskForPath($submission->file_path)->response(
            $submission->file_path,
            $submission->file_name ?? basename($submission->file_path),
            ['Content-Disposition' => 'inline; filename="'.addslashes($submission->file_name ?? 'jawaban').'"']
        );
    }

    public function download(Course $course, Assignment $assignment, AssignmentSubmission $submission): StreamedResponse
    {
        $this->authorizeAccess($course, $assignment, $submission);

        return CourseStorage::diskForPath($submission->file_path)->download(
            $submission->file_path,
            $submission->file_name ?? basename($submission->file_path)
        );
    }

    protected function authorizeAccess(Course $course, Assignment $assignment, AssignmentSubmission $submission): void
    {
        abort_unless($assignment->course_id === $course->id, 404);
        abort_unless($submission->assignment_id === $assignment->id, 404);
        abort_unless($submission->hasFile(), 404);
        abort_unless(
            $submission->user_id === Auth::id()
            && Auth::user()->enrolledCourses()->whereKey($course->id)->exists(),
            403
        );
    }
}
