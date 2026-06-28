<?php

namespace App\Http\Controllers\Api\Admin;

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
                'users' => User::count(),
                'dosen' => User::where('role', UserRole::Dosen)->count(),
                'mahasiswa' => User::where('role', UserRole::Mahasiswa)->count(),
                'courses' => Course::count(),
            ],
        ]);
    }
}
