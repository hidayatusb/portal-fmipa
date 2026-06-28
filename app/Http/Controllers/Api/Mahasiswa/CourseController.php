<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\MaterialResource;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Support\CourseStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CourseController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $courses = Auth::user()
            ->enrolledCourses()
            ->with('lecturer')
            ->withCount(['materials', 'assignments'])
            ->orderByPivot('enrolled_at', 'desc')
            ->get();

        if ($search = trim((string) $request->query('search'))) {
            $term = strtolower($search);
            $courses = $courses->filter(function (Course $course) use ($term) {
                return str_contains(strtolower($course->title), $term)
                    || str_contains(strtolower($course->code), $term)
                    || str_contains(strtolower($course->description ?? ''), $term)
                    || str_contains(strtolower($course->lecturer->name ?? ''), $term);
            })->values();
        }

        return $this->success(CourseResource::collection($courses));
    }

    public function join(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'min:2', 'max:20'],
        ]);

        $course = Course::where('code', strtoupper(trim($validated['code'])))->first();

        if (! $course) {
            return $this->error('Kode kelas tidak ditemukan.', 422, [
                'code' => ['Kode kelas tidak ditemukan.'],
            ]);
        }

        if (Auth::user()->enrolledCourses()->whereKey($course->id)->exists()) {
            return $this->error('Anda sudah terdaftar di mata kuliah ini.', 422, [
                'code' => ['Anda sudah terdaftar di mata kuliah ini.'],
            ]);
        }

        Auth::user()->enrolledCourses()->attach($course->id, [
            'enrolled_at' => now(),
        ]);

        return $this->success(CourseResource::make($course->load('lecturer')), 'Berhasil gabung ke kelas.', 201);
    }

    public function show(Course $course): JsonResponse
    {
        $this->authorizeEnrollment($course);

        $course->load(['materials', 'assignments', 'lecturer'])->loadCount(['materials', 'assignments']);

        $assignments = $course->assignments->map(function ($assignment) {
            $submission = $assignment->submissionFor(Auth::id());

            return array_merge(
                AssignmentResource::make($assignment)->resolve(),
                ['my_submission' => $submission ? [
                    'id' => $submission->id,
                    'submitted_at' => $submission->submitted_at?->toIso8601String(),
                    'is_late' => $submission->isLate(),
                    'is_graded' => $submission->isGraded(),
                    'score' => $submission->score,
                ] : null]
            );
        });

        return $this->success([
            'course' => CourseResource::make($course),
            'materials' => MaterialResource::collection($course->materials),
            'assignments' => $assignments,
        ]);
    }

    public function materialFile(Course $course, CourseMaterial $material): StreamedResponse|JsonResponse
    {
        $this->authorizeEnrollment($course);
        abort_unless($material->course_id === $course->id, 404);
        abort_unless($material->hasFile(), 404);

        return CourseStorage::diskForPath($material->file_path)->download($material->file_path, $material->file_name);
    }

    protected function authorizeEnrollment(Course $course): void
    {
        abort_unless(
            Auth::user()->enrolledCourses()->whereKey($course->id)->exists(),
            403,
            'Anda tidak terdaftar di mata kuliah ini.'
        );
    }
}
