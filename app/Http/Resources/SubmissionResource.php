<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\AssignmentSubmission */
class SubmissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $role = $request->user()?->isMahasiswa() ? 'mahasiswa' : 'dosen';
        $assignment = $this->assignment;

        return [
            'id' => $this->id,
            'assignment_id' => $this->assignment_id,
            'user_id' => $this->user_id,
            'content' => $this->content,
            'has_file' => $this->hasFile(),
            'file_name' => $this->file_name,
            'file_url' => $this->hasFile() && $assignment
                ? url("/api/{$role}/courses/{$assignment->course_id}/assignments/{$assignment->id}/submissions/{$this->id}/file")
                : null,
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'is_late' => $this->isLate(),
            'score' => $this->score,
            'feedback' => $this->feedback,
            'feedback_at' => $this->feedback_at?->toIso8601String(),
            'is_graded' => $this->isGraded(),
            'student' => UserResource::make($this->whenLoaded('student')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
