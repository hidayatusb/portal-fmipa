<?php

namespace Database\Seeders;

use App\Enums\UserApprovalStatus;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrator',
                'username' => 'admin',
                'email' => 'admin@lms.test',
                'password' => 'password',
                'role' => UserRole::Admin,
                'approval_status' => UserApprovalStatus::Approved,
            ],
            [
                'name' => 'Dr. Budi Santoso',
                'username' => 'dosen',
                'email' => 'dosen@lms.test',
                'password' => 'password',
                'role' => UserRole::Dosen,
                'approval_status' => UserApprovalStatus::Approved,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['username' => $user['username']],
                $user
            );
        }
    }
}
