<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
    public function create(): Response
    {
        return Inertia::render('admin/providers/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'department' => 'required|string|max:255',
            'specialization' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'base_fee' => 'required|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make('password'), // You should generate a secure password and send it to the provider
        ]);

        $user->forceFill([
            'role' => UserRole::Provider,
            'email_verified_at' => now(),
        ])->save();

        $user->provider()->create([
            'department' => $validated['department'],
            'specialization' => $validated['specialization'],
            'bio' => $validated['bio'] ?? '',
            'base_fee' => $validated['base_fee'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->route('admin.providers.index')
            ->with('success', 'Provider created successfully.');
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
}
