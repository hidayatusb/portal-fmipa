<?php

namespace App\Models;

use App\Support\CourseStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'assignment_id',
        'user_id',
        'content',
        'file_path',
        'file_name',
        'submitted_at',
        'score',
        'feedback',
        'feedback_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'score' => 'integer',
            'feedback_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (AssignmentSubmission $submission) {
            if ($submission->file_path) {
                CourseStorage::delete($submission->file_path);
            }
        });
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function hasFile(): bool
    {
        return filled($this->file_path)
            && CourseStorage::exists($this->file_path);
    }

    public function hasFeedback(): bool
    {
        return filled($this->feedback);
    }

    public function isGraded(): bool
    {
        return $this->score !== null;
    }

    public function isLate(): bool
    {
        if ($this->submitted_at === null) {
            return false;
        }

        $this->loadMissing('assignment');

        return $this->submitted_at->gt($this->assignment->due_date);
    }

    public function scoreTone(): string
    {
        if ($this->score === null) {
            return 'muted';
        }

        if ($this->score >= 80) {
            return 'success';
        }

        if ($this->score >= 60) {
            return 'warning';
        }

        return 'danger';
    }

    public function isImageFile(): bool
    {
        if (! $this->hasFile()) {
            return false;
        }

        $extension = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    }

    public function isPdfFile(): bool
    {
        if (! $this->hasFile()) {
            return false;
        }

        return strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION)) === 'pdf';
    }

    public function viewUrl(string $role = 'dosen'): string
    {
        $route = $role === 'dosen'
            ? 'dosen.elearning.submissions.show'
            : 'mahasiswa.elearning.submissions.show';

        return route($route, [
            'course' => $this->assignment->course_id,
            'assignment' => $this->assignment_id,
            'submission' => $this->id,
        ]);
    }

    public function fileUrl(string $role = 'mahasiswa'): ?string
    {
        if (! $this->hasFile()) {
            return null;
        }

        $route = $role === 'dosen'
            ? 'dosen.elearning.submissions.file.show'
            : 'mahasiswa.elearning.submissions.file.show';

        return route($route, [
            'course' => $this->assignment->course_id,
            'assignment' => $this->assignment_id,
            'submission' => $this->id,
        ]);
    }

    public function fileDownloadUrl(string $role = 'mahasiswa'): ?string
    {
        if (! $this->hasFile()) {
            return null;
        }

        $route = $role === 'dosen'
            ? 'dosen.elearning.submissions.file.download'
            : 'mahasiswa.elearning.submissions.file.download';

        return route($route, [
            'course' => $this->assignment->course_id,
            'assignment' => $this->assignment_id,
            'submission' => $this->id,
        ]);
    }
}
