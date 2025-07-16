<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NonTeachingStaff;

class NonTeachingStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat 1 staf per sekolah (total 3 staf) untuk testing
     */
    public function run(): void
    {
        $staffs = [
            // SD Negeri 122 (ID: 1)
            [
                'school_id' => 1,
                'full_name' => 'Dewi Safitri',
                'nip_nik' => '198501012010012001',
                'birth_place' => 'Pematang Siantar',
                'birth_date' => '1985-01-01',
                'gender' => 'Perempuan',
                'religion' => 'Islam',
                'address' => 'Jl. Merdeka No. 10, Pematang Siantar',
                'staff_type' => 'Tata Usaha',
                'position' => 'Kepala TU',
                'education_level' => 'S1',
                'education_major' => 'Administrasi Pendidikan',
                'employment_status' => 'PNS',
                'rank' => 'III/c',
                'tmt' => '2010-01-01',
                'status' => 'Aktif'
            ],
            // SMP Negeri 1 (ID: 2)
            [
                'school_id' => 2,
                'full_name' => 'Rudi Hartono',
                'nip_nik' => '198601012010011001',
                'birth_place' => 'Medan',
                'birth_date' => '1986-01-01',
                'gender' => 'Laki-laki',
                'religion' => 'Islam',
                'address' => 'Jl. Sudirman No. 15, Pematang Siantar',
                'staff_type' => 'Laboratorium',
                'position' => 'Kepala Lab IPA',
                'education_level' => 'S1',
                'education_major' => 'Pendidikan IPA',
                'employment_status' => 'PNS',
                'rank' => 'III/b',
                'tmt' => '2010-01-01',
                'status' => 'Aktif'
            ],
            // SMA Negeri 1 (ID: 3)
            [
                'school_id' => 3,
                'full_name' => 'Ahmad Fauzi',
                'nip_nik' => '198701012010011002',
                'birth_place' => 'Pematang Siantar',
                'birth_date' => '1987-01-01',
                'gender' => 'Laki-laki',
                'religion' => 'Islam',
                'address' => 'Jl. Diponegoro No. 20, Pematang Siantar',
                'staff_type' => 'Perpustakaan',
                'position' => 'Kepala Perpustakaan',
                'education_level' => 'S1',
                'education_major' => 'Ilmu Perpustakaan',
                'employment_status' => 'PNS',
                'rank' => 'III/b',
                'tmt' => '2010-01-01',
                'status' => 'Aktif'
            ]
        ];

        foreach ($staffs as $staff) {
            NonTeachingStaff::create($staff);
        }
    }
}
