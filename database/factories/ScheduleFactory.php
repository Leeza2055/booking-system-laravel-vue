<?php

namespace Database\Factories;

use App\Enums\DayOfWeek;
use App\Models\Provider;
use App\Models\Schedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Schedule>
 */
class ScheduleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $shift = $this->faker->randomElement([
            ['09:00:00', '17:00:00'],
            ['10:00:00', '18:00:00'],
            ['08:00:00', '14:00:00'],
            ['14:00:00', '20:00:00'],
        ]);

        return [
            'provider_id' => Provider::factory(),
            'day_of_week' => DayOfWeek::cases()[random_int(0, 6)],
            'start_time' => $shift[0],
            'end_time' => $shift[1],
            'slot_duration_minutes' => $this->faker->randomElement([15, 30, 45, 60]),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
