<?php

namespace App\Http\Controllers\Dosen;

use App\Exports\CourseGradesExport;
use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\CourseGradesReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CourseGradesExportController extends Controller
{
    public function excel(Course $course): BinaryFileResponse
    {
        $this->authorizeCourse($course);

        $report = (new CourseGradesReport($course))->build();

        return Excel::download(
            new CourseGradesExport($report),
            $this->filename($course, 'xlsx')
        );
    }

    public function pdf(Course $course)
    {
        $this->authorizeCourse($course);

        $report = (new CourseGradesReport($course))->build();
        $assignmentCount = $report['assignments']->count();

        $paper = match (true) {
            $assignmentCount > 14 => 'a1',
            $assignmentCount > 9 => 'a2',
            $assignmentCount > 4 => 'a3',
            default => 'a4',
        };

        return Pdf::loadView('dosen.elearning.grades-export-pdf', $report)
            ->setPaper($paper, 'landscape')
            ->download($this->filename($course, 'pdf'));
    }

    protected function authorizeCourse(Course $course): void
    {
        abort_unless($course->ownedBy(Auth::id()), 403);
    }

    protected function filename(Course $course, string $extension): string
    {
        return 'nilai-'.strtolower($course->code).'-'.now()->format('Ymd-His').'.'.$extension;
    }
}
