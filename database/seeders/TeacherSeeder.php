<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\School;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat 8 guru manual (sesuai dengan data yang sudah ada)
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
                'gender' => 'P',
                'birth_place' => 'Pematang Siantar',
                'birth_date' => '1967-01-01',
                'nip' => '196701012007011001',
                'employment_status' => 'PNS',
                'jenis_ptk' => 'Guru',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Pd.',
                'jenjang' => 'S1',
                'education_major' => 'PGSD',
                'sertifikasi' => 'Matematika',
                'tmt' => '2007-01-01',
                'tugas_tambahan' => 'Wali Kelas',
                'mengajar' => 'Matematika',
                'jam_tugas_tambahan' => 2,
                'jjm' => 24,
                'total_jjm' => 26,
                'siswa' => 30,
                'kompetensi' => 'Matematika Dasar',
                'subjects' => 'Matematika', // backward compatibility
                'photo' => null
            ],
            [
                'school_id' => null,
                'full_name' => 'Ahmad Rasyid',
                'nuptk' => '1234567002',
                'gender' => 'L',
                'birth_place' => 'Medan',
                'birth_date' => '1968-01-01',
                'nip' => '196801012007011002',
                'employment_status' => 'PNS',
                'jenis_ptk' => 'Guru',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Pd.',
                'jenjang' => 'S1',
                'education_major' => 'PGSD',
                'sertifikasi' => 'IPA',
                'tmt' => '2007-01-01',
                'tugas_tambahan' => 'Koordinator IPA',
                'mengajar' => 'IPA',
                'jam_tugas_tambahan' => 4,
                'jjm' => 24,
                'total_jjm' => 28,
                'siswa' => 32,
                'kompetensi' => 'IPA Terpadu',
                'subjects' => 'IPA', // backward compatibility
                'photo' => null
            ],
            [
                'school_id' => null,
                'full_name' => 'Muhammad Yusuf',
                'nuptk' => '1234567003',
                'gender' => 'L',
                'birth_place' => 'Pematang Siantar',
                'birth_date' => '1969-01-01',
                'nip' => '196901012007011003',
                'employment_status' => 'PNS',
                'jenis_ptk' => 'Kepala Sekolah',
                'gelar_depan' => null,
                'gelar_belakang' => 'M.Pd.',
                'jenjang' => 'S2',
                'education_major' => 'Pendidikan Matematika',
                'sertifikasi' => 'Matematika',
                'tmt' => '2007-01-01',
                'tugas_tambahan' => 'Kepala Sekolah',
                'mengajar' => 'Matematika',
                'jam_tugas_tambahan' => 12,
                'jjm' => 12,
                'total_jjm' => 24,
                'siswa' => 25,
                'kompetensi' => 'Manajemen Sekolah, Matematika',
                'subjects' => 'Matematika', // backward compatibility
                'photo' => null
            ],
            [
                'school_id' => null,
                'full_name' => 'Sri Wahyuni',
                'nuptk' => '1234567004',
                'gender' => 'P',
                'birth_place' => 'Medan',
                'birth_date' => '1970-01-01',
                'nip' => '197001012007011004',
                'employment_status' => 'PNS',
                'jenis_ptk' => 'Guru',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Pd.',
                'jenjang' => 'S1',
                'education_major' => 'Pendidikan Bahasa Indonesia',
                'sertifikasi' => 'Bahasa Indonesia',
                'tmt' => '2007-01-01',
                'tugas_tambahan' => 'Koordinator Bahasa',
                'mengajar' => 'Bahasa Indonesia',
                'jam_tugas_tambahan' => 3,
                'jjm' => 24,
                'total_jjm' => 27,
                'siswa' => 28,
                'kompetensi' => 'Bahasa Indonesia, Sastra',
                'subjects' => 'Bahasa Indonesia', // backward compatibility
                'photo' => null
            ],
            [
                'school_id' => null,
                'full_name' => 'Dewi Lestari',
                'nuptk' => '1234567007',
                'gender' => 'P',
                'birth_place' => 'Pematang Siantar',
                'birth_date' => '1973-01-01',
                'nip' => '197301012007011007',
                'employment_status' => 'PNS',
                'jenis_ptk' => 'Guru',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Pd.',
                'jenjang' => 'S1',
                'education_major' => 'PGSD',
                'sertifikasi' => 'Guru Kelas',
                'tmt' => '2007-01-01',
                'tugas_tambahan' => 'Wali Kelas',
                'mengajar' => 'Tematik',
                'jam_tugas_tambahan' => 2,
                'jjm' => 24,
                'total_jjm' => 26,
                'siswa' => 25,
                'kompetensi' => 'Pembelajaran Tematik',
                'subjects' => 'Bahasa Indonesia', // backward compatibility
                'photo' => null
            ],
            [
                'school_id' => null,
                'full_name' => 'Bambang Sutrisno',
                'nuptk' => '1234567008',
                'gender' => 'L',
                'birth_place' => 'Medan',
                'birth_date' => '1974-01-01',
                'nip' => '197401012007011008',
                'employment_status' => 'PNS',
                'jenis_ptk' => 'Guru',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Pd.',
                'jenjang' => 'S1',
                'education_major' => 'PGSD',
                'sertifikasi' => 'Matematika',
                'tmt' => '2007-01-01',
                'tugas_tambahan' => 'Wali Kelas',
                'mengajar' => 'Matematika',
                'jam_tugas_tambahan' => 2,
                'jjm' => 24,
                'total_jjm' => 26,
                'siswa' => 30,
                'kompetensi' => 'Matematika Dasar',
                'subjects' => 'Matematika', // backward compatibility
                'photo' => null
            ],
            [
                'school_id' => null,
                'full_name' => 'Yuliana Simanjuntak',
                'nuptk' => '1234567009',
                'gender' => 'P',
                'birth_place' => 'Pematang Siantar',
                'birth_date' => '1975-01-01',
                'nip' => '197501012007011009',
                'employment_status' => 'PNS',
                'jenis_ptk' => 'Guru',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Pd.',
                'jenjang' => 'S1',
                'education_major' => 'Pendidikan Bahasa Inggris',
                'sertifikasi' => 'Bahasa Inggris',
                'tmt' => '2007-01-01',
                'tugas_tambahan' => 'Koordinator Bahasa Inggris',
                'mengajar' => 'Bahasa Inggris',
                'jam_tugas_tambahan' => 3,
                'jjm' => 24,
                'total_jjm' => 27,
                'siswa' => 28,
                'kompetensi' => 'Bahasa Inggris, TOEFL',
                'subjects' => 'Bahasa Inggris', // backward compatibility
                'photo' => null
            ],
            [
                'school_id' => null,
                'full_name' => 'Rudi Hartono',
                'nuptk' => '1234567010',
                'gender' => 'L',
                'birth_place' => 'Medan',
                'birth_date' => '1976-01-01',
                'nip' => '197601012007011010',
                'employment_status' => 'PNS',
                'jenis_ptk' => 'Wakil Kepala Sekolah',
                'gelar_depan' => null,
                'gelar_belakang' => 'S.Pd.',
                'jenjang' => 'S1',
                'education_major' => 'Pendidikan Matematika',
                'sertifikasi' => 'Matematika',
                'tmt' => '2007-01-01',
                'tugas_tambahan' => 'Wakil Kepala Sekolah',
                'mengajar' => 'Matematika',
                'jam_tugas_tambahan' => 8,
                'jjm' => 16,
                'total_jjm' => 24,
                'siswa' => 20,
                'kompetensi' => 'Manajemen Kurikulum, Matematika',
                'subjects' => 'Matematika', // backward compatibility
                'photo' => null
            ]
        ];

        foreach ($teachers as $teacher) {
            $teacher['school_id'] = $schools[array_rand($schools)];
            Teacher::create($teacher);
        }
    }
}