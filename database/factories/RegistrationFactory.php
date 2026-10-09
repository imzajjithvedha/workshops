<?php

namespace Database\Factories;

use App\Enums\RegistrationStatus;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Registration>
 */
class RegistrationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'workshop_id' => Workshop::factory(),
            'attendee_name' => fake()->name(),
            'attendee_email' => fake()->unique()->safeEmail(),
            'status' => RegistrationStatus::ACTIVE,
            'registered_by' => User::factory()->staff(),
            'registered_at' => now(),
        ];
    }

    public function cancelled(?User $by = null): static
    {
        return $this->state(fn () => [
            'status' => RegistrationStatus::CANCELLED,
            'cancelled_by' => $by?->id ?? User::factory()->staff(),
            'cancelled_at' => now(),
        ]);
    }
}
