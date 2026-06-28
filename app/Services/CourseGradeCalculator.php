<?php

namespace App\Services;

use App\Models\Course;

class CourseGradeCalculator
{
    public static function assignmentAverage(array $scores): ?float
    {
        $gradedScores = array_values(array_filter($scores, fn (?int $score) => $score !== null));

        if ($gradedScores === []) {
            return null;
        }

        return round(array_sum($gradedScores) / count($gradedScores), 1);
    }

    public static function finalGrade(
        Course $course,
        ?int $attendanceScore,
        ?float $assignmentAverage,
        ?int $utsScore,
        ?int $uasScore
    ): ?float {
        $components = [
            ['weight' => $course->weight_attendance, 'score' => $attendanceScore],
            ['weight' => $course->weight_assignment, 'score' => $assignmentAverage],
            ['weight' => $course->weight_uts, 'score' => $utsScore],
            ['weight' => $course->weight_uas, 'score' => $uasScore],
        ];

        $weightedSum = 0;

        foreach ($components as $component) {
            if ($component['weight'] <= 0) {
                continue;
            }

            if ($component['score'] === null) {
                return null;
            }

            $weightedSum += $component['score'] * $component['weight'];
        }

        return round($weightedSum / 100, 1);
    }

    public static function weightsTotal(Course $course): int
    {
        return $course->weight_attendance
            + $course->weight_assignment
            + $course->weight_uts
            + $course->weight_uas;
    }
}
