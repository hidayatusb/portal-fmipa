<?php

namespace App\Livewire\Dosen\Elearning;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Course;
use App\Support\AssignmentNotifier;
use App\Support\CourseStorage;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.demo1.base')]
class CreateAssignment extends Component
{
    use SetsBreadcrumbs;
    use WithFileUploads;

    public Course $course;

    public string $title = '';

    public string $description = '';

    public string $dueDate = '';

    public bool $acceptLateSubmissions = false;

    public $attachment = null;

    public function mount(Course $course): void
    {
        abort_unless($course->ownedBy(Auth::id()), 403);

        $this->course = $course;

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'E-Learning', 'url' => route('dosen.elearning.index')],
            ['label' => $course->code, 'url' => route('dosen.elearning.show', $course)],
            ['label' => 'Tambah Tugas'],
        ]);
    }

    public function removeAttachment(): void
    {
        $this->reset('attachment');
        $this->resetValidation('attachment');
    }

    public function save(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'min:3', 'max:150'],
            'description' => ['nullable', 'string', 'max:2000'],
            'dueDate' => ['required', 'date', 'after:now'],
            'attachment' => [
                'nullable',
                'file',
                'max:10240',
            ],
        ], [
            'title.required' => 'Judul tugas wajib diisi.',
            'dueDate.required' => 'Batas waktu wajib diisi.',
            'dueDate.after' => 'Batas waktu tugas harus setelah waktu sekarang.',
            'attachment.max' => 'Ukuran file maksimal 10 MB.',
        ]);

        $assignment = $this->course->assignments()->create([
            'title' => $this->title,
            'description' => $this->description,
            'due_date' => Carbon::parse($this->dueDate, config('app.timezone')),
            'accept_late_submissions' => $this->acceptLateSubmissions,
        ]);

        if ($this->attachment) {
            $assignment->update([
                'attachment_name' => $this->attachment->getClientOriginalName(),
                'attachment_path' => $this->attachment->store(
                    CourseStorage::assignmentDirectory($assignment),
                    CourseStorage::diskName()
                ),
            ]);
        }

        AssignmentNotifier::notifyStudentsAboutNewAssignment($assignment->fresh());

        session()->flash('success', 'Tugas berhasil ditambahkan.');

        $this->redirect(route('dosen.elearning.show', $this->course), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.dosen.elearning.create-assignment');
    }
}
