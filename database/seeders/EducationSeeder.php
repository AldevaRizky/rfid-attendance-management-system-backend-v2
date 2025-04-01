<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Education;

class EducationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Membuat data pendidikan lengkap di Indonesia
        $educations = [
            ['name' => 'Taman Kanak-Kanak (TK)', 'level' => 1],
            ['name' => 'Sekolah Dasar (SD)', 'level' => 2],
            ['name' => 'Sekolah Menengah Pertama (SMP)', 'level' => 3],
            ['name' => 'Sekolah Menengah Atas (SMA)', 'level' => 4],
            ['name' => 'Sekolah Menengah Kejuruan (SMK)', 'level' => 4],
            ['name' => 'Diploma 1 (D1)', 'level' => 5],
            ['name' => 'Diploma 2 (D2)', 'level' => 6],
            ['name' => 'Diploma 3 (D3)', 'level' => 7],
            ['name' => 'Diploma 4 (D4)', 'level' => 8],
            ['name' => 'Sarjana (S1)', 'level' => 8],
            ['name' => 'Magister (S2)', 'level' => 9],
            ['name' => 'Doktor (S3)', 'level' => 10],
            ['name' => 'Pendidikan Nonformal', 'level' => 0],
            ['name' => 'Pendidikan Informal', 'level' => 0],
        ];

        // Loop untuk membuat data pendidikan
        foreach ($educations as $education) {
            Education::create($education);
        }
    }
}
