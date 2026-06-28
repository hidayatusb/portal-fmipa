<?php

namespace App\Http\Controllers\Api\Dosen;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\SubmissionResource;
use App\Models\Assignment;
use App\Models\Course;
use App\Support\CourseStorage;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssignmentController extends ApiController
{
    public function store(Request $request, Course $course): JsonResponse
    {
        $this->authorizeCourse($course);

        $validated = $request->validate([
            'title' => ['required', 'string', 'min:3', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'due_date' => ['required', 'date', 'after:now'],
            'accept_late_submissions' => ['nullable', 'boolean'],
            'attachment' => ['nullable', 'file', 'max:10240'],
        ]);

        $assignment = $course->assignments()->create([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'due_date' => Carbon::parse($validated['due_date'], config('app.timezone')),
            'accept_late_submissions' => $request->boolean('accept_late_submissions'),
        ]);

        if ($request->hasFile('attachment')) {
            $assignment->update([
                'attachment_name' => $request->file('attachment')->getClientOriginalName(),
                'attachment_path' => $request->file('attachment')->store(
                    CourseStorage::assignmentDirectory($assignment),
                    CourseStorage::diskName()
                ),
            ]);
        }

        return $this->success(AssignmentResource::make($assignment), 'Tugas berhasil ditambahkan.', 201);
    }

    public function show(Course $course, Assignment $assignment): JsonResponse
    {
        $this->authorizeAssignment($course, $assignment);

        $assignment->load(['submissions.student'])->loadCount('submissions');

        return $this->success([
            'assignment' => AssignmentResource::make($assignment),
            'submissions' => SubmissionResource::collection($assignment->submissions->sortByDesc('submitted_at')->values()),
        ]);
    }

    public function update(Request $request, Course $course, Assignment $assignment): JsonResponse
    {
        $this->authorizeAssignment($course, $assignment);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'min:3', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'due_date' => ['sometimes', 'required', 'date'],
            'accept_late_submissions' => ['nullable', 'boolean'],
            'attachment' => ['nullable', 'file', 'max:10240'],
            'remove_attachment' => ['nullable', 'boolean'],
        ]);

        $data = collect($validated)->only(['title', 'description'])->all();

        if (isset($validated['due_date'])) {
            $data['due_date'] = Carbon::parse($validated['due_date'], config('app.timezone'));
        }

        if ($request->has('accept_late_submissions')) {
            $data['accept_late_submissions'] = $request->boolean('accept_late_submissions');
        }

        if ($request->hasFile('attachment')) {
            if ($assignment->attachment_path) {
                CourseStorage::delete($assignment->attachment_path);
            }

            $data['attachment_name'] = $request->file('attachment')->getClientOriginalName();
            $data['attachment_path'] = $request->file('attachment')->store(
                CourseStorage::assignmentDirectory($assignment),
                CourseStorage::diskName()
            );
        } elseif ($request->boolean('remove_attachment') && $assignment->attachment_path) {
            CourseStorage::delete($assignment->attachment_path);
            $data['attachment_path'] = null;
            $data['attachment_name'] = null;
        }

        $assignment->update($data);

        return $this->success(AssignmentResource::make($assignment->fresh()), 'Tugas berhasil diperbarui.');
    }

    public function destroy(Course $course, Assignment $assignment): JsonResponse
    {
        $this->authorizeAssignment($course, $assignment);

        Assignment::destroy($assignment->getKey());

        return $this->success(message: 'Tugas berhasil dihapus.');
    }

    public function attachment(Course $course, Assignment $assignment): StreamedResponse|JsonResponse
    {
        $this->authorizeAssignment($course, $assignment);
        abort_unless($assignment->hasAttachment(), 404);

        return CourseStorage::diskForPath($assignment->attachment_path)->download($assignment->attachment_path, $assignment->attachment_name);
    }

    protected function authorizeCourse(Course $course): void
    {
        abort_unless($course->user_id === Auth::id(), 403);
    }

    protected function authorizeAssignment(Course $course, Assignment $assignment): void
    {
        $this->authorizeCourse($course);
        abort_unless($assignment->course_id === $course->id, 404);
    }
}
