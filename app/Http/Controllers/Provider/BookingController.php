<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Response;

class BookingController extends Controller
{
    public function index(): Response
    {
        $bookings = Auth::user()->provider->bookings()->with(['customer', 'service'])->orderBy('booking_date', 'desc')->get();

        return inertia('provider/bookings/Index', [
            'bookings' => $bookings,
        ]);
    }
}
