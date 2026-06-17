<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $services = Auth::user()->provider?->services()->latest()->get() ?? collect();

        return Inertia::render('provider/services/Index', [
            'services' => $services,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('provider/services/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        Auth::user()->provider->services()->create($validated);

        return redirect()->route('provider.services.index')->with('success', 'Service created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service): Response
    {
        // Ensure this service belongs to the logged-in provider
        if ($service->provider_id !== Auth::user()->provider?->id) {
            abort(403);
        }

        return Inertia::render('provider/services/Edit', [
            'service' => $service,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service): RedirectResponse
    {
        // Ensure this service belongs to the logged-in provider
        if ($service->provider_id !== Auth::user()->provider?->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_minutes' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $service->update($validated);

        return redirect()->route('provider.services.index')->with('success', 'Service updated successfully.');
    }
}
