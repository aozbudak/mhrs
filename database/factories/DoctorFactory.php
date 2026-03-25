<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    protected $model = Doctor::class;

    public function definition(): array
    {
        return [
            'user_id' => null,
            'department_id' => Department::factory(),
            'title' => fake()->randomElement(['Uzman Dr.', 'Doç. Dr.', 'Prof. Dr.', 'Dr.']),
            'license_number' => fake()->optional(0.7)->numerify('########'),
            'bio' => fake()->optional(0.4)->paragraph(),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
