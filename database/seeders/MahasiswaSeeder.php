<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class MahasiswaSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'mahasiswa'],
            [
                'name' => 'Andi Pratama',
                'email' => 'mahasiswa@lms.test',
                'password' => 'password',
                'role' => UserRole::Mahasiswa,
            ]
        );

        for ($i = 2; $i <= 20; $i++) {
            $number = str_pad((string) $i, 2, '0', STR_PAD_LEFT);

            User::updateOrCreate(
                ['username' => "mahasiswa{$number}"],
                [
                    'name' => fake()->name(),
                    'email' => "mahasiswa{$number}@lms.test",
                    'password' => 'password',
                    'role' => UserRole::Mahasiswa,
                ]
            );
        }
    }
}
