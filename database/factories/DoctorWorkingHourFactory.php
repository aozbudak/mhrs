<?php

namespace Database\Factories;

use App\Models\Doctor;
use App\Models\DoctorWorkingHour;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorWorkingHour>
 */
class DoctorWorkingHourFactory extends Factory
{
    protected $model = DoctorWorkingHour::class;

    public function definition(): array
    {
        return [
            'doctor_id' => Doctor::factory(),
            'weekday' => fake()->numberBetween(1, 5),
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'sort_order' => 0,
        ];
    }
}
