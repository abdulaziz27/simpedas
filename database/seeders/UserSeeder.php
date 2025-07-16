<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
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
        $adminDinas = User::create([
            'name' => 'Admin Dinas Pendidikan',
            'email' => 'admin@dinaspendidikanpematang.com',
            'password' => Hash::make('password'),
            'phone' => '0622-123456',
            'school_id' => null,
            'teacher_id' => null
        ]);
        $adminDinas->assignRole('admin_dinas');

        // 2. Admin SD Negeri 122 (ID: 1)
        $adminSD = User::create([
            'name' => 'Admin SD Negeri 122',
            'email' => 'admin@sdn122-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '0622-21349',
            'school_id' => 1,
            'teacher_id' => null
        ]);
        $adminSD->assignRole('admin_sekolah');

        // 3. Admin SMP Negeri 1 (ID: 2)
        $adminSMP = User::create([
            'name' => 'Admin SMP Negeri 1',
            'email' => 'admin@smpn1-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '0622-21347',
            'school_id' => 2,
            'teacher_id' => null
        ]);
        $adminSMP->assignRole('admin_sekolah');

        // 4. Guru SD Negeri 122 (ID: 1)
        $guru1 = User::create([
            'name' => 'Siti Fatimah',
            'email' => 'siti.fatimah@sdn122-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '081234567001',
            'school_id' => 1,
            'teacher_id' => 1
        ]);
        $guru1->assignRole('guru');

        // 5. Guru SMP Negeri 1 (ID: 3)
        $guru2 = User::create([
            'name' => 'Muhammad Yusuf',
            'email' => 'muhammad.yusuf@smpn1-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '081234567003',
            'school_id' => 2,
            'teacher_id' => 3
        ]);
        $guru2->assignRole('guru');

        // 6. Admin SMA Swasta HKBP (ID: 6)
        $adminHKBP = User::create([
            'name' => 'Admin SMA Swasta HKBP',
            'email' => 'admin@hkbp-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '0622-21352',
            'school_id' => 6,
            'teacher_id' => null
        ]);
        $adminHKBP->assignRole('admin_sekolah');

        // 7. Guru SMA Swasta HKBP (ID: 11)
        $guru3 = User::create([
            'name' => 'Pardamean Siregar',
            'email' => 'pardamean.siregar@hkbp-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '081234567011',
            'school_id' => 6,
            'teacher_id' => 11
        ]);
        $guru3->assignRole('guru');

        // 8. Guru SMA Swasta HKBP (ID: 12)
        $guru4 = User::create([
            'name' => 'Yohana Simbolon',
            'email' => 'yohana.simbolon@hkbp-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '081234567012',
            'school_id' => 6,
            'teacher_id' => 12
        ]);
        $guru4->assignRole('guru');
    }
}
