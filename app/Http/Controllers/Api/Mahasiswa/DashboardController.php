<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Api\ApiController;
use App\Models\Course;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        $courses = Auth::user()
            ->enrolledCourses()
            ->with('lecturer')
            ->withCount(['materials', 'assignments'])
            ->orderByPivot('enrolled_at', 'desc')
            ->limit(5)
            ->get();

        $allCourses = Auth::user()->enrolledCourses()->withCount(['materials', 'assignments'])->get();

        return $this->success([
            'stats' => [
                'courses' => $allCourses->count(),
                'materials' => $allCourses->sum('materials_count'),
                'assignments' => $allCourses->sum('assignments_count'),
            ],
            'recent_courses' => $courses->map(fn (Course $course) => [
                'id' => $course->id,
                'title' => $course->title,
                'code' => $course->code,
                'lecturer_name' => $course->lecturer?->name,
                'materials_count' => $course->materials_count,
                'assignments_count' => $course->assignments_count,
            ]),
        ]);
    }
}
