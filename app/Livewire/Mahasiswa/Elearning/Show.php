<?php

namespace App\Livewire\Mahasiswa\Elearning;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.demo1.base')]
class Show extends Component
{
    use SetsBreadcrumbs;

    public Course $course;

    public function mount(Course $course): void
    {
        abort_unless(
            Auth::user()->enrolledCourses()->whereKey($course->id)->exists(),
            403
        );

        $this->course = $course->load(['materials', 'assignments', 'lecturer']);

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'E-Learning', 'url' => route('mahasiswa.elearning.index')],
            ['label' => $course->code],
        ]);
    }

    public function render(): View
    {
        return view('livewire.mahasiswa.elearning.show');
    }
}
