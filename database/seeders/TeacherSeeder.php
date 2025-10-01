<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\School;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat 12 guru manual (2 per sekolah)
     */
    public function run(): void
    {
        $schools = School::pluck('id')->all();
        if (empty($schools)) {
            return;
        }

        $teachers = [
            [
                'school_id' => null,
                'full_name' => 'Siti Fatimah',
                'nuptk' => '1234567001',
                'nip' => '196701012007011001',
                'birth_place' => 'Pematang Siantar',
                'birth_date' => '1967-01-01',
                'gender' => 'Perempuan',
                'religion' => 'Islam',
                'address' => 'Jl. Merdeka No. 10, Pematang Siantar',
                'phone' => '081234567001',
                'education_level' => 'S1 Pendidikan',
                'education_major' => 'PGSD',
                'subjects' => 'Matematika',
                'employment_status' => 'PNS',
                'rank' => 'III/d',
                'position' => 'Guru Kelas',
                'tmt' => '2007-01-01',
                'status' => 'Aktif',
                'academic_year' => '2024/2025',
                'photo' => null
            ],
            [
                'school_id' => null,
                'full_name' => 'Ahmad Rasyid',
                'nuptk' => '1234567002',
                'nip' => '196801012007011002',
                'birth_place' => 'Medan',
                'birth_date' => '1968-01-01',
                'gender' => 'Laki-laki',
                'religion' => 'Islam',
                'address' => 'Jl. Sudirman No. 15, Pematang Siantar',
                'phone' => '081234567002',
                'education_level' => 'S1 Pendidikan',
                'education_major' => 'PGSD',
                'subjects' => 'IPA',
                'employment_status' => 'PNS',
                'rank' => 'III/c',
                'position' => 'Guru Kelas',
                'tmt' => '2007-01-01',
                'status' => 'Aktif',
                'academic_year' => '2024/2025',
                'photo' => null
            ],
            [
                'school_id' => null,
                'full_name' => 'Muhammad Yusuf',
                'nuptk' => '1234567003',
                'nip' => '196901012007011003',
                'birth_place' => 'Pematang Siantar',
                'birth_date' => '1969-01-01',
                'gender' => 'Laki-laki',
                'religion' => 'Islam',
                'address' => 'Jl. Diponegoro No. 20, Pematang Siantar',
                'phone' => '081234567003',
                'education_level' => 'S2 Pendidikan',
                'education_major' => 'Pendidikan Matematika',
                'subjects' => 'Matematika',
                'employment_status' => 'PNS',
                'rank' => 'IV/a',
                'position' => 'Guru Matematika',
                'tmt' => '2007-01-01',
                'status' => 'Aktif',
                'academic_year' => '2024/2025',
                'photo' => null
            ],
            [
                'school_id' => null,
                'full_name' => 'Sri Wahyuni',
                'nuptk' => '1234567004',
                'nip' => '197001012007011004',
                'birth_place' => 'Medan',
                'birth_date' => '1970-01-01',
                'gender' => 'Perempuan',
                'religion' => 'Islam',
                'address' => 'Jl. Kartini No. 25, Pematang Siantar',
                'phone' => '081234567004',
                'education_level' => 'S1 Pendidikan',
                'education_major' => 'Pendidikan Bahasa Indonesia',
                'subjects' => 'Bahasa Indonesia',
                'employment_status' => 'PNS',
                'rank' => 'III/d',
                'position' => 'Guru Bahasa Indonesia',
                'tmt' => '2007-01-01',
                'status' => 'Aktif',
                'academic_year' => '2024/2025',
                'photo' => null
            ],
            [
                'school_id' => null,
                'full_name' => 'Dewi Lestari',
                'nuptk' => '1234567007',
                'nip' => '197301012007011007',
                'birth_place' => 'Pematang Siantar',
                'birth_date' => '1973-01-01',
                'gender' => 'Perempuan',
                'religion' => 'Islam',
                'address' => 'Jl. Sisingamangaraja No. 12, Pematang Siantar',
                'phone' => '081234567007',
                'education_level' => 'S1 Pendidikan',
                'education_major' => 'PGSD',
                'subjects' => 'Bahasa Indonesia',
                'employment_status' => 'PNS',
                'rank' => 'III/d',
                'position' => 'Guru Kelas',
                'tmt' => '2007-01-01',
                'status' => 'Aktif',
                'academic_year' => '2024/2025',
                'photo' => null
            ],
            [
                'school_id' => null,
                'full_name' => 'Bambang Sutrisno',
                'nuptk' => '1234567008',
                'nip' => '197401012007011008',
                'birth_place' => 'Medan',
                'birth_date' => '1974-01-01',
                'gender' => 'Laki-laki',
                'religion' => 'Islam',
                'address' => 'Jl. Sisingamangaraja No. 13, Pematang Siantar',
                'phone' => '081234567008',
                'education_level' => 'S1 Pendidikan',
                'education_major' => 'PGSD',
                'subjects' => 'Matematika',
                'employment_status' => 'PNS',
                'rank' => 'III/c',
                'position' => 'Guru Kelas',
                'tmt' => '2007-01-01',
                'status' => 'Aktif',
                'academic_year' => '2024/2025',
                'photo' => null
            ],
            [
                'school_id' => null,
                'full_name' => 'Yuliana Simanjuntak',
                'nuptk' => '1234567009',
                'nip' => '197501012007011009',
                'birth_place' => 'Pematang Siantar',
                'birth_date' => '1975-01-01',
                'gender' => 'Perempuan',
                'religion' => 'Kristen',
                'address' => 'Jl. Diponegoro No. 22, Pematang Siantar',
                'phone' => '081234567009',
                'education_level' => 'S1 Pendidikan',
                'education_major' => 'Pendidikan Bahasa Inggris',
                'subjects' => 'Bahasa Inggris',
                'employment_status' => 'PNS',
                'rank' => 'III/d',
                'position' => 'Guru Bahasa Inggris',
                'tmt' => '2007-01-01',
                'status' => 'Aktif',
                'academic_year' => '2024/2025',
                'photo' => null
            ],
            [
                'school_id' => null,
                'full_name' => 'Rudi Hartono',
                'nuptk' => '1234567010',
                'nip' => '197601012007011010',
                'birth_place' => 'Medan',
                'birth_date' => '1976-01-01',
                'gender' => 'Laki-laki',
                'religion' => 'Kristen',
                'address' => 'Jl. Diponegoro No. 23, Pematang Siantar',
                'phone' => '081234567010',
                'education_level' => 'S1 Pendidikan',
                'education_major' => 'Pendidikan Matematika',
                'subjects' => 'Matematika',
                'employment_status' => 'PNS',
                'rank' => 'III/c',
                'position' => 'Guru Matematika',
                'tmt' => '2007-01-01',
                'status' => 'Aktif',
                'academic_year' => '2024/2025',
                'photo' => null
            ]
        ];

        foreach ($teachers as $teacher) {
            $teacher['school_id'] = $schools[array_rand($schools)];
            Teacher::create($teacher);
        }
    }
}
