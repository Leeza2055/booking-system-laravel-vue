<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $schedules = Auth::user()->provider?->schedules()->latest()->get() ?? collect();

        return Inertia::render('provider/schedules/Index', [
            'schedules' => $schedules,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('provider/schedules/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'day_of_week' => ['required', 'integer', 'between:0,6', Rule::unique('schedules')->where(fn ($query) => $query->where('provider_id', Auth::user()->provider?->id)
            ), ],
            'start_time' => ['required', 'date_format:H:i,H:i:s'],
            'end_time' => ['required', 'date_format:H:i,H:i:s', 'after:start_time'],
            'slot_duration_minutes' => ['required', 'integer', 'min:30', 'max:1440'],
            'is_active' => ['required', 'boolean'],
        ],
        );

        Auth::user()->provider?->schedules()->create($validated);

        return redirect()->route('provider.schedules.index')->with('success', 'Schedule created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule): Response
    {
        // Ensure this schedule belongs to the logged-in provider
        if ($schedule->provider_id !== Auth::user()->provider?->id) {
            abort(403);
        }

        return Inertia::render('provider/schedules/Edit', [
            'schedule' => $schedule,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule): RedirectResponse
    {
        // Ensure this schedule belongs to the logged-in provider
        if ($schedule->provider_id !== Auth::user()->provider?->id) {
            abort(403);
        }

        $validated = $request->validate([
            'day_of_week' => ['required', 'integer', 'between:0,6', Rule::unique('schedules')
                ->where(fn ($query) => $query->where('provider_id', Auth::user()->provider?->id)
                )
                ->ignore($schedule->id), ],
            'start_time' => ['required', 'date_format:H:i,H:i:s'],
            'end_time' => ['required', 'date_format:H:i,H:i:s', 'after:start_time'],
            'slot_duration_minutes' => ['required', 'integer', 'min:30', 'max:1440'],
            'is_active' => ['required', 'boolean'],
        ],
            [
                'start_time.after_or_equal' => 'Start time must be 6:00 AM or later.',
                'end_time.before_or_equal' => 'End time must be 10:00 PM or earlier.',
                'end_time.after' => 'End time must be after start time.',
            ]
        );

        $schedule->update($validated);

        return redirect()->route('provider.schedules.index')->with('success', 'Schedule updated successfully.');
    }
}
