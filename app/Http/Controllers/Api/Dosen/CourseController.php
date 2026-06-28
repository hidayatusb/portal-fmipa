<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\MaterialResource;
use App\Http\Resources\UserResource;
use App\Models\Course;
use App\Services\CourseGradesReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $query = Course::query()
            ->where('user_id', Auth::id())
            ->withCount(['materials', 'students', 'assignments'])
            ->latest();

        if ($search = trim((string) $request->query('search'))) {
            $term = '%'.strtolower($search).'%';
            $query->where(function ($inner) use ($term) {
                $inner->whereRaw('LOWER(title) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(code) LIKE ?', [$term])
                    ->orWhereRaw('LOWER(description) LIKE ?', [$term]);
            });
        }

        $courses = $query->get();

        return $this->success(CourseResource::collection($courses));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:150'],
            'code' => ['required', 'string', 'min:2', 'max:20', 'unique:courses,code'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $course = Course::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
        ]);

        return $this->success(CourseResource::make($course), 'Mata kuliah berhasil ditambahkan.', 201);
    }

    public function show(Course $course): JsonResponse
    {
        $this->authorizeCourse($course);

        $course->load(['materials', 'assignments', 'students'])->loadCount(['materials', 'students', 'assignments']);

        return $this->success([
            'course' => CourseResource::make($course),
            'materials' => MaterialResource::collection($course->materials),
            'assignments' => AssignmentResource::collection($course->assignments),
            'students' => UserResource::collection($course->students),
        ]);
    }

    public function update(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'min:3', 'max:150'],
            'code' => ['sometimes', 'required', 'string', 'min:2', 'max:20', 'unique:courses,code,'.$course->id],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        if (isset($validated['code'])) {
            $validated['code'] = strtoupper($validated['code']);
        }

        $course->update($validated);

        return $this->success(CourseResource::make($course->fresh()), 'Mata kuliah berhasil diperbarui.');
    }

    public function grades(Course $course): JsonResponse
    {
        $this->authorizeCourse($course);

        return $this->success((new CourseGradesReport($course))->build());
    }

    protected function authorizeCourse(Course $course): void
    {
        abort_unless($course->ownedBy(Auth::id()), 403, 'Anda tidak memiliki akses ke mata kuliah ini.');
    }
}
