<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Course;
use App\Support\CourseStorage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssignmentAttachmentController extends Controller
{
    public function show(Course $course, Assignment $assignment): StreamedResponse
    {
        $this->authorizeAccess($course, $assignment);

        return CourseStorage::diskForPath($assignment->attachment_path)->response(
            $assignment->attachment_path,
            $assignment->attachment_name ?? basename($assignment->attachment_path),
            ['Content-Disposition' => 'inline; filename="'.addslashes($assignment->attachment_name ?? 'lampiran').'"']
        );
    }

    public function download(Course $course, Assignment $assignment): StreamedResponse
    {
        $this->authorizeAccess($course, $assignment);

        return CourseStorage::diskForPath($assignment->attachment_path)->download(
            $assignment->attachment_path,
            $assignment->attachment_name ?? basename($assignment->attachment_path)
        );
    }

    protected function authorizeAccess(Course $course, Assignment $assignment): void
    {
        abort_unless($assignment->belongsToCourse($course), 404);
        abort_unless($assignment->hasAttachment(), 404);
        abort_unless(
            Auth::user()->enrolledCourses()->whereKey($course->id)->exists(),
            403
        );
    }
}
