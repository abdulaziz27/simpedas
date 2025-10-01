<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat sekolah manual untuk testing (SD, SMP, TK, Non Formal)
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
                'name' => 'TK Negeri Pembina Pematang Siantar',
                'npsn' => '10258122',
                'education_level' => 'TK',
                'status' => 'Negeri',
                'address' => 'Jl. Hang Tuah No. 8, Pematang Siantar',
                'phone' => '0622-21346',
                'email' => 'tk.pembina@pematangsiantar.sch.id',
                'website' => 'https://tkpembina-pematangsiantar.sch.id',
                'headmaster' => 'Nurhasanah, S.Pd.AUD',
                'region' => 'Siantar Tengah',
                'logo' => null
            ],
            [
                'name' => 'TK Swasta Melati',
                'npsn' => '10258132',
                'education_level' => 'TK',
                'status' => 'Swasta',
                'address' => 'Jl. Mawar No. 3, Pematang Siantar',
                'phone' => '0622-21357',
                'email' => 'tk.melati@pematangsiantar.sch.id',
                'website' => 'https://tkmelati-pematangsiantar.sch.id',
                'headmaster' => 'Siti Nurlaila, S.Pd.AUD',
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
                'name' => 'PKBM Harapan Bangsa',
                'npsn' => '10258128',
                'education_level' => 'Non Formal',
                'status' => 'Swasta',
                'address' => 'Jl. Melanthon Siregar No. 30, Pematang Siantar',
                'phone' => '0622-21352',
                'email' => 'harapanbangsa@pematangsiantar.sch.id',
                'website' => 'https://pkbm-harapanbangsa.sch.id',
                'headmaster' => 'Pardamean, S.Pd',
                'region' => 'Siantar Martoba',
                'logo' => null
            ],
            // Non Formal Schools
            [
                'name' => 'SKB Negeri Pematang Siantar',
                'npsn' => '10258129',
                'education_level' => 'Non Formal',
                'status' => 'Negeri',
                'address' => 'Jl. Kapten Sumarsono No. 15, Pematang Siantar',
                'phone' => '0622-21353',
                'email' => 'skb@pematangsiantar.sch.id',
                'website' => 'https://skb-pematangsiantar.sch.id',
                'headmaster' => 'Drs. Mangasi Situmorang',
                'region' => 'Siantar Selatan',
                'logo' => null
            ],
            [
                'name' => 'PKBM Cerdas Mandiri',
                'npsn' => '10258130',
                'education_level' => 'Non Formal',
                'status' => 'Swasta',
                'address' => 'Jl. Veteran No. 45, Pematang Siantar',
                'phone' => '0622-21354',
                'email' => 'cerdas.mandiri@pematangsiantar.sch.id',
                'website' => 'https://pkbmcerdasmandiri-pematangsiantar.sch.id',
                'headmaster' => 'Rizki Harahap, S.T',
                'region' => 'Siantar Sitalasari',
                'logo' => null
            ]
        ];
        foreach ($schools as $school) {
            School::create($school);
        }
    }
}
