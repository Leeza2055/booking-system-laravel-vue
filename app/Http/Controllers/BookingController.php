<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Provider;
use App\Models\Service;
use App\Services\SlotGeneratorService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function getSlots(Request $request, Provider $provider): JsonResponse
    {
        $validated = $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
        ]);

        $slots = app(SlotGeneratorService::class)->getAvailableSlots($provider, $validated['booking_date']);

        return response()->json($slots->values());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'provider_id' => 'required|exists:providers,id',
            'service_id' => 'required|exists:services,id',
            'booking_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i,H:i:s',
            'notes' => 'nullable|string|max:1000',
        ]);

        /** @var Provider $provider */
        $provider = Provider::findOrFail($validated['provider_id']);

        /** @var Service $service */
        $service = $provider->services()->findOrFail($validated['service_id']);

        $availableSlots = app(SlotGeneratorService::class)->getAvailableSlots($provider, $validated['booking_date']);

        if ($availableSlots->doesntContain($validated['start_time'])) {
            return back()->withErrors(['start_time' => 'This slot is no longer available.']);
        }

        $endTime = Carbon::parse($validated['start_time'])->addMinutes($service->duration_minutes)->format('H:i');
        Booking::create([
            'provider_id' => $validated['provider_id'],
            'service_id' => $validated['service_id'],
            'customer_id' => Auth::id(),
            'booking_date' => $validated['booking_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $endTime,
            'notes' => $validated['notes'] ?? null,
            'status' => BookingStatus::Pending,
        ]);

        return redirect()->route('dashboard')->with('success', 'Your booking has been created successfully!');
    }
}
