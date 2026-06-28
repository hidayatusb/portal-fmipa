<?php

namespace App\Support;

use App\Enums\UserRole;
use App\Models\User;

class FcmTopic
{
    public static function students(int|string $courseId): string
    {
        return 'course_'.(int) $courseId.'_students';
    }

    public static function lecturer(int|string $courseId): string
    {
        return 'course_'.(int) $courseId.'_lecturer';
    }

    /**
     * @return list<string>
     */
    public static function forUser(User $user): array
    {
        $role = $user->resolvedRole();

        if ($role === UserRole::Dosen) {
            return $user->courses()
                ->pluck('id')
                ->map(fn ($id) => static::lecturer($id))
                ->values()
                ->all();
        }

        if ($role === UserRole::Mahasiswa) {
            return $user->enrolledCourses()
                ->pluck('courses.id')
                ->map(fn ($id) => static::students($id))
                ->values()
                ->all();
        }

        return [];
    }
}
