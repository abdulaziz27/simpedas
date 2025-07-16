<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Hanya menggunakan seeder manual untuk data testing yang lebih terstruktur
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,      // Buat roles dan permissions
            SchoolSeeder::class,    // Buat 2-3 sekolah untuk testing
            TeacherSeeder::class,   // Buat 2 guru per sekolah
            StudentSeeder::class,   // Buat 2-3 siswa per sekolah
            NonTeachingStaffSeeder::class, // Buat 1 staf per sekolah
            UserSeeder::class,      // Buat users untuk admin dan guru
        ]);
    }
}