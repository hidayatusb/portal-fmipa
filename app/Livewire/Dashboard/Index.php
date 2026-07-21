<?php

namespace App\Livewire\Dashboard;

use App\Enums\UserApprovalStatus;
use App\Enums\UserRole;
use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\Announcement;
use App\Models\AssignmentSubmission;
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

        if ($user?->isAdmin()) {
            return view('livewire.dashboard.admin', [
                'announcements' => Announcement::query()
                    ->published()
                    ->latest('published_at')
                    ->limit(5)
                    ->get(),
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

            $courseIds = Course::query()
                ->where('user_id', $user->id)
                ->pluck('id');

            $ungradedSubmissions = AssignmentSubmission::query()
                ->whereNull('score', 'and', false)
                ->whereHas(
                    'assignment',
                    fn ($query) => $query->whereIn('course_id', $courseIds->all(), 'and', false),
                    '>=',
                    1,
                )
                ->count('*');

            return view('livewire.dashboard.dosen', [
                'user' => $user,
                'courses' => $courses,
                'stats' => [
                    'courses' => $courseIds->count(),
                    'ungraded' => $ungradedSubmissions,
                ],
            ]);
        }

        return view('livewire.dashboard.index');
    }
}
