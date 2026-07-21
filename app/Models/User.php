<?php

namespace App\Models;

use App\Enums\UserApprovalStatus;
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
     * @var array<string, mixed>
     */
    protected $attributes = [
        'approval_status' => UserApprovalStatus::Approved,
    ];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'approval_status',
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
            'approval_status' => UserApprovalStatus::class,
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

    public function resolvedApprovalStatus(): ?UserApprovalStatus
    {
        $status = $this->approval_status;

        if ($status instanceof UserApprovalStatus) {
            return $status;
        }

        return UserApprovalStatus::tryFrom((string) $status);
    }

    public function isPendingApproval(): bool
    {
        return $this->resolvedApprovalStatus() === UserApprovalStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->resolvedApprovalStatus() === UserApprovalStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->resolvedApprovalStatus() === UserApprovalStatus::Rejected;
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @param  list<UserRole|string>  $roles
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeWhereRoleIn($query, array $roles)
    {
        $values = array_map(
            fn (UserRole|string $role) => $role instanceof UserRole ? $role->value : $role,
            $roles,
        );

        return $query->whereIn('role', $values, 'and', false);
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<self>  $query
     * @return \Illuminate\Database\Eloquent\Builder<self>
     */
    public function scopeWhereApprovalStatus($query, UserApprovalStatus|string $status)
    {
        $value = $status instanceof UserApprovalStatus ? $status->value : $status;

        return $query->where('approval_status', '=', $value, 'and');
    }

    public function approvalStatusMessage(): string
    {
        return match ($this->resolvedApprovalStatus()) {
            UserApprovalStatus::Pending => 'Akun Anda masih menunggu persetujuan admin.',
            UserApprovalStatus::Rejected => 'Akun Anda ditolak. Hubungi administrator untuk informasi lebih lanjut.',
            default => 'Akun Anda tidak dapat mengakses sistem.',
        };
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

    /**
     * Hapus akun beserta data terkait (kelas, pengumpulan, token, notifikasi, foto).
     */
    public function deleteAccount(): void
    {
        $this->loadMissing(['courses', 'assignmentSubmissions']);

        foreach ($this->courses as $course) {
            $course->delete();
        }

        $this->assignmentSubmissions->each->delete();

        if ($this->profile_picture) {
            Storage::disk('public')->delete($this->profile_picture);
        }

        $this->notifications()->delete();
        $this->tokens()->delete();
        $this->deviceTokens()->delete();

        $this->delete();
    }
}
