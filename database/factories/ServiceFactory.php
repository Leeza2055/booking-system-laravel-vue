<?php

namespace Database\Factories;

use App\Models\Provider;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    /** @var array<string, array<int, string>> */
    private array $servicesBySpecialization = [
        'General Physician' => ['General Consultation', 'Follow-up Visit', 'Health Checkup'],
        'Cardiologist' => ['Cardiac Consultation', 'ECG', 'Stress Test'],
        'Dermatologist' => ['Skin Consultation', 'Acne Treatment', 'Mole Removal'],
        'Pediatrician' => ['Child Checkup', 'Vaccination', 'Growth Assessment'],
        'Orthopedic Surgeon' => ['Joint Consultation', 'Fracture Review', 'Physiotherapy Session'],
        'Dentist' => ['Dental Checkup', 'Teeth Cleaning', 'Cavity Filling'],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider_id' => Provider::factory(),
            'name' => 'Constultation',
            'description' => $this->faker->paragraph(),
            'duration_minutes' => $this->faker->numberBetween(15, 120),
            'price' => $this->faker->randomFloat(2, 50, 500),
            'is_active' => $this->faker->boolean(80),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (Service $service) {
            $provider = $service->provider;
            $options = $this->servicesBySpecialization[$provider->specialization] ?? ['General Consultation'];
            $service->name = $this->faker->randomElement($options);
        });
    }
}
