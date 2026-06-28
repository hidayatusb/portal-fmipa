<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\CourseMaterial */
class MaterialResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $role = $request->user()?->isMahasiswa() ? 'mahasiswa' : 'dosen';

        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'content' => $this->content,
            'sort_order' => $this->sort_order,
            'has_file' => $this->hasFile(),
            'file_name' => $this->file_name,
            'file_url' => $this->hasFile() ? url("/api/{$role}/courses/{$this->course_id}/materials/{$this->id}/file") : null,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
