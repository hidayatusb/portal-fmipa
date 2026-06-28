<?php

namespace App\Livewire\Mahasiswa\Elearning;

use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.demo1.base')]
class Index extends Component
{
    use SetsBreadcrumbs;

    public bool $showJoinForm = false;

    public string $joinCode = '';

    public string $search = '';

    public function mount(): void
    {
        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'E-Learning'],
        ]);
    }

    public function toggleJoinForm(): void
    {
        $this->showJoinForm = ! $this->showJoinForm;

        if (! $this->showJoinForm) {
            $this->reset(['joinCode']);
            $this->resetValidation();
        }
    }

    public function joinClass(): void
    {
        $this->validate([
            'joinCode' => ['required', 'string', 'min:2', 'max:20'],
        ], [
            'joinCode.required' => 'Kode kelas wajib diisi.',
            'joinCode.min' => 'Kode kelas minimal 2 karakter.',
            'joinCode.max' => 'Kode kelas maksimal 20 karakter.',
        ]);

        $course = Course::where('code', strtoupper(trim($this->joinCode)))->first();

        if (! $course) {
            $this->addError('joinCode', 'Kode kelas tidak ditemukan. Pastikan kode yang Anda masukkan benar.');

            return;
        }

        if (Auth::user()->enrolledCourses()->whereKey($course->id)->exists()) {
            $this->addError('joinCode', 'Anda sudah terdaftar di mata kuliah ini.');

            return;
        }

        Auth::user()->enrolledCourses()->attach($course->id, [
            'enrolled_at' => now(),
        ]);

        $this->reset(['joinCode', 'showJoinForm']);
        $this->resetValidation();

        session()->flash('success', "Berhasil gabung ke kelas {$course->title}.");
    }

    public function render(): View
    {
        $allCourses = Auth::user()
            ->enrolledCourses()
            ->with('lecturer')
            ->withCount(['materials', 'assignments'])
            ->orderByPivot('enrolled_at', 'desc')
            ->get();

        $search = trim($this->search);

        $courses = $search === ''
            ? $allCourses
            : $allCourses->filter(function (Course $course) use ($search) {
                $term = strtolower($search);

                return str_contains(strtolower($course->title), $term)
                    || str_contains(strtolower($course->code), $term)
                    || str_contains(strtolower($course->description ?? ''), $term)
                    || str_contains(strtolower($course->lecturer->name), $term);
            })->values();

        return view('livewire.mahasiswa.elearning.index', [
            'courses' => $courses,
            'courseCount' => $allCourses->count(),
        ]);
    }
}
