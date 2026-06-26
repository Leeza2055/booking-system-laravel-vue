<?php

use App\Http\Controllers\Admin\ProviderController as AdminProviderController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\Provider\BookingController as ProviderBookingController;
use App\Http\Controllers\Provider\ScheduleController as ProviderScheduleController;
use App\Http\Controllers\Provider\ServiceController as ProviderServiceController;
use App\Http\Controllers\ProviderController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');
Route::get('/providers', [ProviderController::class, 'index'])->name('providers.index');
Route::get('/providers/{provider}', [ProviderController::class, 'show'])->name('providers.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
    Route::get('/providers/{provider}/slots', [BookingController::class, 'getSlots'])->name('providers.slots');
});

Route::middleware(['auth', 'verified', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('providers', AdminProviderController::class)->except(['show', 'destroy']);
});

Route::middleware(['auth', 'verified', 'provider'])->prefix('provider')->name('provider.')->group(function () {
    Route::resource('services', ProviderServiceController::class)->except(['show', 'destroy']);
    Route::resource('schedules', ProviderScheduleController::class)->except(['show', 'destroy']);
    Route::get('bookings', [ProviderBookingController::class, 'index'])->name('bookings.index');
});

Route::middleware(['auth', 'verified', 'customer'])->group(function () {
    Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
    Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
});

require __DIR__.'/settings.php';
