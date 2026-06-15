<?php

namespace Database\Seeders;

use App\Models\Provider;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Provider::all()->each(function ($provider) {
            Service::factory()
                ->count(rand(1, 5))
                ->create(['provider_id' => $provider->id]);
        });
    }
}
