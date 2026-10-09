<?php

use App\Models\Workshop;
use App\Support\RaceRunner;

/*
| The Saturday-morning scenario for real: each booking runs in its own PHP
| process with its own database connection, all released at the same
| instant. (A normal feature test can't show this: its requests run one
| after another inside one process.) Without the row lock in
| RegistrationService this books every attempt; with it, exactly the capacity.
*/

it('never books more seats than the capacity when bookings arrive at the same moment', function () {
    $staff = staff();
    $workshop = Workshop::factory()->capacity(5)->create();

    $result = RaceRunner::run($workshop, $staff, attempts: 20, env: [
        'APP_ENV' => 'testing',
        'DB_DATABASE' => config('database.connections.mysql.database'),
        'CACHE_STORE' => 'array',
    ]);

    expect($result['late'])->toBe(0, 'Some workers missed the starting gun: ' . implode(' | ', $result['outputs']))
        ->and($result['booked'])->toBe(5, implode(' | ', $result['outputs']))
        ->and($result['refused'])->toBe(15)
        ->and($workshop->activeRegistrations()->count())->toBe(5);
})->group('concurrency');
