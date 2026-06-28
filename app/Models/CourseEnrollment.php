<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseEnrollment extends Model
{
    protected $fillable = [
        'course_id',
        'user_id',
        'enrolled_at',
        'attendance_score',
        'uts_score',
        'uas_score',
    ];

    protected function casts(): array
    {
        return [
            'enrolled_at' => 'datetime',
            'attendance_score' => 'integer',
            'uts_score' => 'integer',
            'uas_score' => 'integer',
        ];
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
