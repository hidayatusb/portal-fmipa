<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SubmissionController extends Controller
{
    public function show(Course $course, Assignment $assignment, AssignmentSubmission $submission): View
    {
        $this->authorizeSubmission($course, $assignment, $submission);

        $submission->load('student');

        $submissions = $assignment->submissions()
            ->with('student')
            ->orderByDesc('submitted_at')
            ->get();

        $currentIndex = $submissions->search(fn (AssignmentSubmission $s) => $s->id === $submission->id);

        return view('dosen.elearning.submission-show', [
            'course' => $course,
            'assignment' => $assignment,
            'submission' => $submission,
            'previousSubmission' => $currentIndex !== false && $currentIndex > 0
                ? $submissions[$currentIndex - 1]
                : null,
            'nextSubmission' => $currentIndex !== false && $currentIndex < $submissions->count() - 1
                ? $submissions[$currentIndex + 1]
                : null,
            'submissionPosition' => $currentIndex !== false ? $currentIndex + 1 : null,
            'submissionTotal' => $submissions->count(),
        ]);
    }

    public function updateGrade(
        Request $request,
        Course $course,
        Assignment $assignment,
        AssignmentSubmission $submission
    ): RedirectResponse {
        $this->authorizeSubmission($course, $assignment, $submission);

        $validated = $request->validate([
            'score' => ['required', 'integer', 'min:0', 'max:100'],
            'feedback' => ['nullable', 'string', 'max:2000'],
        ], [
            'score.required' => 'Skor wajib diisi.',
            'score.integer' => 'Skor harus berupa angka.',
            'score.min' => 'Skor minimal 0.',
            'score.max' => 'Skor maksimal 100.',
            'feedback.max' => 'Feedback maksimal 2000 karakter.',
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'] ?? null,
            'feedback_at' => now(),
        ]);

        return redirect()
            ->route('dosen.elearning.submissions.show', [$course, $assignment, $submission])
            ->with('success', 'Penilaian berhasil disimpan.');
    }

    protected function authorizeSubmission(
        Course $course,
        Assignment $assignment,
        AssignmentSubmission $submission
    ): void {
        abort_unless($course->user_id === Auth::id(), 403);
        abort_unless($assignment->course_id === $course->id, 404);
        abort_unless($submission->assignment_id === $assignment->id, 404);
    }
}
