<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\UserApprovalStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Api\ApiController;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends ApiController
{
    public function __invoke(): JsonResponse
    {
        return $this->success([
            'stats' => [
                'pending_approvals' => User::query()
                    ->whereIn('role', [UserRole::Dosen, UserRole::Mahasiswa])
                    ->where('approval_status', UserApprovalStatus::Pending)
                    ->count(),
                'dosen' => User::where('role', UserRole::Dosen)->count(),
                'mahasiswa' => User::where('role', UserRole::Mahasiswa)->count(),
                'courses' => Course::count(),
            ],
        ]);
    }
}
