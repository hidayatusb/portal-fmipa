<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Assignment */
class AssignmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $role = $request->user()?->isMahasiswa() ? 'mahasiswa' : 'dosen';

        return [
            'id' => $this->id,
            'course_id' => $this->course_id,
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => $this->due_date?->toIso8601String(),
            'accept_late_submissions' => $this->acceptsLateSubmissions(),
            'is_overdue' => $this->isOverdue(),
            'is_closed_for_submissions' => $this->isClosedForSubmissions(),
            'deadline_tone' => $this->deadlineTone(),
            'remaining_label' => $this->remainingLabel(),
            'has_attachment' => $this->hasAttachment(),
            'attachment_name' => $this->attachment_name,
            'attachment_url' => $this->hasAttachment()
                ? url("/api/{$role}/courses/{$this->course_id}/assignments/{$this->id}/attachment")
                : null,
            'submissions_count' => $this->whenCounted('submissions'),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
