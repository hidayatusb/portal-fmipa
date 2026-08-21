<?php

namespace Database\Seeders;

use App\Models\PlacementTestResult;
use Illuminate\Database\Seeder;

class PlacementTestResultSeeder extends Seeder
{
    public function run(): void
    {
        $results = [
            [
                'nim' => '2310101001',
                'nama' => 'Andi Pratama',
                'nilai' => 85,
                'hasil' => 'Lulus',
                'keterangan' => 'https://drive.google.com/file/d/1AbCdEfGhIjKlMnOpQrStUvWxYz01/view',
            ],
            [
                'nim' => '2310101002',
                'nama' => 'Siti Rahmawati',
                'nilai' => 92,
                'hasil' => 'Lulus',
                'keterangan' => 'https://drive.google.com/file/d/1AbCdEfGhIjKlMnOpQrStUvWxYz02/view',
            ],
            [
                'nim' => '2310101003',
                'nama' => 'Budi Santoso',
                'nilai' => 48,
                'hasil' => 'Tidak Lulus',
                'keterangan' => null,
            ],
            [
                'nim' => '2310101004',
                'nama' => 'Dewi Lestari',
                'nilai' => 74,
                'hasil' => 'Lulus',
                'keterangan' => 'https://drive.google.com/file/d/1AbCdEfGhIjKlMnOpQrStUvWxYz04/view',
            ],
            [
                'nim' => '2310101005',
                'nama' => 'Rizky Hidayat',
                'nilai' => 55,
                'hasil' => 'Tidak Lulus',
                'keterangan' => null,
            ],
        ];

        foreach ($results as $result) {
            PlacementTestResult::updateOrCreate(
                ['nim' => $result['nim']],
                $result
            );
        }

        // Data acak tambahan agar mudah diuji
        for ($i = 6; $i <= 20; $i++) {
            $nim = '23101010'.str_pad((string) $i, 2, '0', STR_PAD_LEFT);
            $nilai = fake()->numberBetween(30, 100);
            $lulus = $nilai >= 60;

            PlacementTestResult::updateOrCreate(
                ['nim' => $nim],
                [
                    'nama' => fake()->name(),
                    'nilai' => $nilai,
                    'hasil' => $lulus ? 'Lulus' : 'Tidak Lulus',
                    'keterangan' => $lulus
                        ? 'https://drive.google.com/file/d/1AbCdEfGhIjKlMnOpQrStUvWxYz'.str_pad((string) $i, 2, '0', STR_PAD_LEFT).'/view'
                        : null,
                ]
            );
        }
    }
}
