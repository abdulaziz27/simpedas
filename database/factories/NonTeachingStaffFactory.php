<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\NonTeachingStaff>
 */
class NonTeachingStaffFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'full_name' => $this->faker->name(),
            'nip_nik' => $this->faker->unique()->numerify('##################'),
            'birth_place' => $this->faker->city,
            'birth_date' => $this->faker->date(),
            'gender' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'religion' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
            'address' => $this->faker->address,
            'staff_type' => 'Tenaga Administrasi Sekolah',
            'position' => $this->faker->randomElement(['Tata Usaha', 'Operator Sekolah', 'Perpustakaan', 'Keamanan']),
            'education_level' => 'SMA/Sederajat',
            'employment_status' => $this->faker->randomElement(['PNS', 'PPPK', 'PTY']),
            'status' => 'Aktif',
        ];
    }
}
