<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat 8 user utama: 1 admin dinas, 2 admin sekolah, 2 guru, 1 admin sekolah lain, 2 guru lain
     */
    public function run(): void
    {
        // 1. Admin Dinas
        $adminDinas = User::firstOrCreate(['email' => 'admin@dinaspendidikanpematang.com'], [
            'name' => 'Admin Dinas Pendidikan',
            'password' => Hash::make('password'),
            'phone' => '0622-123456',
            'school_id' => null,
            'teacher_id' => null
        ]);
        $adminDinas->assignRole('admin_dinas');

        $adminDinas = User::firstOrCreate(['email' => 'itsabdulaziz3@gmail.com'], [
            'name' => 'Abdul Aziz',
            'password' => Hash::make('password'),
            'phone' => '0852-1155-3430',
            'school_id' => null,
            'teacher_id' => null
        ]);
        $adminDinas->assignRole('admin_dinas');

        // 2. Admin Sekolah untuk dua sekolah pertama (SD dan SMP jika ada)
        $sdSchool = School::where('education_level', 'SD')->first();
        $smpSchool = School::where('education_level', 'SMP')->first();

        if ($sdSchool) {
            $adminSD = User::firstOrCreate(['email' => 'admin@sdn122-pematangsiantar.sch.id'], [
                'name' => 'Admin ' . $sdSchool->name,
                'password' => Hash::make('password'),
                'phone' => '0622-21349',
                'school_id' => $sdSchool->id,
                'teacher_id' => null
            ]);
            $adminSD->assignRole('admin_sekolah');
        }

        if ($smpSchool) {
            $adminSMP = User::firstOrCreate(['email' => 'admin@' . str_replace(' ', '', strtolower($smpSchool->name)) . '.sch.id'], [
                'name' => 'Admin ' . $smpSchool->name,
                'password' => Hash::make('password'),
                'phone' => '0622-21347',
                'school_id' => $smpSchool->id,
                'teacher_id' => null
            ]);
            $adminSMP->assignRole('admin_sekolah');
        }

        // 4-7. Buat user guru berdasarkan data teacher yang ada
        $teacherNames = ['Siti Fatimah', 'Muhammad Yusuf', 'Dewi Lestari', 'Rudi Hartono'];
        foreach ($teacherNames as $tName) {
            $teacher = Teacher::where('full_name', $tName)->first();
            if (!$teacher) {
                continue;
            }
            $email = str_replace(' ', '.', strtolower($teacher->full_name)) . '@example.com';
            if ($teacher->full_name === 'Siti Fatimah') {
                $email = 'siti.fatimah@sdn122-pematangsiantar.sch.id';
            }
            $user = User::firstOrCreate(['email' => $email], [
                'name' => $teacher->full_name,
                'password' => Hash::make('password'),
                'phone' => '0812' . rand(100000000, 999999999),
                'school_id' => $teacher->school_id,
                'teacher_id' => $teacher->id
            ]);
            $user->assignRole('guru');
        }
    }
}
