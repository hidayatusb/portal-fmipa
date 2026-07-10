<?php

namespace App\Livewire\Admin\UserApprovals;

use App\Enums\UserApprovalStatus;
use App\Enums\UserRole;
use App\Livewire\Concerns\SetsBreadcrumbs;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.demo1.base')]
class Index extends Component
{
    use SetsBreadcrumbs;

    public string $status = 'pending';

    public function mount(): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $this->setBreadcrumbs([
            ['label' => 'Home', 'url' => route('dashboard.index')],
            ['label' => 'Review Akun'],
        ]);
    }

    public function approve(int $userId): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $user = User::query()
            ->whereKey($userId)
            ->whereIn('role', [UserRole::Dosen, UserRole::Mahasiswa])
            ->where('approval_status', UserApprovalStatus::Pending)
            ->firstOrFail();

        $user->update(['approval_status' => UserApprovalStatus::Approved]);

        session()->flash('success', "Akun {$user->name} berhasil disetujui.");
    }

    public function reject(int $userId): void
    {
        abort_unless(Auth::user()?->isAdmin(), 403);

        $user = User::query()
            ->whereKey($userId)
            ->whereIn('role', [UserRole::Dosen, UserRole::Mahasiswa])
            ->where('approval_status', UserApprovalStatus::Pending)
            ->firstOrFail();

        $user->update(['approval_status' => UserApprovalStatus::Rejected]);

        session()->flash('success', "Akun {$user->name} ditolak.");
    }

    public function render(): View
    {
        $status = $this->status === 'all' ? null : UserApprovalStatus::tryFrom($this->status);

        $users = User::query()
            ->whereIn('role', [UserRole::Dosen, UserRole::Mahasiswa])
            ->when($status, fn ($query) => $query->where('approval_status', $status))
            ->latest()
            ->get();

        $pendingCount = User::query()
            ->whereIn('role', [UserRole::Dosen, UserRole::Mahasiswa])
            ->where('approval_status', UserApprovalStatus::Pending)
            ->count();

        return view('livewire.admin.user-approvals.index', [
            'users' => $users,
            'pendingCount' => $pendingCount,
        ]);
    }
}
