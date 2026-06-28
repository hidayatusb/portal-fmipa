<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $dosen = User::where('username', 'dosen')->first();
        $mahasiswa = User::where('role', UserRole::Mahasiswa)->get();

        if (! $dosen) {
            return;
        }

        $courses = [
            [
                'title' => 'Pemrograman Web',
                'code' => 'PW101',
                'description' => 'Mata kuliah dasar pengembangan aplikasi web menggunakan HTML, CSS, JavaScript, dan PHP.',
                'materials' => [
                    ['title' => 'Pengenalan HTML & CSS', 'type' => 'document', 'content' => 'https://developer.mozilla.org/en-US/docs/Web/HTML'],
                    ['title' => 'Video: Struktur Halaman Web', 'type' => 'video', 'content' => 'https://www.youtube.com/watch?v=UB1O30fR-EE'],
                    ['title' => 'Referensi PHP Dasar', 'type' => 'link', 'content' => 'https://www.php.net/manual/en/getting-started.php'],
                ],
                'assignments' => [
                    [
                        'title' => 'Tugas 1: Halaman Profil HTML',
                        'description' => 'Buat halaman profil pribadi menggunakan HTML dan CSS dasar.',
                        'due_date' => now()->addDays(7),
                    ],
                    [
                        'title' => 'Tugas 2: Formulir Pendaftaran',
                        'description' => 'Buat formulir pendaftaran responsif dengan validasi HTML5.',
                        'due_date' => now()->addDays(14),
                    ],
                ],
            ],
            [
                'title' => 'Basis Data',
                'code' => 'BD201',
                'description' => 'Konsep perancangan basis data, normalisasi, SQL, dan implementasi menggunakan MySQL.',
                'materials' => [
                    ['title' => 'Modul ERD & Normalisasi', 'type' => 'document', 'content' => 'Materi ERD dan teknik normalisasi hingga 3NF.'],
                    ['title' => 'Video: Query SQL Dasar', 'type' => 'video', 'content' => 'https://www.youtube.com/watch?v=HXV3zeQKqGY'],
                ],
                'assignments' => [
                    [
                        'title' => 'Tugas ERD Perpustakaan',
                        'description' => 'Rancang ERD untuk sistem perpustakaan kampus minimal 5 entitas.',
                        'due_date' => now()->addDays(10),
                    ],
                ],
            ],
            [
                'title' => 'Machine Learning',
                'code' => 'ML301',
                'description' => 'Pengenalan machine learning, supervised learning, dan implementasi dasar dengan Python.',
                'materials' => [
                    ['title' => 'Outline Silabus', 'type' => 'document', 'content' => 'Silabus mata kuliah Machine Learning semester ini.'],
                ],
                'assignments' => [
                    [
                        'title' => 'Review Paper Machine Learning',
                        'description' => 'Baca dan ringkas satu paper machine learning pilihan Anda.',
                        'due_date' => now()->subDays(3),
                    ],
                ],
            ],
        ];

        foreach ($courses as $courseData) {
            $materials = $courseData['materials'];
            $assignments = $courseData['assignments'];
            unset($courseData['materials'], $courseData['assignments']);

            $course = Course::updateOrCreate(
                ['code' => $courseData['code']],
                array_merge($courseData, ['user_id' => $dosen->id])
            );

            foreach ($materials as $materialIndex => $material) {
                CourseMaterial::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'title' => $material['title'],
                    ],
                    array_merge($material, ['sort_order' => $materialIndex + 1])
                );
            }

            foreach ($assignments as $assignment) {
                Assignment::updateOrCreate(
                    [
                        'course_id' => $course->id,
                        'title' => $assignment['title'],
                    ],
                    $assignment
                );
            }

            if ($mahasiswa->isNotEmpty()) {
                $maxEnrollment = $mahasiswa->count();
                $minEnrollment = min(3, $maxEnrollment);
                $enrollmentCount = fake()->numberBetween($minEnrollment, $maxEnrollment);

                $enrollments = $mahasiswa
                    ->random($enrollmentCount)
                    ->mapWithKeys(fn (User $student) => [
                        $student->id => [
                            'enrolled_at' => fake()->dateTimeBetween('-6 months', 'now'),
                        ],
                    ])
                    ->all();

                $course->students()->sync($enrollments);
            }
        }
    }
}
