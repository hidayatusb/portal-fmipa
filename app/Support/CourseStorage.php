<?php

namespace App\Support;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\User;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CourseStorage
{
    public static function diskName(): string
    {
        return (string) config('filesystems.course_disk', 'google');
    }

    public static function disk(): Filesystem
    {
        return Storage::disk(static::diskName());
    }

    public static function sanitizeSegment(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/[\\\\\\/:*?"<>|]/', '-', $value) ?? $value;
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return $value !== '' ? $value : 'unknown';
    }

    public static function lecturerDirectory(User $lecturer): string
    {
        return static::sanitizeSegment($lecturer->id.' - '.$lecturer->name);
    }

    public static function courseDirectory(Course $course): string
    {
        $course->loadMissing('lecturer');

        return static::lecturerDirectory($course->lecturer).'/'.static::sanitizeSegment($course->code.' - '.$course->title);
    }

    public static function materialsDirectory(Course $course): string
    {
        return static::courseDirectory($course).'/materi';
    }

    public static function assignmentDirectory(Assignment $assignment): string
    {
        $assignment->loadMissing('course.lecturer');

        return static::courseDirectory($assignment->course).'/tugas '.$assignment->sequenceNumber();
    }

    public static function submissionDirectory(Assignment $assignment): string
    {
        $assignment->loadMissing('course.lecturer');

        return static::courseDirectory($assignment->course).'/jawaban '.$assignment->sequenceNumber();
    }

    public static function exists(?string $path): bool
    {
        if (blank($path)) {
            return false;
        }

        $disk = static::diskName();

        try {
            if (Storage::disk($disk)->exists($path)) {
                return true;
            }
        } catch (Throwable $e) {
            static::logStorageFailure('exists', $disk, $path, $e);
        }

        if ($disk !== 'public') {
            try {
                if (Storage::disk('public')->exists($path)) {
                    return true;
                }
            } catch (Throwable $e) {
                static::logStorageFailure('exists', 'public', $path, $e);
            }
        }

        return false;
    }

    public static function delete(?string $path): void
    {
        if (blank($path)) {
            return;
        }

        $disk = static::diskName();

        try {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        } catch (Throwable $e) {
            static::logStorageFailure('delete', $disk, $path, $e);
        }

        if ($disk !== 'public') {
            try {
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            } catch (Throwable $e) {
                static::logStorageFailure('delete', 'public', $path, $e);
            }
        }
    }

    public static function diskForPath(?string $path): FilesystemAdapter
    {
        if (blank($path)) {
            /** @var FilesystemAdapter $disk */
            $disk = static::disk();

            return $disk;
        }

        $disk = static::diskName();

        try {
            if (Storage::disk($disk)->exists($path)) {
                /** @var FilesystemAdapter $filesystem */
                $filesystem = Storage::disk($disk);

                return $filesystem;
            }
        } catch (Throwable $e) {
            static::logStorageFailure('diskForPath', $disk, $path, $e);
        }

        if ($disk !== 'public') {
            try {
                if (Storage::disk('public')->exists($path)) {
                    /** @var FilesystemAdapter $filesystem */
                    $filesystem = Storage::disk('public');

                    return $filesystem;
                }
            } catch (Throwable $e) {
                static::logStorageFailure('diskForPath', 'public', $path, $e);
            }
        }

        /** @var FilesystemAdapter $filesystem */
        $filesystem = Storage::disk($disk);

        return $filesystem;
    }

    protected static function logStorageFailure(string $operation, string $disk, string $path, Throwable $e): void
    {
        Log::warning('CourseStorage: gagal mengakses penyimpanan file.', [
            'operation' => $operation,
            'disk' => $disk,
            'path' => $path,
            'message' => $e->getMessage(),
        ]);
    }
}
