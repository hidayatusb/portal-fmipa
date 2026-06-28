<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Api\ApiController;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = Auth::user();

        $courses = Course::query()
            ->where('user_id', $user->id)
            ->withCount(['materials', 'students'])
            ->latest()
            ->limit(5)
            ->get();

        $allCourses = Course::query()
            ->where('user_id', $user->id)
            ->withCount(['materials', 'students'])
            ->get();

        return $this->success([
            'stats' => [
                'courses' => $allCourses->count(),
                'students' => $allCourses->sum('students_count'),
                'materials' => $allCourses->sum('materials_count'),
            ],
            'recent_courses' => $courses->map(fn (Course $course) => [
                'id' => $course->id,
                'title' => $course->title,
                'code' => $course->code,
                'students_count' => $course->students_count,
                'materials_count' => $course->materials_count,
            ]),
        ]);
    }
}
