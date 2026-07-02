<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\UserApprovalStatus;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\UserResource;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $users = User::query()
            ->when($request->filled('approval_status'), fn ($query) => $query->where('approval_status', $request->string('approval_status')))
            ->when($request->filled('role'), fn ($query) => $query->where('role', $request->string('role')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $term = '%'.$request->string('search').'%';

                $query->where(function ($inner) use ($term) {
                    $inner->where('name', 'like', $term)
                        ->orWhere('username', 'like', $term)
                        ->orWhere('email', 'like', $term);
                });
            })
            ->latest()
            ->paginate($request->integer('per_page', 20));

        return $this->success([
            'items' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function show(User $user): JsonResponse
    {
        return $this->success(UserResource::make($user));
    }

    public function approve(User $user): JsonResponse
    {
        $this->ensureReviewable($user);

        $user->update(['approval_status' => UserApprovalStatus::Approved]);

        return $this->success(UserResource::make($user->fresh()), 'Akun berhasil disetujui.');
    }

    public function reject(User $user): JsonResponse
    {
        $this->ensureReviewable($user);

        $user->update(['approval_status' => UserApprovalStatus::Rejected]);

        return $this->success(UserResource::make($user->fresh()), 'Akun berhasil ditolak.');
    }

    protected function ensureReviewable(User $user): void
    {
        abort_unless(
            $user->hasAnyRole(UserRole::Dosen, UserRole::Mahasiswa) && $user->isPendingApproval(),
            422,
            'Akun ini tidak dapat direview.'
        );
    }
}
