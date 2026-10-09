<?php

use App\Models\AuditLog;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Database\Seeders\DatabaseSeeder;

it('logs every registration and cancellation with who did it', function () {
    $user = apiAs(staff());
    $workshop = Workshop::factory()->create();

    $id = $this->postJson("/api/v1/workshops/{$workshop->id}/registrations", [
        'attendee_name' => 'Ada Lovelace',
        'attendee_email' => 'ada@example.com',
    ])->json('data.id');

    $this->patchJson("/api/v1/registrations/{$id}/cancel", ['reason' => 'Called to cancel'])->assertOk();

    $created = AuditLog::where('action', 'registration.created')->where('subject_id', $id)->sole();
    $cancelled = AuditLog::where('action', 'registration.cancelled')->where('subject_id', $id)->sole();

    expect($created->user_id)->toBe($user->id)
        ->and($created->changes['attendee_email'])->toBe([null, 'ada@example.com'])
        ->and($cancelled->user_id)->toBe($user->id)
        ->and($cancelled->changes['status'])->toBe(['active', 'cancelled'])
        ->and($cancelled->changes['cancellation_reason'])->toBe([null, 'Called to cancel']);
});

it('logs a password change without ever storing the password', function () {
    apiAs(admin());
    $user = staff();

    $this->patchJson("/api/v1/users/{$user->id}", [
        'name' => $user->name,
        'email' => $user->email,
        'role' => 'staff',
        'status' => 'active',
        'password' => 'brand-new-password',
        'password_confirmation' => 'brand-new-password',
    ])->assertOk();

    $log = AuditLog::where('action', 'user.updated')->where('subject_id', $user->id)->sole();

    expect($log->changes)->toBe(['password' => ['(hidden)', '(changed)']]);
    expect(json_encode(AuditLog::all()))->not->toContain('brand-new-password')
        ->not->toContain($user->refresh()->password);
});

it('writes nothing when a save changes nothing', function () {
    apiAs(manager());
    $workshop = Workshop::factory()->create();
    $before = AuditLog::count();

    $this->patchJson("/api/v1/workshops/{$workshop->id}", [
        'code' => $workshop->code,
        'title' => $workshop->title,
        'description' => $workshop->description,
        'instructor' => $workshop->instructor,
        'location' => $workshop->location,
        'starts_at' => $workshop->starts_at->format('Y-m-d H:i:s'),
        'ends_at' => $workshop->ends_at->format('Y-m-d H:i:s'),
        'capacity' => $workshop->capacity,
        'status' => $workshop->status->value,
    ])->assertOk();

    expect(AuditLog::count())->toBe($before);
});

it('shows every change on the admin activity log, filterable by area', function () {
    $this->seed(DatabaseSeeder::class);
    $workshop = Workshop::first();
    Registration::factory()->for($workshop)->create(['attendee_name' => 'Ada Lovelace']);

    $this->actingAs(User::firstWhere('email', 'admin@example.com'));

    $this->get(route('admin.activity-logs.index'))
        ->assertOk()
        ->assertSee('Manager (manager@example.com)')
        ->assertSee("{$workshop->code} · {$workshop->title}")
        ->assertSee("Ada Lovelace on {$workshop->code}")
        ->assertSee('Workshop created')
        ->assertSee('User created');

    $this->get(route('admin.activity-logs.index', ['area' => 'workshops']))
        ->assertOk()
        ->assertSee('Workshop created')
        ->assertDontSee('User created')
        ->assertDontSee('Registration created');
});

it('seeds an admin, a manager, a staff member and 5 workshops, with their audit trail', function () {
    $this->seed(DatabaseSeeder::class);

    $admin = User::firstWhere('email', 'admin@example.com');
    $manager = User::firstWhere('email', 'manager@example.com');

    expect(User::pluck('role')->map->value->all())->toBe(['admin', 'manager', 'staff'])
        ->and(Workshop::count())->toBe(5)
        ->and(Registration::count())->toBe(0);

    $logs = AuditLog::orderBy('id')->get();

    expect($logs)->toHaveCount(7)
        ->and($logs->take(2)->pluck('action')->unique()->all())->toBe(['user.created'])
        ->and($logs->take(2)->pluck('user_id')->unique()->all())->toBe([$admin->id])
        ->and($logs->skip(2)->pluck('action')->unique()->values()->all())->toBe(['workshop.created'])
        ->and($logs->skip(2)->pluck('user_id')->unique()->values()->all())->toBe([$manager->id]);
});
