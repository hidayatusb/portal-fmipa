<?php

namespace App\Models;

use App\Support\CourseStorage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Assignment extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'due_date',
        'accept_late_submissions',
        'attachment_path',
        'attachment_name',
    ];

    protected function casts(): array
    {
        return [
            'course_id' => 'integer',
            'accept_late_submissions' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    protected function dueDate(): Attribute
    {
        $timezone = config('app.timezone', 'Asia/Makassar');

        return Attribute::make(
            get: fn (?string $value) => $value === null
                ? null
                : Carbon::parse($value, $timezone),
            set: fn (mixed $value) => $value === null
                ? null
                : Carbon::parse($value, $timezone)->format('Y-m-d H:i:s'),
        );
    }

    protected static function booted(): void
    {
        static::deleting(function (Assignment $assignment) {
            if ($assignment->attachment_path) {
                CourseStorage::delete($assignment->attachment_path);
            }
        });
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function belongsToCourse(Course|int|string|null $course): bool
    {
        $courseId = $course instanceof Course ? $course->id : $course;

        if ($courseId === null) {
            return false;
        }

        return (int) $this->course_id === (int) $courseId;
    }

    public function sequenceNumber(): int
    {
        $ids = static::query()
            ->where('course_id', $this->course_id)
            ->orderBy('due_date')
            ->orderBy('id')
            ->pluck('id');

        $index = $ids->search($this->id);

        return $index !== false ? $index + 1 : 1;
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function submissionFor(?int $userId = null): ?AssignmentSubmission
    {
        $userId ??= Auth::user()?->id;

        if (! $userId) {
            return null;
        }

        return $this->submissions()->where('user_id', $userId)->first();
    }

    public function isOverdue(): bool
    {
        return $this->due_date->isPast();
    }

    public function acceptsLateSubmissions(): bool
    {
        return (bool) $this->accept_late_submissions;
    }

    public function acceptsSubmissions(): bool
    {
        return ! $this->isOverdue() || $this->acceptsLateSubmissions();
    }

    public function isClosedForSubmissions(): bool
    {
        return $this->isOverdue() && ! $this->acceptsLateSubmissions();
    }

    public function lateSubmissionLabel(): string
    {
        return $this->acceptsLateSubmissions()
            ? 'Terbuka setelah deadline'
            : 'Tertutup setelah deadline';
    }

    public function isDueSoon(): bool
    {
        return ! $this->isOverdue() && now()->diffInDays($this->due_date, false) <= 2;
    }

    public function remainingLabel(): string
    {
        if ($this->isOverdue()) {
            return 'Batas waktu sudah berakhir';
        }

        $now = now();

        if ($now->diffInHours($this->due_date, false) < 24) {
            $hours = (int) $now->diffInHours($this->due_date, false);

            return $hours <= 1 ? 'Kurang dari 1 jam lagi' : "{$hours} jam lagi";
        }

        $days = (int) $now->diffInDays($this->due_date, false);

        return "{$days} hari lagi";
    }

    /**
     * @return array{days: int, hours: int, minutes: int}|null
     */
    public function countdownParts(): ?array
    {
        if ($this->isOverdue()) {
            return null;
        }

        $diff = now()->diff($this->due_date);

        return [
            'days' => (int) $diff->days,
            'hours' => (int) $diff->h,
            'minutes' => (int) $diff->i,
        ];
    }

    public function deadlineProgress(): int
    {
        $start = $this->created_at ?? now();
        $end = $this->due_date;

        if ($end->lte($start)) {
            return 100;
        }

        if (now()->gte($end)) {
            return 100;
        }

        $total = max(1, $start->diffInSeconds($end));
        $elapsed = $start->diffInSeconds(now());

        return min(100, max(0, (int) round(($elapsed / $total) * 100)));
    }

    public function deadlineTone(): string
    {
        if ($this->isOverdue()) {
            return 'overdue';
        }

        if ($this->isDueSoon()) {
            return 'urgent';
        }

        return 'active';
    }

    public function hasAttachment(): bool
    {
        return filled($this->attachment_path)
            && CourseStorage::exists($this->attachment_path);
    }

    public function isImageAttachment(): bool
    {
        if (! $this->hasAttachment()) {
            return false;
        }

        $extension = strtolower(pathinfo($this->attachment_path, PATHINFO_EXTENSION));

        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp'], true);
    }

    public function attachmentUrl(string $role = 'dosen'): ?string
    {
        if (! $this->hasAttachment()) {
            return null;
        }

        $route = $role === 'mahasiswa'
            ? 'mahasiswa.elearning.assignments.attachment.show'
            : 'dosen.elearning.assignments.attachment.show';

        return route($route, [
            'course' => $this->course_id,
            'assignment' => $this->id,
        ]);
    }

    public function attachmentDownloadUrl(string $role = 'dosen'): ?string
    {
        if (! $this->hasAttachment()) {
            return null;
        }

        $route = $role === 'mahasiswa'
            ? 'mahasiswa.elearning.assignments.attachment.download'
            : 'dosen.elearning.assignments.attachment.download';

        return route($route, [
            'course' => $this->course_id,
            'assignment' => $this->id,
        ]);
    }
}
