<?php

namespace App\Livewire\Mahasiswa\Elearning;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Course;
use App\Notifications\AssignmentSubmittedNotification;
use App\Support\CourseStorage;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.demo1.base')]
class ShowAssignment extends Component
{
    use SetsBreadcrumbs;
    use WithFileUploads;

    public Course $course;

    public Assignment $assignment;

    public ?AssignmentSubmission $submission = null;

    public string $content = '';

    public $file = null;

    public bool $removeExistingFile = false;

    public function mount(Course $course, Assignment $assignment): void
    {
        abort_unless(
            Auth::user()->enrolledCourses()->whereKey($course->id)->exists(),
            403
        );
        abort_unless($assignment->belongsToCourse($course), 404);

        $this->course = $course;
        $this->assignment = $assignment;
        $this->submission = $assignment->submissionFor(Auth::id());
        $this->fillForm();

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'E-Learning', 'url' => route('mahasiswa.elearning.index')],
            ['label' => $course->code, 'url' => route('mahasiswa.elearning.show', $course)],
            ['label' => 'Detail Tugas'],
        ]);
    }

    protected function fillForm(): void
    {
        if ($this->submission) {
            $this->content = $this->submission->content ?? '';
        } else {
            $this->content = '';
        }

        $this->reset(['file', 'removeExistingFile']);
        $this->resetValidation();
    }

    protected function authorizeSubmission(): void
    {
        abort_unless(
            Auth::user()->enrolledCourses()->whereKey($this->course->id)->exists(),
            403
        );
        abort_unless($this->assignment->belongsToCourse($this->course), 404);
    }

    public function removeFile(): void
    {
        $this->reset('file');
        $this->resetValidation('file');
    }

    public function markRemoveExistingFile(): void
    {
        $this->removeExistingFile = true;
        $this->reset('file');
    }

    public function undoRemoveExistingFile(): void
    {
        $this->removeExistingFile = false;
    }

    public function submitAssignment(): void
    {
        $this->authorizeSubmission();

        if ($this->assignment->isClosedForSubmissions()) {
            $this->addError('file', 'Batas waktu tugas sudah berakhir. Pengumpulan tidak dapat dilakukan.');

            return;
        }

        $hasExistingFile = $this->submission?->hasFile() && ! $this->removeExistingFile;

        $this->validate([
            'content' => ['nullable', 'string', 'max:5000'],
            'file' => [
                'nullable',
                'file',
                'max:10240',
            ],
        ], [
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        $willHaveFile = (bool) $this->file || $hasExistingFile;

        if (! $willHaveFile && blank($this->content)) {
            $this->addError('file', 'Unggah file atau isi catatan jawaban.');

            return;
        }

        $isUpdate = $this->submission !== null;

        $filePath = $this->submission?->file_path;
        $fileName = $this->submission?->file_name;

        if ($this->file) {
            if ($filePath) {
                CourseStorage::delete($filePath);
            }

            $fileName = $this->file->getClientOriginalName();
            $filePath = $this->file->store(
                CourseStorage::submissionDirectory($this->assignment),
                CourseStorage::diskName()
            );
        } elseif ($this->removeExistingFile && $filePath) {
            CourseStorage::delete($filePath);
            $filePath = null;
            $fileName = null;
        }

        $data = [
            'content' => $this->content ?: null,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'submitted_at' => now(),
        ];

        if ($this->submission) {
            $this->submission->update($data);
        } else {
            $this->submission = $this->assignment->submissions()->create([
                ...$data,
                'user_id' => Auth::id(),
            ]);
        }

        $this->submission->refresh();
        $this->submission->loadMissing(['student', 'assignment.course']);
        $this->fillForm();

        $this->course->loadMissing('lecturer');

        if ($lecturer = $this->course->lecturer) {
            $lecturer->notify(new AssignmentSubmittedNotification(
                submission: $this->submission,
                isUpdate: $isUpdate,
            ));
        }

        session()->flash('success', 'Jawaban tugas berhasil dikumpulkan.');
    }

    public function render(): View
    {
        return view('livewire.mahasiswa.elearning.show-assignment');
    }
}
