<?php

namespace Database\Factories;

use App\Enums\WorkshopStatus;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Workshop>
 */
class WorkshopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $starts = fake()->dateTimeBetween('+1 day', '+3 weeks')->setTime(fake()->numberBetween(9, 17), 0);

        return [
            'code' => strtoupper(fake()->unique()->bothify('WS-###??')),
            'title' => fake()->randomElement(['Pottery', 'Coding', 'Yoga', 'Watercolour', 'Photography']) . ' ' . fake()->words(2, true),
            'description' => fake()->sentence(12),
            'instructor' => fake()->name(),
            'location' => fake()->randomElement(config('workshops.locations')),
            'starts_at' => $starts,
            'ends_at' => (clone $starts)->modify('+2 hours'),
            'capacity' => 20,
            'status' => WorkshopStatus::SCHEDULED,
            'created_by' => User::factory()->manager(),
        ];
    }

    public function capacity(int $seats): static
    {
        return $this->state(fn () => ['capacity' => $seats]);
    }

    public function status(WorkshopStatus $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }

    public function startingAt(\DateTimeInterface $starts): static
    {
        return $this->state(fn () => [
            'starts_at' => $starts,
            'ends_at' => (clone $starts)->modify('+2 hours'),
        ]);
    }
}
