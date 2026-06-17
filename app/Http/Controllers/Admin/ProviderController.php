<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $providers = Provider::with('user')->latest()->get();

        return Inertia::render('admin/providers/Index', [
            'providers' => $providers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('admin/providers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Provider $provider): Response
    {
        $provider->load('user');

        return Inertia::render('admin/providers/Edit', [
            'provider' => $provider,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Provider $provider): RedirectResponse
    {
        $validated = $request->validate([
            'department' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'base_fee' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $provider->update($validated);

        return redirect()->route('admin.providers.index')
            ->with('success', 'Provider updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
