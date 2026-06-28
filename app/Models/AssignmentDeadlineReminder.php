<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentDeadlineReminder extends Model
{
    protected $fillable = [
        'assignment_id',
        'user_id',
        'hours_before',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'assignment_id' => 'integer',
            'user_id' => 'integer',
            'hours_before' => 'integer',
            'sent_at' => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
