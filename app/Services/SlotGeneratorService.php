<?php

namespace App\Services;

use App\Enums\DayOfWeek;
use App\Models\Provider;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SlotGeneratorService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /** @return Collection<int, string> */
    public function getAvailableSlots(Provider $provider, string $date): Collection
    {
        $carbonDate = Carbon::parse($date);
        $dayOfWeek = DayOfWeek::from($carbonDate->dayOfWeek);
        $schedule = $provider->schedules()
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)->first();

        if (! $schedule) {
            return collect();
        }

        $slots = collect();
        $current = Carbon::parse($schedule->start_time);
        $end = Carbon::parse($schedule->end_time);

        while ($current->lt($end)) {
            $slots->push($current->format('H:i'));
            $current->addMinutes($schedule->slot_duration_minutes);
        }

        $bookedSlots = $provider->bookings()
            ->where('booking_date', $date)
            ->pluck('start_time')
            ->map(fn ($time) => Carbon::parse($time)->format('H:i'));

        return $slots->diff($bookedSlots);
    }
}
