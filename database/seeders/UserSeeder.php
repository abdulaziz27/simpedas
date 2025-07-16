<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat user untuk admin dinas, admin sekolah, dan guru
     */
    public function run(): void
    {
        // Admin Dinas
        $adminDinas = User::create([
            'name' => 'Admin Dinas Pendidikan',
            'email' => 'admin@dinas-pendidikan.go.id',
            'password' => Hash::make('password'),
            'phone' => '0622-123456',
            'school_id' => null,
            'teacher_id' => null
        ]);
        $adminDinas->assignRole('admin_dinas');

        // Admin SD Negeri 122 (ID: 1)
        $adminSD = User::create([
            'name' => 'Admin SD Negeri 122',
            'email' => 'admin@sdn122-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '0622-21349',
            'school_id' => 1,
            'teacher_id' => null
        ]);
        $adminSD->assignRole('admin_sekolah');

        // Admin SMP Negeri 1 (ID: 2)
        $adminSMP = User::create([
            'name' => 'Admin SMP Negeri 1',
            'email' => 'admin@smpn1-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '0622-21347',
            'school_id' => 2,
            'teacher_id' => null
        ]);
        $adminSMP->assignRole('admin_sekolah');

        // Admin SMA Negeri 1 (ID: 3)
        $adminSMA = User::create([
            'name' => 'Admin SMA Negeri 1',
            'email' => 'admin@sman1-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '0622-21346',
            'school_id' => 3,
            'teacher_id' => null
        ]);
        $adminSMA->assignRole('admin_sekolah');

        // Guru-guru SD Negeri 122
        $guru = User::create([
            'name' => 'Siti Fatimah',
            'email' => 'siti.fatimah@sdn122-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '081234567001',
            'school_id' => 1,
            'teacher_id' => 1
        ]);
        $guru->assignRole('guru');

        $guru = User::create([
            'name' => 'Ahmad Rasyid',
            'email' => 'ahmad.rasyid@sdn122-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '081234567002',
            'school_id' => 1,
            'teacher_id' => 2
        ]);
        $guru->assignRole('guru');

        // Guru-guru SMP Negeri 1
        $guru = User::create([
            'name' => 'Muhammad Yusuf',
            'email' => 'muhammad.yusuf@smpn1-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '081234567003',
            'school_id' => 2,
            'teacher_id' => 3
        ]);
        $guru->assignRole('guru');

        $guru = User::create([
            'name' => 'Sri Wahyuni',
            'email' => 'sri.wahyuni@smpn1-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '081234567004',
            'school_id' => 2,
            'teacher_id' => 4
        ]);
        $guru->assignRole('guru');

        // Guru-guru SMA Negeri 1
        $guru = User::create([
            'name' => 'Abdul Rahman',
            'email' => 'abdul.rahman@sman1-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '081234567005',
            'school_id' => 3,
            'teacher_id' => 5
        ]);
        $guru->assignRole('guru');

        $guru = User::create([
            'name' => 'Nurul Hidayah',
            'email' => 'nurul.hidayah@sman1-pematangsiantar.sch.id',
            'password' => Hash::make('password'),
            'phone' => '081234567006',
            'school_id' => 3,
            'teacher_id' => 6
        ]);
        $guru->assignRole('guru');
    }
}
