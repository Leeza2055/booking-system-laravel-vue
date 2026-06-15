<?php

namespace Database\Seeders;

use App\Enums\DayOfWeek;
use App\Models\Provider;
use App\Models\Schedule;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $weekdays = [
            DayOfWeek::Monday,
            DayOfWeek::Tuesday,
            DayOfWeek::Wednesday,
            DayOfWeek::Thursday,
            DayOfWeek::Friday,
        ];

        Provider::all()->each(function (Provider $provider) use ($weekdays) {
            foreach ($weekdays as $day) {
                Schedule::factory()->create([
                    'provider_id' => $provider->id,
                    'day_of_week' => $day,
                ]);
            }
        });
    }
}
