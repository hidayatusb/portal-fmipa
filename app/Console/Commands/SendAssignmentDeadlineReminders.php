<?php

namespace App\Console\Commands;

use App\Models\Assignment;
use App\Models\AssignmentDeadlineReminder;
use App\Models\User;
use App\Notifications\AssignmentDeadlineReminderNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendAssignmentDeadlineReminders extends Command
{
    protected $signature = 'assignments:send-deadline-reminders';

    protected $description = 'Kirim push/database reminder deadline tugas ke mahasiswa';

    public function handle(): int
    {
        $hoursList = config('fcm.deadline_reminder_hours', [24, 72]);

        if ($hoursList === []) {
            return self::SUCCESS;
        }

        $now = now();

        Assignment::query()
            ->with(['course.students'])
            ->where('due_date', '>', $now)
            ->chunkById(50, function ($assignments) use ($hoursList, $now) {
                foreach ($assignments as $assignment) {
                    foreach ($hoursList as $hoursBefore) {
                        $this->sendReminderForWindow($assignment, $hoursBefore, $now);
                    }
                }
            });

        return self::SUCCESS;
    }

    protected function sendReminderForWindow(Assignment $assignment, int $hoursBefore, Carbon $now): void
    {
        $dueDate = $assignment->due_date;
        $windowStart = $dueDate->copy()->subHours($hoursBefore);
        $windowEnd = $windowStart->copy()->addHour();

        if (! $now->greaterThanOrEqualTo($windowStart) || ! $now->lessThanOrEqualTo($windowEnd)) {
            return;
        }

        /** @var \Illuminate\Support\Collection<int, User> $students */
        $students = $assignment->course->students;

        foreach ($students as $student) {
            $alreadySent = AssignmentDeadlineReminder::query()
                ->where('assignment_id', $assignment->id)
                ->where('user_id', $student->id)
                ->where('hours_before', $hoursBefore)
                ->exists();

            if ($alreadySent) {
                continue;
            }

            $student->notify(new AssignmentDeadlineReminderNotification($assignment, $hoursBefore));

            AssignmentDeadlineReminder::query()->create([
                'assignment_id' => $assignment->id,
                'user_id' => $student->id,
                'hours_before' => $hoursBefore,
                'sent_at' => now(),
            ]);
        }
    }
}
