<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Support\CourseStorage;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MaterialFileController extends Controller
{
    public function show(Course $course, CourseMaterial $material): StreamedResponse
    {
        $this->authorizeAccess($course, $material);

        return CourseStorage::diskForPath($material->file_path)->response(
            $material->file_path,
            $material->file_name ?? basename($material->file_path),
            ['Content-Disposition' => 'inline; filename="'.addslashes($material->file_name ?? 'materi').'"']
        );
    }

    public function download(Course $course, CourseMaterial $material): StreamedResponse
    {
        $this->authorizeAccess($course, $material);

        return CourseStorage::diskForPath($material->file_path)->download(
            $material->file_path,
            $material->file_name ?? basename($material->file_path)
        );
    }

    protected function authorizeAccess(Course $course, CourseMaterial $material): void
    {
        abort_unless($course->ownedBy(Auth::id()), 403);
        abort_unless($material->belongsToCourse($course), 404);
        abort_unless($material->hasFile(), 404);
    }
}
