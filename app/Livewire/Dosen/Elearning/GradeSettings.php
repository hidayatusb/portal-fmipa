<?php

namespace App\Livewire\Dosen\Elearning;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.demo1.base')]
class GradeSettings extends Component
{
    use SetsBreadcrumbs;

    public Course $course;

    public int $weightAttendance = 10;

    public int $weightAssignment = 30;

    public int $weightUts = 30;

    public int $weightUas = 30;

    /** @var array<int, array{attendance_score: ?string, uts_score: ?string, uas_score: ?string}> */
    public array $studentGrades = [];

    public function mount(Course $course): void
    {
        abort_unless($course->user_id === Auth::id(), 403);

        $this->course = $course->load('students');
        $this->fillGradeSettings();
        $this->fillStudentGrades();

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'E-Learning', 'url' => route('dosen.elearning.index')],
            ['label' => $course->code, 'url' => route('dosen.elearning.show', $course)],
            ['label' => 'Pengaturan Nilai'],
        ]);
    }

    protected function fillGradeSettings(): void
    {
        $this->weightAttendance = $this->course->weight_attendance;
        $this->weightAssignment = $this->course->weight_assignment;
        $this->weightUts = $this->course->weight_uts;
        $this->weightUas = $this->course->weight_uas;
    }

    protected function fillStudentGrades(): void
    {
        $this->studentGrades = $this->course->students
            ->sortBy('username')
            ->mapWithKeys(fn ($student) => [
                $student->id => [
                    'attendance_score' => $student->pivot->attendance_score !== null
                        ? (string) $student->pivot->attendance_score
                        : '',
                    'uts_score' => $student->pivot->uts_score !== null
                        ? (string) $student->pivot->uts_score
                        : '',
                    'uas_score' => $student->pivot->uas_score !== null
                        ? (string) $student->pivot->uas_score
                        : '',
                ],
            ])
            ->all();
    }

    public function saveGradeSettings(): void
    {
        $this->validate([
            'weightAttendance' => ['required', 'integer', 'min:0', 'max:100'],
            'weightAssignment' => ['required', 'integer', 'min:0', 'max:100'],
            'weightUts' => ['required', 'integer', 'min:0', 'max:100'],
            'weightUas' => ['required', 'integer', 'min:0', 'max:100'],
        ], [
            'weightAttendance.required' => 'Bobot kehadiran wajib diisi.',
            'weightAssignment.required' => 'Bobot tugas wajib diisi.',
            'weightUts.required' => 'Bobot UTS wajib diisi.',
            'weightUas.required' => 'Bobot UAS wajib diisi.',
        ]);

        $weightTotal = $this->weightAttendance + $this->weightAssignment + $this->weightUts + $this->weightUas;

        if ($weightTotal !== 100) {
            $this->addError('weightAttendance', "Total bobot saat ini {$weightTotal}%. Total harus sama dengan 100%.");

            return;
        }

        $this->course->update([
            'weight_attendance' => $this->weightAttendance,
            'weight_assignment' => $this->weightAssignment,
            'weight_uts' => $this->weightUts,
            'weight_uas' => $this->weightUas,
        ]);

        $this->course->refresh();
        $this->resetValidation();
        session()->flash('success', 'Pengaturan bobot nilai berhasil disimpan.');
    }

    public function saveStudentGrades(): void
    {
        $this->validate([
            'studentGrades.*.attendance_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'studentGrades.*.uts_score' => ['nullable', 'integer', 'min:0', 'max:100'],
            'studentGrades.*.uas_score' => ['nullable', 'integer', 'min:0', 'max:100'],
        ], [
            'studentGrades.*.attendance_score.integer' => 'Nilai kehadiran harus berupa angka.',
            'studentGrades.*.uts_score.integer' => 'Nilai UTS harus berupa angka.',
            'studentGrades.*.uas_score.integer' => 'Nilai UAS harus berupa angka.',
        ]);

        foreach ($this->studentGrades as $userId => $grades) {
            CourseEnrollment::query()
                ->where('course_id', $this->course->id)
                ->where('user_id', $userId)
                ->update([
                    'attendance_score' => $grades['attendance_score'] !== '' ? (int) $grades['attendance_score'] : null,
                    'uts_score' => $grades['uts_score'] !== '' ? (int) $grades['uts_score'] : null,
                    'uas_score' => $grades['uas_score'] !== '' ? (int) $grades['uas_score'] : null,
                ]);
        }

        $this->course->load('students');
        $this->fillStudentGrades();
        session()->flash('success', 'Nilai kehadiran, UTS, dan UAS berhasil disimpan.');
    }

    public function render(): View
    {
        return view('livewire.dosen.elearning.grade-settings');
    }
}
