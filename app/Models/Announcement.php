<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Announcement extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'body',
        'image_path',
        'is_published',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::deleting(function (Announcement $announcement) {
            if ($announcement->image_path) {
                Storage::disk('public')->delete($announcement->image_path);
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function hasImage(): bool
    {
        return filled($this->image_path)
            && Storage::disk('public')->exists($this->image_path);
    }

    public function imageUrl(): ?string
    {
        if (! $this->hasImage()) {
            return null;
        }

        return route('announcements.image', $this);
    }

    public function imageApiUrl(): ?string
    {
        if (! $this->hasImage()) {
            return null;
        }

        return route('api.announcements.image', [
            'announcement' => $this,
            'v' => $this->updated_at?->timestamp,
        ]);
    }
}
