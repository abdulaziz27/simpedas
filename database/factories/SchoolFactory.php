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
            'education_level' => $this->faker->randomElement(['SMP', 'SMA', 'SMK']),
            'status' => 'Negeri',
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'headmaster' => $this->faker->name,
            'region' => $this->faker->city,
        ];
    }
}
