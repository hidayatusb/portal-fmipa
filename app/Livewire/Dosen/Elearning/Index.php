<?php

namespace App\Livewire\Dosen\Elearning;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.demo1.base')]
class Index extends Component
{
    use SetsBreadcrumbs;

    public bool $showForm = false;

    #[Validate('required|min:3|max:150')]
    public string $title = '';

    #[Validate('required|min:2|max:20|unique:courses,code')]
    public string $code = '';

    #[Validate('nullable|max:1000')]
    public string $description = '';

    public string $search = '';

    public function mount(): void
    {
        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'E-Learning'],
        ]);
    }

    public function toggleForm(): void
    {
        $this->showForm = ! $this->showForm;

        if (! $this->showForm) {
            $this->reset(['title', 'code', 'description']);
            $this->resetValidation();
        }
    }

    public function saveCourse(): void
    {
        $this->validate();

        Course::create([
            'user_id' => Auth::id(),
            'title' => $this->title,
            'code' => strtoupper($this->code),
            'description' => $this->description,
        ]);

        $this->toggleForm();
        session()->flash('success', 'Mata kuliah berhasil ditambahkan.');
    }

    public function render(): View
    {
        $allCourses = Course::query()
            ->where('user_id', Auth::id())
            ->withCount(['materials', 'students'])
            ->latest()
            ->get();

        $search = trim($this->search);

        $courses = $search === ''
            ? $allCourses
            : $allCourses->filter(function (Course $course) use ($search) {
                $term = strtolower($search);

                return str_contains(strtolower($course->title), $term)
                    || str_contains(strtolower($course->code), $term)
                    || str_contains(strtolower($course->description ?? ''), $term);
            })->values();

        return view('livewire.dosen.elearning.index', [
            'courses' => $courses,
            'courseCount' => $allCourses->count(),
        ]);
    }
}
