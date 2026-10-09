<?php

namespace Database\Factories;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::STAFF,
            'status' => Status::ACTIVE,
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => UserRole::ADMIN]);
    }

    public function manager(): static
    {
        return $this->state(fn () => ['role' => UserRole::MANAGER]);
    }

    public function staff(): static
    {
        return $this->state(fn () => ['role' => UserRole::STAFF]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['status' => Status::INACTIVE]);
    }
}
