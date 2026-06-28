<?php

namespace App\Http\Controllers\Api\Mahasiswa;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\AssignmentResource;
use App\Http\Resources\SubmissionResource;
use App\Models\Assignment;
use App\Models\Course;
use App\Notifications\AssignmentSubmittedNotification;
use App\Support\CourseStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AssignmentController extends ApiController
{
    public function show(Course $course, Assignment $assignment): JsonResponse
    {
        $this->authorizeAssignment($course, $assignment);

        $submission = $assignment->submissionFor(Auth::id());

        return $this->success([
            'assignment' => AssignmentResource::make($assignment),
            'submission' => $submission ? SubmissionResource::make($submission) : null,
        ]);
    }

    public function submit(Request $request, Course $course, Assignment $assignment): JsonResponse
    {
        $this->authorizeAssignment($course, $assignment);

        if ($assignment->isClosedForSubmissions()) {
            return $this->error('Batas waktu tugas sudah berakhir. Pengumpulan tidak dapat dilakukan.', 422);
        }

        $submission = $assignment->submissionFor(Auth::id());
        $isUpdate = $submission !== null;
        $hasExistingFile = $submission?->hasFile() && ! $request->boolean('remove_file');

        $validated = $request->validate([
            'content' => ['nullable', 'string', 'max:5000'],
            'file' => ['nullable', 'file', 'max:10240'],
            'remove_file' => ['nullable', 'boolean'],
        ]);

        $willHaveFile = $request->hasFile('file') || $hasExistingFile;

        if (! $willHaveFile && blank($validated['content'] ?? null)) {
            return $this->error('Unggah file atau isi catatan jawaban.', 422, [
                'file' => ['Unggah file atau isi catatan jawaban.'],
            ]);
        }

        $filePath = $submission?->file_path;
        $fileName = $submission?->file_name;

        if ($request->hasFile('file')) {
            if ($filePath) {
                CourseStorage::delete($filePath);
            }

            $fileName = $request->file('file')->getClientOriginalName();
            $filePath = $request->file('file')->store(
                CourseStorage::submissionDirectory($assignment),
                CourseStorage::diskName()
            );
        } elseif ($request->boolean('remove_file') && $filePath) {
            CourseStorage::delete($filePath);
            $filePath = null;
            $fileName = null;
        }

        $data = [
            'content' => $validated['content'] ?? null,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'submitted_at' => now(),
        ];

        if ($submission) {
            $submission->update($data);
        } else {
            $submission = $assignment->submissions()->create([
                ...$data,
                'user_id' => Auth::id(),
            ]);
        }

        $submission->refresh()->loadMissing(['student', 'assignment.course']);
        $course->loadMissing('lecturer');

        if ($lecturer = $course->lecturer) {
            $lecturer->notify(new AssignmentSubmittedNotification(
                submission: $submission,
                isUpdate: $isUpdate,
            ));
        }

        return $this->success(
            SubmissionResource::make($submission),
            $isUpdate ? 'Jawaban tugas berhasil diperbarui.' : 'Jawaban tugas berhasil dikumpulkan.',
            $isUpdate ? 200 : 201,
        );
    }

    public function attachment(Course $course, Assignment $assignment): StreamedResponse|JsonResponse
    {
        $this->authorizeAssignment($course, $assignment);
        abort_unless($assignment->hasAttachment(), 404);

        return CourseStorage::diskForPath($assignment->attachment_path)->download($assignment->attachment_path, $assignment->attachment_name);
    }

    public function submissionFile(Course $course, Assignment $assignment, int $submission): StreamedResponse|JsonResponse
    {
        $this->authorizeAssignment($course, $assignment);

        $record = $assignment->submissions()
            ->whereKey($submission)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        abort_unless($record->hasFile(), 404);

        return CourseStorage::diskForPath($record->file_path)->download($record->file_path, $record->file_name);
    }

    protected function authorizeAssignment(Course $course, Assignment $assignment): void
    {
        abort_unless(
            Auth::user()->enrolledCourses()->whereKey($course->id)->exists(),
            403
        );
        abort_unless($assignment->belongsToCourse($course), 404);
    }
}
