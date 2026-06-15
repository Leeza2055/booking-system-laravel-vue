<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\Provider;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Provider>
 */
class ProviderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $pairs = [
            'General Medicine' => 'General Physician',
            'Cardiology' => 'Cardiologist',
            'Dermatology' => 'Dermatologist',
            'Pediatrics' => 'Pediatrician',
            'Orthopedics' => 'Orthopedic Surgeon',
            'Dental' => 'Dentist',
        ];

        $department = $this->faker->randomElement(array_keys($pairs));

        return [
            'user_id' => User::factory()->create(['role' => UserRole::Provider]),
            'department' => $department,
            'specialization' => $pairs[$department],
            'bio' => $this->faker->paragraph(),
            'base_fee' => $this->faker->randomFloat(2, 500, 2000),
            'is_active' => $this->faker->boolean(80),
        ];
    }
}
