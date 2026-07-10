<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserApprovalStatus;
use App\Enums\UserRole;
use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Announcement;
use App\Models\Course;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.demo1.base')]
class Index extends Component
{
    use SetsBreadcrumbs;

    public function mount(): void
    {
        $this->setBreadcrumbs([]);
    }

    public function render(): View
    {
        $user = Auth::user();
        $announcements = Announcement::query()
            ->published()
            ->latest('published_at')
            ->limit(5)
            ->get();

        if ($user?->isAdmin()) {
            return view('livewire.dashboard.admin', [
                'announcements' => $announcements,
                'stats' => [
                    'pending' => User::query()
                        ->whereRoleIn([UserRole::Dosen, UserRole::Mahasiswa])
                        ->whereApprovalStatus(UserApprovalStatus::Pending)
                        ->count('*'),
                    'dosen' => User::query()
                        ->whereRoleIn([UserRole::Dosen])
                        ->whereApprovalStatus(UserApprovalStatus::Approved)
                        ->count('*'),
                    'mahasiswa' => User::query()
                        ->whereRoleIn([UserRole::Mahasiswa])
                        ->whereApprovalStatus(UserApprovalStatus::Approved)
                        ->count('*'),
                    'courses' => Course::query()->count('*'),
                ],
            ]);
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
                'announcements' => $announcements,
                'stats' => [
                    'courses' => $allCourses->count(),
                    'students' => $allCourses->sum('students_count'),
                    'materials' => $allCourses->sum('materials_count'),
                ],
            ]);
        }

        return view('livewire.dashboard.index', [
            'announcements' => $announcements,
        ]);
    }
}
