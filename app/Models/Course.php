<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'code',
        'description',
        'weight_attendance',
        'weight_assignment',
        'weight_uts',
        'weight_uas',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
        ];
    }

    public function ownedBy(int|string|null $userId): bool
    {
        if ($userId === null) {
            return false;
        }

        return (int) $this->user_id === (int) $userId;
    }

    public function matchesId(int|string|null $id): bool
    {
        if ($id === null) {
            return false;
        }

        return (int) $this->id === (int) $id;
    }

    public function lecturer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function materials(): HasMany
    {
        return $this->hasMany(CourseMaterial::class)->orderBy('sort_order');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(Assignment::class)->orderBy('due_date');
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_enrollments')
            ->withPivot(['enrolled_at', 'attendance_score', 'uts_score', 'uas_score'])
            ->withTimestamps();
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }
}
