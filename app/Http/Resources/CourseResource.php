<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\Course */
class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'code' => $this->code,
            'description' => $this->description,
            'materials_count' => $this->whenCounted('materials'),
            'students_count' => $this->whenCounted('students'),
            'assignments_count' => $this->whenCounted('assignments'),
            'lecturer' => UserResource::make($this->whenLoaded('lecturer')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
