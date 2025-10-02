<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\School;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat siswa sesuai dengan struktur tabel terbaru
     */
    public function run(): void
    {
        $schools = School::pluck('id')->all();
        if (empty($schools)) {
            return;
        }

        $students = [
            [
                'sekolah_id' => null,
                'nisn' => '1234567001',
                'nipd' => '2024001',
                'nama_lengkap' => 'Budi Santoso',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Pematang Siantar',
                'tanggal_lahir' => '2014-01-01',
                'agama' => 'Islam',
                'rombel' => '6A',
                'status_siswa' => 'tamat',
                'alamat' => 'Jl. Merdeka No. 1',
                'kecamatan' => 'Siantar Marimbun',
                'kelurahan' => 'Pematang Siantar',
                'kode_pos' => '21111',
                'nama_ayah' => 'Bapak Santoso',
                'pekerjaan_ayah' => 'Wiraswasta',
                'nama_ibu' => 'Ibu Santoso',
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'anak_ke' => 1,
                'jumlah_saudara' => 2,
                'no_hp' => '081234567001',
                'kip' => false,
                'transportasi' => 'Sepeda',
                'jarak_rumah_sekolah' => 2.5,
                'tinggi_badan' => 140,
                'berat_badan' => 35,
            ],
            [
                'sekolah_id' => null,
                'nisn' => '1234567002',
                'nipd' => '2024002',
                'nama_lengkap' => 'Siti Aisyah',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '2014-02-15',
                'agama' => 'Islam',
                'rombel' => '6A',
                'status_siswa' => 'aktif',
                'alamat' => 'Jl. Sudirman No. 2',
                'kecamatan' => 'Siantar Utara',
                'kelurahan' => 'Pematang Siantar',
                'kode_pos' => '21112',
                'nama_ayah' => 'Bapak Aminah',
                'pekerjaan_ayah' => 'PNS',
                'nama_ibu' => 'Ibu Aminah',
                'pekerjaan_ibu' => 'Guru',
                'anak_ke' => 2,
                'jumlah_saudara' => 3,
                'no_hp' => '081234567002',
                'kip' => true,
                'transportasi' => 'Jalan Kaki',
                'jarak_rumah_sekolah' => 1.0,
                'tinggi_badan' => 135,
                'berat_badan' => 32,
            ],
            [
                'sekolah_id' => null,
                'nisn' => '1234567003',
                'nipd' => '2024003',
                'nama_lengkap' => 'Rizky Pratama',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Pematang Siantar',
                'tanggal_lahir' => '2014-03-10',
                'agama' => 'Islam',
                'rombel' => '6B',
                'status_siswa' => 'aktif',
                'alamat' => 'Jl. Gatot Subroto No. 3',
                'kecamatan' => 'Siantar Selatan',
                'kelurahan' => 'Pematang Siantar',
                'kode_pos' => '21113',
                'nama_ayah' => 'Bapak Pratama',
                'pekerjaan_ayah' => 'Wiraswasta',
                'nama_ibu' => 'Ibu Pratama',
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'anak_ke' => 1,
                'jumlah_saudara' => 1,
                'no_hp' => '081234567003',
                'kip' => false,
                'transportasi' => 'Motor',
                'jarak_rumah_sekolah' => 5.0,
                'tinggi_badan' => 142,
                'berat_badan' => 38,
            ],
            [
                'sekolah_id' => null,
                'nisn' => '1234567004',
                'nipd' => '2024004',
                'nama_lengkap' => 'Dewi Lestari',
                'jenis_kelamin' => 'P',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '2014-04-20',
                'agama' => 'Islam',
                'rombel' => '6B',
                'status_siswa' => 'aktif',
                'alamat' => 'Jl. Thamrin No. 4',
                'kecamatan' => 'Siantar Barat',
                'kelurahan' => 'Pematang Siantar',
                'kode_pos' => '21114',
                'nama_ayah' => 'Bapak Lestari',
                'pekerjaan_ayah' => 'Karyawan Swasta',
                'nama_ibu' => 'Ibu Lestari',
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'anak_ke' => 2,
                'jumlah_saudara' => 2,
                'no_hp' => '081234567004',
                'kip' => false,
                'transportasi' => 'Mobil',
                'jarak_rumah_sekolah' => 3.2,
                'tinggi_badan' => 138,
                'berat_badan' => 34,
            ],
            [
                'sekolah_id' => null,
                'nisn' => '1234567005',
                'nipd' => '2024005',
                'nama_lengkap' => 'Andi Saputra',
                'jenis_kelamin' => 'L',
                'tempat_lahir' => 'Pematang Siantar',
                'tanggal_lahir' => '2014-05-25',
                'agama' => 'Islam',
                'rombel' => '6C',
                'status_siswa' => 'aktif',
                'alamat' => 'Jl. Ahmad Yani No. 5',
                'kecamatan' => 'Siantar Timur',
                'kelurahan' => 'Pematang Siantar',
                'kode_pos' => '21115',
                'nama_ayah' => 'Bapak Saputra',
                'pekerjaan_ayah' => 'Wiraswasta',
                'nama_ibu' => 'Ibu Saputra',
                'pekerjaan_ibu' => 'Ibu Rumah Tangga',
                'anak_ke' => 1,
                'jumlah_saudara' => 2,
                'no_hp' => '081234567005',
                'kip' => true,
                'transportasi' => 'Sepeda',
                'jarak_rumah_sekolah' => 1.8,
                'tinggi_badan' => 145,
                'berat_badan' => 40,
            ],
        ];

        foreach ($students as $student) {
            $student['sekolah_id'] = $schools[array_rand($schools)];
            Student::create($student);
        }
    }
}
