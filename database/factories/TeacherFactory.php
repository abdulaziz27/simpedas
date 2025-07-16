<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teacher>
 */
class TeacherFactory extends Factory
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
            'nuptk' => $this->faker->unique()->numerify('################'),
            'nip' => $this->faker->unique()->numerify('##################'),
            'gender' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'birth_place' => $this->faker->city(),
            'birth_date' => $this->faker->date(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'religion' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
            'status' => 'Aktif',
            'academic_year' => '2023/2024',
            'education_level' => $this->faker->randomElement(['S1', 'S2', 'S3']),
            'subjects' => $this->faker->randomElement(['Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Fisika', 'Biologi']),
            'employment_status' => $this->faker->randomElement(['PNS', 'PPPK', 'GTY', 'PTY']),
        ];
    }
}
