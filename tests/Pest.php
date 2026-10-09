<?php

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

// The concurrency test needs committed rows that other PHP processes can
// see, so it truncates instead of wrapping each test in a transaction.
pest()->extend(TestCase::class)
    ->use(DatabaseTruncation::class)
    ->in('Concurrency');

function admin(): User
{
    return User::factory()->admin()->create();
}

function manager(): User
{
    return User::factory()->manager()->create();
}

function staff(): User
{
    return User::factory()->staff()->create();
}

/**
 * Authenticate API calls as $user (Sanctum token guard).
 */
function apiAs(User $user): User
{
    Sanctum::actingAs($user);

    return $user;
}
