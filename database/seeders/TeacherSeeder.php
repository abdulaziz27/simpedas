<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat 2 guru per sekolah (total 6 guru) untuk testing
     */
    public function run(): void
    {
        $teachers = [
            // SD Negeri 122 (ID: 1)
            [
                'school_id' => 1,
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
                'academic_year' => '2024/2025'
            ],
            [
                'school_id' => 1,
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
                'academic_year' => '2024/2025'
            ],
            // SMP Negeri 1 (ID: 2)
            [
                'school_id' => 2,
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
                'academic_year' => '2024/2025'
            ],
            [
                'school_id' => 2,
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
                'academic_year' => '2024/2025'
            ],
            // SMA Negeri 1 (ID: 3)
            [
                'school_id' => 3,
                'full_name' => 'Abdul Rahman',
                'nuptk' => '1234567005',
                'nip' => '197101012007011005',
                'birth_place' => 'Pematang Siantar',
                'birth_date' => '1971-01-01',
                'gender' => 'Laki-laki',
                'religion' => 'Islam',
                'address' => 'Jl. Imam Bonjol No. 30, Pematang Siantar',
                'phone' => '081234567005',
                'education_level' => 'S2 Pendidikan',
                'education_major' => 'Pendidikan Fisika',
                'subjects' => 'Fisika',
                'employment_status' => 'PNS',
                'rank' => 'IV/a',
                'position' => 'Guru Fisika',
                'tmt' => '2007-01-01',
                'status' => 'Aktif',
                'academic_year' => '2024/2025'
            ],
            [
                'school_id' => 3,
                'full_name' => 'Nurul Hidayah',
                'nuptk' => '1234567006',
                'nip' => '197201012007011006',
                'birth_place' => 'Medan',
                'birth_date' => '1972-01-01',
                'gender' => 'Perempuan',
                'religion' => 'Islam',
                'address' => 'Jl. Sutomo No. 35, Pematang Siantar',
                'phone' => '081234567006',
                'education_level' => 'S2 Pendidikan',
                'education_major' => 'Pendidikan Kimia',
                'subjects' => 'Kimia',
                'employment_status' => 'PNS',
                'rank' => 'IV/a',
                'position' => 'Guru Kimia',
                'tmt' => '2007-01-01',
                'status' => 'Aktif',
                'academic_year' => '2024/2025'
            ]
        ];

        foreach ($teachers as $teacher) {
            Teacher::create($teacher);
        }
    }
}
