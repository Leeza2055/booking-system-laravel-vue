<?php

namespace App\Models;

use App\Enums\DayOfWeek;
use Database\Factories\ScheduleFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property DayOfWeek $day_of_week
 */
class Schedule extends Model
{
    /** @use HasFactory<ScheduleFactory> */
    use HasFactory;

    public $appends = ['day_of_week_label'];

    protected function casts(): array
    {
        return [
            'day_of_week' => DayOfWeek::class,
            'start_time' => 'datetime:h:i A',
            'end_time' => 'datetime:h:i A',
        ];
    }

    /**
     * @return BelongsTo<Provider, $this>
     */
    public function provider(): BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function getDayOfWeekLabelAttribute(): string
    {
        return $this->day_of_week->name;
    }
}
