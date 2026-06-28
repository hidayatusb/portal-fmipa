<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\SubmissionResource;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Support\CourseStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends ApiController
{
    public function show(Course $course, Assignment $assignment, AssignmentSubmission $submission): JsonResponse
    {
        $this->authorizeSubmission($course, $assignment, $submission);

        $submission->load(['student', 'assignment']);

        $submissions = $assignment->submissions()
            ->with('student')
            ->orderByDesc('submitted_at')
            ->get();

        $currentIndex = $submissions->search(fn (AssignmentSubmission $item) => $item->id === $submission->id);

        return $this->success([
            'submission' => SubmissionResource::make($submission),
            'navigation' => [
                'position' => $currentIndex !== false ? $currentIndex + 1 : null,
                'total' => $submissions->count(),
                'previous_submission_id' => $currentIndex !== false && $currentIndex > 0
                    ? $submissions[$currentIndex - 1]->id
                    : null,
                'next_submission_id' => $currentIndex !== false && $currentIndex < $submissions->count() - 1
                    ? $submissions[$currentIndex + 1]->id
                    : null,
            ],
        ]);
    }

    public function grade(Request $request, Course $course, Assignment $assignment, AssignmentSubmission $submission): JsonResponse
    {
        $this->authorizeSubmission($course, $assignment, $submission);

        $validated = $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string', 'max:2000'],
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'] ?? null,
            'feedback_at' => now(),
        ]);

        return $this->success(SubmissionResource::make($submission->fresh(['student', 'assignment'])), 'Penilaian berhasil disimpan.');
    }

    public function file(Course $course, Assignment $assignment, AssignmentSubmission $submission): StreamedResponse|JsonResponse
    {
        $this->authorizeSubmission($course, $assignment, $submission);
        abort_unless($submission->hasFile(), 404);

        return CourseStorage::diskForPath($submission->file_path)->download($submission->file_path, $submission->file_name);
    }

    protected function authorizeSubmission(Course $course, Assignment $assignment, AssignmentSubmission $submission): void
    {
        abort_unless($course->ownedBy(Auth::id()), 403);
        abort_unless($assignment->course_id === $course->id, 404);
        abort_unless($submission->assignment_id === $assignment->id, 404);
    }
}
