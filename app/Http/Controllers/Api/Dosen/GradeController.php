<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserResource;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends ApiController
{
    public function show(Course $course): JsonResponse
    {
        $this->authorizeCourse($course);

        $course->load('students');

        return $this->success([
            'weights' => [
                'attendance' => $course->weight_attendance,
                'assignment' => $course->weight_assignment,
                'uts' => $course->weight_uts,
                'uas' => $course->weight_uas,
            ],
            'students' => $course->students->sortBy('username')->values()->map(fn ($student) => [
                'user' => UserResource::make($student),
                'attendance_score' => $student->pivot->attendance_score,
                'uts_score' => $student->pivot->uts_score,
                'uas_score' => $student->pivot->uas_score,
            ]),
        ]);
    }

    public function updateWeights(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'weight_attendance' => ['required', 'integer', 'min:0', 'max:100'],
            'weight_assignment' => ['required', 'integer', 'min:0', 'max:100'],
            'weight_uts' => ['required', 'integer', 'min:0', 'max:100'],
            'weight_uas' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $total = $validated['weight_attendance'] + $validated['weight_assignment'] + $validated['weight_uts'] + $validated['weight_uas'];

        if ($total !== 100) {
            return $this->error("Total bobot saat ini {$total}%. Total harus sama dengan 100%.");
        }

        $course->update([
            'weight_attendance' => $validated['weight_attendance'],
            'weight_assignment' => $validated['weight_assignment'],
            'weight_uts' => $validated['weight_uts'],
            'weight_uas' => $validated['weight_uas'],
        ]);

        return $this->success(message: 'Pengaturan bobot nilai berhasil disimpan.');
    }

    public function updateStudentScores(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'grades' => ['required', 'array'],
            'grades.*.user_id' => ['required', 'integer', 'exists:users,id'],
            'grades.*.attendance_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'grades.*.uts_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'grades.*.uas_score' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        foreach ($validated['grades'] as $grade) {
            CourseEnrollment::query()
                ->where('course_id', $course->id)
                ->where('user_id', $grade['user_id'])
                ->update([
                    'attendance_score' => $grade['attendance_score'] ?? null,
                    'uts_score' => $grade['uts_score'] ?? null,
                    'uas_score' => $grade['uas_score'] ?? null,
                ]);
        }

        return $this->success(message: 'Nilai mahasiswa berhasil disimpan.');
    }

    protected function authorizeCourse(Course $course): void
    {
        abort_unless($course->ownedBy(Auth::id()), 403);
    }
}
