<?php

namespace Database\Seeders;

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
            ],
            [
                'name' => 'Dr. Budi Santoso',
                'username' => 'dosen',
                'email' => 'dosen@lms.test',
                'password' => 'password',
                'role' => UserRole::Dosen,
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
