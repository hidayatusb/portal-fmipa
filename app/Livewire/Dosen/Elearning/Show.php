<?php

namespace App\Livewire\Dosen\Elearning;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.demo1.base')]
class Show extends Component
{
    use SetsBreadcrumbs;

    public Course $course;

    public bool $showEditForm = false;

    public string $editTitle = '';

    public string $editCode = '';

    public string $editDescription = '';

    public string $deleteCoursePassword = '';

    public function mount(Course $course): void
    {
        abort_unless($course->ownedBy(Auth::id()), 403);

        $this->course = $course->load(['materials', 'assignments', 'students']);
        $this->fillEditForm();

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'E-Learning', 'url' => route('dosen.elearning.index')],
            ['label' => $course->code],
        ]);
    }

    protected function fillEditForm(): void
    {
        $this->editTitle = $this->course->title;
        $this->editCode = $this->course->code;
        $this->editDescription = $this->course->description ?? '';
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

    public function updateCourse(): void
    {
        $this->validate([
            'editTitle' => ['required', 'string', 'min:3', 'max:150'],
            'editCode' => [
                'required',
                'string',
                'min:2',
                'max:20',
                Rule::unique('courses', 'code')->ignore($this->course->id),
            ],
            'editDescription' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->course->update([
            'title' => $this->editTitle,
            'code' => strtoupper($this->editCode),
            'description' => $this->editDescription,
        ]);

        $this->course->refresh();
        $this->showEditForm = false;
        session()->flash('success', 'Mata kuliah berhasil diperbarui.');
    }

    public function deleteMaterial(int $materialId): void
    {
        $material = $this->course->materials()->findOrFail($materialId);
        $material->delete();

        $this->course->load('materials');
        session()->flash('success', 'Materi berhasil dihapus.');
    }

    public function resetDeleteCourseForm(): void
    {
        $this->deleteCoursePassword = '';
        $this->resetValidation();
    }

    public function deleteCourse(): void
    {
        abort_unless($this->course->ownedBy(Auth::id()), 403);

        $this->validate([
            'deleteCoursePassword' => ['required', 'current_password'],
        ], [
            'deleteCoursePassword.required' => 'Password wajib diisi untuk menghapus kelas.',
            'deleteCoursePassword.current_password' => 'Password tidak sesuai.',
        ]);

        $this->course->delete();

        session()->flash('success', 'Mata kuliah berhasil dihapus.');

        $this->redirect(route('dosen.elearning.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.dosen.elearning.show');
    }
}
