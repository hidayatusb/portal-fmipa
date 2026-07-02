<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserApprovalStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
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

        if ($user?->isAdmin()) {
            return view('livewire.dashboard.admin', [
                'stats' => [
                    'pending' => User::query()
                        ->whereIn('role', [UserRole::Dosen, UserRole::Mahasiswa])
                        ->where('approval_status', UserApprovalStatus::Pending)
                        ->count(),
                    'dosen' => User::where('role', UserRole::Dosen)->where('approval_status', UserApprovalStatus::Approved)->count(),
                    'mahasiswa' => User::where('role', UserRole::Mahasiswa)->where('approval_status', UserApprovalStatus::Approved)->count(),
                    'courses' => Course::count(),
                ],
            ])->layoutData(['breadcrumbs' => []]);
        }

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
