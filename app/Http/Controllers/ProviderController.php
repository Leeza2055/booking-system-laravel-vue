<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use Inertia\Inertia;
use Inertia\Response;

class ProviderController extends Controller
{
    public function index(): Response
    {
        $providers = Provider::with('user')
            ->where('is_active', true)
            ->latest()
            ->get();

        return Inertia::render('providers/Index', [
            'providers' => $providers,
        ]);
    }

    public function show(Provider $provider): Response
    {
        $provider->load(['user', 'services' => function ($query) {
            $query->where('is_active', true);
        }]);

        return Inertia::render('providers/Show', [
            'provider' => $provider,
        ]);
    }
}
