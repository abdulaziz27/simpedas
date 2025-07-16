<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat 9 sekolah manual untuk testing (SD, SMP, SMA, SMK, TK)
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
                'website' => 'https://sdn122-pematangsiantar.sch.id',
                'headmaster' => 'Hj. Rosita S.Pd',
                'region' => 'Siantar Tengah',
                'logo' => null
            ],
            [
                'name' => 'SMP Negeri 1 Pematang Siantar',
                'npsn' => '10258123',
                'education_level' => 'SMP',
                'status' => 'Negeri',
                'address' => 'Jl. Ahmad Yani No. 10, Pematang Siantar',
                'phone' => '0622-21347',
                'email' => 'smpn1@pematangsiantar.sch.id',
                'website' => 'https://smpn1-pematangsiantar.sch.id',
                'headmaster' => 'Hj. Marlina S.Pd',
                'region' => 'Siantar Utara',
                'logo' => null
            ],
            [
                'name' => 'SMA Negeri 1 Pematang Siantar',
                'npsn' => '10258122',
                'education_level' => 'SMA',
                'status' => 'Negeri',
                'address' => 'Jl. Sudirman No. 20, Pematang Siantar',
                'phone' => '0622-21346',
                'email' => 'sman1@pematangsiantar.sch.id',
                'website' => 'https://sman1-pematangsiantar.sch.id',
                'headmaster' => 'Dr. Siti Aminah',
                'region' => 'Siantar Timur',
                'logo' => null
            ],
            [
                'name' => 'SD Swasta Budi Mulia',
                'npsn' => '10258126',
                'education_level' => 'SD',
                'status' => 'Swasta',
                'address' => 'Jl. Sisingamangaraja No. 12, Pematang Siantar',
                'phone' => '0622-21350',
                'email' => 'budi.mulia@pematangsiantar.sch.id',
                'website' => 'https://budimulia-pematangsiantar.sch.id',
                'headmaster' => 'Sumarno, S.Pd',
                'region' => 'Siantar Barat',
                'logo' => null
            ],
            [
                'name' => 'SMP Swasta Methodist',
                'npsn' => '10258127',
                'education_level' => 'SMP',
                'status' => 'Swasta',
                'address' => 'Jl. Diponegoro No. 22, Pematang Siantar',
                'phone' => '0622-21351',
                'email' => 'methodist@pematangsiantar.sch.id',
                'website' => 'https://methodist-pematangsiantar.sch.id',
                'headmaster' => 'Yuliana, S.Pd',
                'region' => 'Siantar Marihat',
                'logo' => null
            ],
            [
                'name' => 'SMA Swasta HKBP',
                'npsn' => '10258128',
                'education_level' => 'SMA',
                'status' => 'Swasta',
                'address' => 'Jl. Melanthon Siregar No. 30, Pematang Siantar',
                'phone' => '0622-21352',
                'email' => 'hkbp@pematangsiantar.sch.id',
                'website' => 'https://hkbp-pematangsiantar.sch.id',
                'headmaster' => 'Pardamean, S.Pd',
                'region' => 'Siantar Martoba',
                'logo' => null
            ],
            // SMK Schools
            [
                'name' => 'SMK Negeri 1 Pematang Siantar',
                'npsn' => '10258129',
                'education_level' => 'SMK',
                'status' => 'Negeri',
                'address' => 'Jl. Kapten Sumarsono No. 15, Pematang Siantar',
                'phone' => '0622-21353',
                'email' => 'smkn1@pematangsiantar.sch.id',
                'website' => 'https://smkn1-pematangsiantar.sch.id',
                'headmaster' => 'Drs. Mangasi Situmorang',
                'region' => 'Siantar Selatan',
                'logo' => null
            ],
            [
                'name' => 'SMK Swasta Teknologi Mandiri',
                'npsn' => '10258130',
                'education_level' => 'SMK',
                'status' => 'Swasta',
                'address' => 'Jl. Veteran No. 45, Pematang Siantar',
                'phone' => '0622-21354',
                'email' => 'teknologi.mandiri@pematangsiantar.sch.id',
                'website' => 'https://teknologimandiri-pematangsiantar.sch.id',
                'headmaster' => 'Rizki Harahap, S.T',
                'region' => 'Siantar Sitalasari',
                'logo' => null
            ],
            // TK School
            [
                'name' => 'TK Negeri Pembina Pematang Siantar',
                'npsn' => '10258131',
                'education_level' => 'TK',
                'status' => 'Negeri',
                'address' => 'Jl. Hang Tuah No. 8, Pematang Siantar',
                'phone' => '0622-21355',
                'email' => 'tk.pembina@pematangsiantar.sch.id',
                'website' => 'https://tkpembina-pematangsiantar.sch.id',
                'headmaster' => 'Nurhasanah, S.Pd.AUD',
                'region' => 'Siantar Tengah',
                'logo' => null
            ]
        ];
        foreach ($schools as $school) {
            School::create($school);
        }
    }
}
