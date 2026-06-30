<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

/**
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $unreadNotifications
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $readNotifications
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'profile_picture',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function hasRole(UserRole $role): bool
    {
        return $this->resolvedRole() === $role;
    }

    public function hasAnyRole(UserRole ...$roles): bool
    {
        $currentRole = $this->resolvedRole();

        return $currentRole !== null && in_array($currentRole, $roles, true);
    }

    public function resolvedRole(): ?UserRole
    {
        $role = $this->role;

        if ($role instanceof UserRole) {
            return $role;
        }

        return UserRole::tryFrom((string) $role);
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(UserRole::Admin);
    }

    public function isDosen(): bool
    {
        return $this->hasRole(UserRole::Dosen);
    }

    public function isMahasiswa(): bool
    {
        return $this->hasRole(UserRole::Mahasiswa);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function enrolledCourses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_enrollments')
            ->withPivot(['enrolled_at', 'attendance_score', 'uts_score', 'uas_score'])
            ->withTimestamps();
    }

    public function assignmentSubmissions(): HasMany
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    public function hasProfilePicture(): bool
    {
        return filled($this->profile_picture)
            && Storage::disk('public')->exists($this->profile_picture);
    }

    public function profilePictureUrl(): string
    {
        if ($this->hasProfilePicture()) {
            return route('profile.picture', ['v' => $this->updated_at?->timestamp]);
        }

        return $this->defaultProfilePictureUrl();
    }

    public function profilePictureApiUrl(): string
    {
        if ($this->hasProfilePicture()) {
            return route('api.profile.picture', ['v' => $this->updated_at?->timestamp]);
        }

        return $this->defaultProfilePictureUrl();
    }

    public function defaultProfilePictureUrl(): string
    {
        return asset('assets/media/avatars/300-2.png');
    }

    public function initials(): string
    {
        return strtoupper(substr($this->name, 0, 1));
    }
}
