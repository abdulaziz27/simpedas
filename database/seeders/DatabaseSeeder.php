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
            RoleSeeder::class,
            SchoolSeeder::class,
            TeacherSeeder::class,
            StudentSeeder::class,
            NonTeachingStaffSeeder::class,
            UserSeeder::class,
            ArticleSeeder::class,
        ]);
    }
}
