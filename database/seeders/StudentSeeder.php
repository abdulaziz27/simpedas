<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat 2 siswa per sekolah (total 6 siswa) untuk testing
     */
    public function run(): void
    {
        $students = [
            // SD Negeri 122 (ID: 1)
            [
                'school_id' => 1,
                'full_name' => 'Budi Santoso',
                'nisn' => '1234567001',
                'nis' => '2024001',
                'birth_place' => 'Pematang Siantar',
                'birth_date' => '2014-01-01',
                'gender' => 'Laki-laki',
                'religion' => 'Islam',
                'grade_level' => '6A',
                'major' => null,
                'achievements' => 'Juara 1 Lomba Matematika SD 2023',
                'student_status' => 'Aktif',
                'graduation_status' => null,
                'academic_year' => '2024/2025'
            ],
            [
                'school_id' => 1,
                'full_name' => 'Siti Aisyah',
                'nisn' => '1234567002',
                'nis' => '2024002',
                'birth_place' => 'Medan',
                'birth_date' => '2014-02-15',
                'gender' => 'Perempuan',
                'religion' => 'Islam',
                'grade_level' => '6A',
                'major' => null,
                'achievements' => null,
                'student_status' => 'Aktif',
                'graduation_status' => null,
                'academic_year' => '2024/2025'
            ],
            // SMP Negeri 1 (ID: 2)
            [
                'school_id' => 2,
                'full_name' => 'Ahmad Rizki',
                'nisn' => '1234567003',
                'nis' => '2024003',
                'birth_place' => 'Pematang Siantar',
                'birth_date' => '2011-03-20',
                'gender' => 'Laki-laki',
                'religion' => 'Islam',
                'grade_level' => '9A',
                'major' => null,
                'achievements' => 'Juara 2 Olimpiade IPA SMP 2023',
                'student_status' => 'Aktif',
                'graduation_status' => null,
                'academic_year' => '2024/2025'
            ],
            [
                'school_id' => 2,
                'full_name' => 'Putri Rahmawati',
                'nisn' => '1234567004',
                'nis' => '2024004',
                'birth_place' => 'Medan',
                'birth_date' => '2011-04-10',
                'gender' => 'Perempuan',
                'religion' => 'Islam',
                'grade_level' => '9A',
                'major' => null,
                'achievements' => null,
                'student_status' => 'Aktif',
                'graduation_status' => null,
                'academic_year' => '2024/2025'
            ],
            // SMA Negeri 1 (ID: 3)
            [
                'school_id' => 3,
                'full_name' => 'Muhammad Farhan',
                'nisn' => '1234567005',
                'nis' => '2024005',
                'birth_place' => 'Pematang Siantar',
                'birth_date' => '2008-05-25',
                'gender' => 'Laki-laki',
                'religion' => 'Islam',
                'grade_level' => '12 IPA 1',
                'major' => 'IPA',
                'achievements' => 'Juara 1 Olimpiade Fisika SMA 2023',
                'student_status' => 'Aktif',
                'graduation_status' => null,
                'academic_year' => '2024/2025'
            ],
            [
                'school_id' => 3,
                'full_name' => 'Annisa Putri',
                'nisn' => '1234567006',
                'nis' => '2024006',
                'birth_place' => 'Medan',
                'birth_date' => '2008-06-15',
                'gender' => 'Perempuan',
                'religion' => 'Islam',
                'grade_level' => '12 IPA 1',
                'major' => 'IPA',
                'achievements' => null,
                'student_status' => 'Aktif',
                'graduation_status' => null,
                'academic_year' => '2024/2025'
            ]
        ];

        foreach ($students as $student) {
            Student::create($student);
        }
    }
}
