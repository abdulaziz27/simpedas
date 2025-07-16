<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat 3 sekolah untuk testing (1 SD, 1 SMP, 1 SMA)
     */
    public function run(): void
    {
        $schools = [
            [
                'name' => 'SD Negeri 122 Pematang Siantar',
                'npsn' => '10258125',
                'education_level' => 'SD',
                'status' => 'Negeri',
                'address' => 'Jl. Kartini No. 5, Pematang Siantar',
                'phone' => '0622-21349',
                'email' => 'sdn122@pematangsiantar.sch.id',
                'headmaster' => 'Hj. Rosita S.Pd',
                'region' => 'Siantar Tengah'
            ],
            [
                'name' => 'SMP Negeri 1 Pematang Siantar',
                'npsn' => '10258123',
                'education_level' => 'SMP',
                'status' => 'Negeri',
                'address' => 'Jl. Ahmad Yani No. 10, Pematang Siantar',
                'phone' => '0622-21347',
                'email' => 'smpn1@pematangsiantar.sch.id',
                'headmaster' => 'Hj. Marlina S.Pd',
                'region' => 'Siantar Utara'
            ],
            [
                'name' => 'SMA Negeri 1 Pematang Siantar',
                'npsn' => '10258122',
                'education_level' => 'SMA',
                'status' => 'Negeri',
                'address' => 'Jl. Sudirman No. 20, Pematang Siantar',
                'phone' => '0622-21346',
                'email' => 'sman1@pematangsiantar.sch.id',
                'headmaster' => 'Dr. Siti Aminah',
                'region' => 'Siantar Timur'
            ]
        ];

        foreach ($schools as $school) {
            School::create($school);
        }
    }
}
