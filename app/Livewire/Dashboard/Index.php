<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserRole;
use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.demo1.base')]
class Index extends Component
{
    public function mount(): void
    {
        $this->dispatch('breadcrumbs-updated', items: []);
    }

    public function render(): View
    {
        $user = Auth::user();

        if ($user?->role === UserRole::Dosen) {
            $courses = Course::query()
                ->where('user_id', $user->id)
                ->withCount(['materials', 'students'])
                ->latest()
                ->limit(5)
                ->get();

            $allCourses = Course::query()
                ->where('user_id', $user->id)
                ->withCount(['materials', 'students'])
                ->get();

            return view('livewire.dashboard.dosen', [
                'user' => $user,
                'courses' => $courses,
                'stats' => [
                    'courses' => $allCourses->count(),
                    'students' => $allCourses->sum('students_count'),
                    'materials' => $allCourses->sum('materials_count'),
                ],
            ])->layoutData(['breadcrumbs' => []]);
        }

        return view('livewire.dashboard.index')->layoutData(['breadcrumbs' => []]);
    }
}
