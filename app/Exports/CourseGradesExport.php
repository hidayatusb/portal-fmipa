<?php

namespace App\Exports;

use App\Services\CourseGradesReport;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class CourseGradesExport implements FromArray, ShouldAutoSize, WithHeadings, WithTitle
{
    public function __construct(private array $report) {}

    public function array(): array
    {
        return collect($this->report['rows'])->map(function (array $row) {
            $data = [
                $row['no'],
                $row['name'],
                $row['username'],
                $row['email'],
            ];

            foreach ($this->report['assignments'] as $assignment) {
                $data[] = CourseGradesReport::scoreLabel($row['scores'][$assignment->id] ?? null);
            }

            $data[] = CourseGradesReport::scoreLabel($row['attendance_score']);
            $data[] = CourseGradesReport::averageLabel($row['assignment_average']);
            $data[] = CourseGradesReport::scoreLabel($row['uts_score']);
            $data[] = CourseGradesReport::scoreLabel($row['uas_score']);
            $data[] = CourseGradesReport::averageLabel($row['final_grade']);

            return $data;
        })->all();
    }

    public function headings(): array
    {
        $headings = ['No', 'Nama Mahasiswa', 'Username', 'Email'];

        foreach ($this->report['assignments'] as $index => $assignment) {
            $headings[] = CourseGradesReport::assignmentHeading($index + 1);
        }

        $headings[] = 'Kehadiran';
        $headings[] = 'Rata-rata Tugas';
        $headings[] = 'UTS';
        $headings[] = 'UAS';
        $headings[] = 'Nilai Akhir';

        return $headings;
    }

    public function title(): string
    {
        return 'Nilai';
    }
}
