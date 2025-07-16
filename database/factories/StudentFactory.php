<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
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
            'nisn' => $this->faker->unique()->numerify('##########'),
            'gender' => $this->faker->randomElement(['Laki-laki', 'Perempuan']),
            'birth_place' => $this->faker->city(),
            'birth_date' => $this->faker->date(),
            'religion' => $this->faker->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
            'student_status' => 'Aktif',
            'grade_level' => $this->faker->randomElement(['7', '8', '9', '10', '11', '12']),
            'academic_year' => '2023/2024',
        ];
    }
}
