<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat sekolah manual untuk testing (SD, SMP, TK, KB, PKBM)
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
                'desa' => 'Kartini',
                'kecamatan' => 'Siantar Tengah',
                'kabupaten_kota' => 'Pematang Siantar',
                'provinsi' => 'Sumatera Utara',
                'google_maps_link' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3964.8930930944184!2d106.96155771105077!3d-6.407771062642807!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6994574258df01%3A0x61558da324fa71fc!2sYayasan%20Al-Hadiid!5e0!3m2!1sen!2sid!4v1760074874200!5m2!1sen!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
                'latitude' => 2.9641,
                'longitude' => 99.0615,
                'phone' => '0622-21349',
                'email' => 'sdn122@pematangsiantar.sch.id',
                'website' => 'https://sdn122-pematangsiantar.sch.id',
                'headmaster' => 'Hj. Rosita S.Pd',
                'logo' => null
            ],
            [
                'name' => 'SMP Negeri 1 Pematang Siantar',
                'npsn' => '10258123',
                'education_level' => 'SMP',
                'status' => 'Negeri',
                'address' => 'Jl. Ahmad Yani No. 10, Pematang Siantar',
                'desa' => 'Ahmad Yani',
                'kecamatan' => 'Siantar Utara',
                'kabupaten_kota' => 'Pematang Siantar',
                'provinsi' => 'Sumatera Utara',
                'latitude' => 2.9589,
                'longitude' => 99.0623,
                'phone' => '0622-21347',
                'email' => 'smpn1@pematangsiantar.sch.id',
                'website' => 'https://smpn1-pematangsiantar.sch.id',
                'headmaster' => 'Hj. Marlina S.Pd',
                'logo' => null
            ],
            [
                'name' => 'TK Negeri Pembina Pematang Siantar',
                'npsn' => '10258122',
                'education_level' => 'TK',
                'status' => 'Negeri',
                'address' => 'Jl. Hang Tuah No. 8, Pematang Siantar',
                'desa' => 'Hang Tuah',
                'kecamatan' => 'Siantar Tengah',
                'kabupaten_kota' => 'Pematang Siantar',
                'provinsi' => 'Sumatera Utara',
                'latitude' => 2.9701,
                'longitude' => 99.0556,
                'phone' => '0622-21346',
                'email' => 'tk.pembina@pematangsiantar.sch.id',
                'website' => 'https://tkpembina-pematangsiantar.sch.id',
                'headmaster' => 'Nurhasanah, S.Pd.AUD',
                'logo' => null
            ],
            [
                'name' => 'TK Swasta Melati',
                'npsn' => '10258132',
                'education_level' => 'TK',
                'status' => 'Swasta',
                'address' => 'Jl. Mawar No. 3, Pematang Siantar',
                'desa' => 'Mawar',
                'kecamatan' => 'Siantar Barat',
                'kabupaten_kota' => 'Pematang Siantar',
                'provinsi' => 'Sumatera Utara',
                'latitude' => 2.9512,
                'longitude' => 99.0689,
                'phone' => '0622-21357',
                'email' => 'tk.melati@pematangsiantar.sch.id',
                'website' => 'https://tkmelati-pematangsiantar.sch.id',
                'headmaster' => 'Siti Nurlaila, S.Pd.AUD',
                'logo' => null
            ],
            [
                'name' => 'SD Swasta Budi Mulia',
                'npsn' => '10258126',
                'education_level' => 'SD',
                'status' => 'Swasta',
                'address' => 'Jl. Sisingamangaraja No. 12, Pematang Siantar',
                'desa' => 'Sisingamangaraja',
                'kecamatan' => 'Siantar Barat',
                'kabupaten_kota' => 'Pematang Siantar',
                'provinsi' => 'Sumatera Utara',
                'latitude' => 2.9456,
                'longitude' => 99.0712,
                'phone' => '0622-21350',
                'email' => 'budi.mulia@pematangsiantar.sch.id',
                'website' => 'https://budimulia-pematangsiantar.sch.id',
                'headmaster' => 'Sumarno, S.Pd',
                'logo' => null
            ],
            [
                'name' => 'SMP Swasta Methodist',
                'npsn' => '10258127',
                'education_level' => 'SMP',
                'status' => 'Swasta',
                'address' => 'Jl. Diponegoro No. 22, Pematang Siantar',
                'desa' => 'Diponegoro',
                'kecamatan' => 'Siantar Marihat',
                'kabupaten_kota' => 'Pematang Siantar',
                'provinsi' => 'Sumatera Utara',
                'latitude' => 2.9603,
                'longitude' => 99.0634,
                'phone' => '0622-21351',
                'email' => 'methodist@pematangsiantar.sch.id',
                'website' => 'https://methodist-pematangsiantar.sch.id',
                'headmaster' => 'Yuliana, S.Pd',
                'logo' => null
            ],
            [
                'name' => 'PKBM Harapan Bangsa',
                'npsn' => '10258128',
                'education_level' => 'PKBM',
                'status' => 'Swasta',
                'address' => 'Jl. Melanthon Siregar No. 30, Pematang Siantar',
                'desa' => 'Melanthon Siregar',
                'kecamatan' => 'Siantar Martoba',
                'kabupaten_kota' => 'Pematang Siantar',
                'provinsi' => 'Sumatera Utara',
                'latitude' => 2.9456,
                'longitude' => 99.0712,
                'phone' => '0622-21352',
                'email' => 'harapanbangsa@pematangsiantar.sch.id',
                'website' => 'https://pkbm-harapanbangsa.sch.id',
                'headmaster' => 'Pardamean, S.Pd',
                'logo' => null
            ],
            // KB Schools
            [
                'name' => 'SKB Negeri Pematang Siantar',
                'npsn' => '10258129',
                'education_level' => 'KB',
                'status' => 'Negeri',
                'address' => 'Jl. Kapten Sumarsono No. 15, Pematang Siantar',
                'desa' => 'Kapten Sumarsono',
                'kecamatan' => 'Siantar Selatan',
                'kabupaten_kota' => 'Pematang Siantar',
                'provinsi' => 'Sumatera Utara',
                'latitude' => 2.9603,
                'longitude' => 99.0634,
                'phone' => '0622-21353',
                'email' => 'skb@pematangsiantar.sch.id',
                'website' => 'https://skb-pematangsiantar.sch.id',
                'headmaster' => 'Drs. Mangasi Situmorang',
                'logo' => null
            ],
            [
                'name' => 'PKBM Cerdas Mandiri',
                'npsn' => '10258130',
                'education_level' => 'PKBM',
                'status' => 'Swasta',
                'address' => 'Jl. Veteran No. 45, Pematang Siantar',
                'desa' => 'Veteran',
                'kecamatan' => 'Siantar Sitalasari',
                'kabupaten_kota' => 'Pematang Siantar',
                'provinsi' => 'Sumatera Utara',
                'latitude' => 2.9678,
                'longitude' => 99.0598,
                'phone' => '0622-21354',
                'email' => 'cerdas.mandiri@pematangsiantar.sch.id',
                'website' => 'https://pkbmcerdasmandiri-pematangsiantar.sch.id',
                'headmaster' => 'Rizki Harahap, S.T',
                'logo' => null
            ]
        ];
        foreach ($schools as $school) {
            School::create($school);
        }
    }
}
