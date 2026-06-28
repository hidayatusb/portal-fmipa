<?php

namespace App\Services;

use App\Models\Course;
use Illuminate\Support\Carbon;

class CourseGradesReport
{
    public function __construct(private Course $course) {}

    public function build(): array
    {
        $course = $this->course->load([
            'lecturer',
            'students',
            'assignments' => fn ($query) => $query->orderBy('due_date'),
            'assignments.submissions',
        ]);

        $assignments = $course->assignments;
        $students = $course->students->sortBy('username')->values();

        $rows = $students->map(function ($student, int $index) use ($assignments, $course) {
            $scores = [];
            $gradedScores = [];

            foreach ($assignments as $assignment) {
                $submission = $assignment->submissions->firstWhere('user_id', $student->id);
                $score = $submission?->score;
                $scores[$assignment->id] = $score;

                if ($score !== null) {
                    $gradedScores[] = $score;
                }
            }

            $assignmentAverage = CourseGradeCalculator::assignmentAverage($scores);
            $attendanceScore = $student->pivot->attendance_score;
            $utsScore = $student->pivot->uts_score;
            $uasScore = $student->pivot->uas_score;

            return [
                'no' => $index + 1,
                'name' => $student->name,
                'username' => $student->username,
                'email' => $student->email,
                'scores' => $scores,
                'assignment_average' => $assignmentAverage,
                'attendance_score' => $attendanceScore,
                'uts_score' => $utsScore,
                'uas_score' => $uasScore,
                'final_grade' => CourseGradeCalculator::finalGrade(
                    $course,
                    $attendanceScore,
                    $assignmentAverage,
                    $utsScore,
                    $uasScore
                ),
            ];
        })->all();

        return [
            'course' => $course,
            'assignments' => $assignments,
            'rows' => $rows,
            'generatedAt' => Carbon::now(),
        ];
    }

    public static function scoreLabel(?int $score): string
    {
        return $score !== null ? (string) $score : '-';
    }

    public static function averageLabel(?float $average): string
    {
        return $average !== null ? (string) $average : '-';
    }

    public static function assignmentHeading(int $number): string
    {
        return 'Tugas '.$number;
    }
}
