<?php

namespace App\Models;

use App\Support\CourseStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseMaterial extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'type',
        'content',
        'file_path',
        'file_name',
        'sort_order',
    ];

    protected static function booted(): void
    {
        static::deleting(function (CourseMaterial $material) {
            if ($material->file_path) {
                CourseStorage::delete($material->file_path);
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function hasFile(): bool
    {
        return filled($this->file_path)
            && CourseStorage::exists($this->file_path);
    }

    public function isImageFile(): bool
    {
        if (! $this->hasFile()) {
            return false;
        }

        $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    }

    public function fileUrl(string $role = 'dosen'): ?string
    {
        if (! $this->hasFile()) {
            return null;
        }

        $route = $role === 'mahasiswa'
            ? 'mahasiswa.elearning.materials.file.show'
            : 'dosen.elearning.materials.file.show';

        return route($route, [
            'course' => $this->course_id,
            'material' => $this->id,
        ]);
    }

    public function fileDownloadUrl(string $role = 'dosen'): ?string
    {
        if (! $this->hasFile()) {
            return null;
        }

        $route = $role === 'mahasiswa'
            ? 'mahasiswa.elearning.materials.file.download'
            : 'dosen.elearning.materials.file.download';

        return route($route, [
            'course' => $this->course_id,
            'material' => $this->id,
        ]);
    }
}
