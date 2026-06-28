<?php

namespace App\Livewire\Dosen\Elearning;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Assignment;
use App\Models\Course;
use App\Support\CourseStorage;
use Carbon\Carbon;
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

    public bool $showEditForm = false;

    public string $editTitle = '';

    public string $editDescription = '';

    public string $editDueDate = '';

    public bool $editAcceptLateSubmissions = false;

    public $attachment = null;

    public bool $removeExistingAttachment = false;

    public function mount(Course $course, Assignment $assignment): void
    {
        abort_unless($course->user_id === Auth::id(), 403);
        abort_unless($assignment->course_id === $course->id, 404);

        $this->course = $course->load('students');
        $this->assignment = $assignment->load(['submissions.student']);

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'E-Learning', 'url' => route('dosen.elearning.index')],
            ['label' => $course->code, 'url' => route('dosen.elearning.show', $course)],
            ['label' => 'Detail Tugas'],
        ]);
    }

    protected function authorizeAssignment(): void
    {
        abort_unless($this->course->user_id === Auth::id(), 403);
        abort_unless($this->assignment->course_id === $this->course->id, 404);
    }

    protected function fillEditForm(): void
    {
        $this->editTitle = $this->assignment->title;
        $this->editDescription = $this->assignment->description ?? '';
        $this->editDueDate = $this->assignment->due_date->format('Y-m-d\TH:i');
        $this->editAcceptLateSubmissions = $this->assignment->acceptsLateSubmissions();
        $this->reset(['attachment', 'removeExistingAttachment']);
        $this->resetValidation();
    }

    public function toggleEditForm(): void
    {
        $this->showEditForm = ! $this->showEditForm;

        if ($this->showEditForm) {
            $this->fillEditForm();
        } else {
            $this->resetValidation();
        }
    }

    public function removeAttachment(): void
    {
        $this->reset('attachment');
        $this->resetValidation('attachment');
    }

    public function markRemoveExistingAttachment(): void
    {
        $this->removeExistingAttachment = true;
        $this->reset('attachment');
    }

    public function undoRemoveExistingAttachment(): void
    {
        $this->removeExistingAttachment = false;
    }

    public function updateAssignment(): void
    {
        $this->authorizeAssignment();

        $this->validate([
            'editTitle' => ['required', 'string', 'min:3', 'max:150'],
            'editDescription' => ['nullable', 'string', 'max:2000'],
            'editDueDate' => ['required', 'date'],
            'attachment' => [
                'nullable',
                'file',
                'max:10240',
            ],
        ], [
            'editTitle.required' => 'Judul tugas wajib diisi.',
            'editDueDate.required' => 'Batas waktu wajib diisi.',
            'attachment.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        $data = [
            'title' => $this->editTitle,
            'description' => $this->editDescription,
            'due_date' => Carbon::parse($this->editDueDate, config('app.timezone')),
            'accept_late_submissions' => $this->editAcceptLateSubmissions,
        ];

        if ($this->attachment) {
            if ($this->assignment->attachment_path) {
                CourseStorage::delete($this->assignment->attachment_path);
            }

            $data['attachment_name'] = $this->attachment->getClientOriginalName();
            $data['attachment_path'] = $this->attachment->store(
                CourseStorage::assignmentDirectory($this->assignment),
                CourseStorage::diskName()
            );
        } elseif ($this->removeExistingAttachment && $this->assignment->attachment_path) {
            CourseStorage::delete($this->assignment->attachment_path);
            $data['attachment_path'] = null;
            $data['attachment_name'] = null;
        }

        $this->assignment->update($data);
        $this->assignment->refresh();

        $this->showEditForm = false;
        session()->flash('success', 'Tugas berhasil diperbarui.');
    }

    public function deleteAssignment(): void
    {
        $this->authorizeAssignment();

        Assignment::destroy($this->assignment->id);

        session()->flash('success', 'Tugas berhasil dihapus.');

        $this->redirect(route('dosen.elearning.show', $this->course), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.dosen.elearning.show-assignment');
    }
}
