<?php

namespace Database\Seeders;

use App\Models\JudulSkripsi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class JudulSkripsiSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake('id_ID');

        $topik = [
            'Analisis Pengaruh',
            'Implementasi',
            'Pengembangan',
            'Evaluasi',
            'Optimasi',
            'Studi Komparasi',
            'Pemodelan',
            'Identifikasi',
            'Rancang Bangun',
            'Karakterisasi',
        ];

        $objek = [
            'Sistem Informasi Akademik Berbasis Web',
            'Algoritma Machine Learning untuk Klasifikasi Data',
            'Sensor IoT pada Monitoring Lingkungan',
            'Aplikasi Mobile untuk Layanan Mahasiswa',
            'Metode Numerik pada Persamaan Differensial',
            'Material Nanokomposit untuk Aplikasi Energi',
            'Analisis Statistik Data Curah Hujan',
            'Sistem Pakar Diagnosa Hama Tanaman',
            'Keamanan Jaringan Menggunakan Firewall',
            'Visualisasi Data Spasial dengan GIS',
            'Katalis Heterogen pada Reaksi Organik',
            'Biodiversitas Mikroalga di Perairan Pesisir',
            'Prediksi Cuaca Menggunakan Time Series',
            'Chatbot Layanan Akademik Berbasis NLP',
            'Pengolahan Citra Digital untuk Deteksi Objek',
            'Website E-Learning Interaktif',
            'Database Terdistribusi untuk Big Data',
            'Sensor Kelembaban Tanah Berbasis Arduino',
            'Analisis Regresi pada Data Produktivitas',
            'Aplikasi Inventaris Laboratorium',
        ];

        $konteks = [
            'di Fakultas MIPA',
            'pada Lingkungan Kampus',
            'Menggunakan Metode Prototype',
            'dengan Pendekatan Deep Learning',
            'Berbasis Cloud Computing',
            'Studi Kasus Universitas Sulawesi Barat',
            'Menggunakan Algoritma Random Forest',
            'dengan Framework Laravel',
            'pada Data Secondary Time Series',
            'untuk Meningkatkan Efisiensi Operasional',
        ];

        $dosen = [
            'Dr. Ahmad Fauzi, M.Si.',
            'Prof. Dr. Siti Aminah, M.Sc.',
            'Dr. Budi Hartono, M.Kom.',
            'Dr. Rina Kusuma, M.Si.',
            'Dr. Andi Wijaya, M.T.',
            'Prof. Dr. Nurhayati, M.Pd.',
            'Dr. Eko Prasetyo, M.Si.',
            'Dr. Maya Sari, M.Kom.',
            'Dr. Hendra Gunawan, M.Sc.',
            'Dr. Lestari Putri, M.Si.',
            'Dr. Rizky Ananda, M.T.',
            'Dr. Fitriani, M.Kom.',
            'Prof. Dr. Agus Santoso, M.Si.',
            'Dr. Dian Permata, M.Sc.',
            'Dr. Wahyu Nugroho, M.Kom.',
        ];

        $rows = [];

        for ($i = 1; $i <= 100; $i++) {
            $pembimbing1 = $faker->randomElement($dosen);
            $pembimbing2 = $faker->randomElement(array_values(array_diff($dosen, [$pembimbing1])));

            $rows[] = [
                'nim' => sprintf('D%08d', 23000000 + $i),
                'nama_mahasiswa' => $faker->name(),
                'judul' => sprintf(
                    '%s %s %s',
                    $faker->randomElement($topik),
                    $faker->randomElement($objek),
                    $faker->randomElement($konteks)
                ),
                'pembimbing1' => $pembimbing1,
                'pembimbing2' => $pembimbing2,
                'created_at' => Carbon::now()->subDays(100 - $i)->setTime(10, 0),
                'updated_at' => Carbon::now()->subDays(100 - $i)->setTime(10, 0),
            ];
        }

        JudulSkripsi::query()->insert($rows);
    }
}
