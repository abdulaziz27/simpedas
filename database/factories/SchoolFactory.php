<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\School>
 */
class SchoolFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'SM' . $this->faker->randomElement(['A', 'P']) . ' Negeri ' . $this->faker->numberBetween(1, 10) . ' ' . $this->faker->city,
            'npsn' => $this->faker->unique()->numerify('########'),
            'education_level' => $this->faker->randomElement(['TK', 'SD', 'SMP', 'KB', 'PKBM']),
            'status' => 'Negeri',
            'address' => $this->faker->address,
            'desa' => $this->faker->city,
            'kecamatan' => $this->faker->randomElement(['Siantar Utara', 'Siantar Selatan', 'Siantar Barat', 'Siantar Timur', 'Siantar Marihat', 'Siantar Martoba', 'Siantar Sitalasari', 'Siantar Marimbun']),
            'kabupaten_kota' => 'Pematang Siantar',
            'provinsi' => 'Sumatera Utara',
            'latitude' => $this->faker->latitude(2.9, 3.0),
            'longitude' => $this->faker->longitude(99.0, 99.1),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'headmaster' => $this->faker->name,
        ];
    }
}
