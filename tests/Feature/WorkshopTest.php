<?php

use App\Models\AuditLog;
use App\Models\Registration;
use App\Models\Workshop;

function workshopData(array $overrides = []): array
{
    return array_merge([
        'code' => 'pot-101',
        'title' => 'Wheel Throwing',
        'instructor' => 'Hana Kimura',
        'location' => 'Central',
        'starts_at' => now()->addDays(3)->format('Y-m-d H:i'),
        'ends_at' => now()->addDays(3)->addHours(2)->format('Y-m-d H:i'),
        'capacity' => 12,
        'status' => 'scheduled',
    ], $overrides);
}

it('creates a workshop, normalising the code and recording the creator', function () {
    $user = apiAs(manager());

    $this->postJson('/api/v1/workshops', workshopData())
        ->assertCreated()
        ->assertJsonPath('data.code', 'POT-101')
        ->assertJsonPath('data.seats_left', 12);

    $workshop = Workshop::firstWhere('code', 'POT-101');
    expect($workshop->created_by)->toBe($user->id);
    expect(AuditLog::where('action', 'workshop.created')->where('subject_id', $workshop->id)->exists())->toBeTrue();
});

it('records who changed what when a workshop is edited', function () {
    $user = apiAs(manager());
    $workshop = Workshop::factory()->capacity(10)->create(['code' => 'POT-101']);

    $this->patchJson("/api/v1/workshops/{$workshop->id}", workshopData(['capacity' => 14, 'title' => $workshop->title, 'instructor' => $workshop->instructor]))
        ->assertOk();

    $log = AuditLog::where('action', 'workshop.updated')->sole();
    expect($log->user_id)->toBe($user->id)
        ->and($log->changes['capacity'])->toBe([10, 14])
        ->and($workshop->refresh()->updated_by)->toBe($user->id);

    $this->getJson("/api/v1/workshops/{$workshop->id}/history")
        ->assertOk()
        ->assertJsonPath('data.0.action', 'workshop.updated')
        ->assertJsonPath('data.0.changes.capacity', [10, 14]);
});

it('refuses lowering capacity below the seats already taken', function () {
    apiAs(manager());
    $workshop = Workshop::factory()->capacity(5)->create(['code' => 'POT-101']);
    Registration::factory()->for($workshop)->count(4)->create();

    $this->patchJson("/api/v1/workshops/{$workshop->id}", workshopData(['capacity' => 3]))
        ->assertUnprocessable()
        ->assertJsonPath('message', "Capacity can't be lower than the 4 seats already taken. Cancel registrations first.");

    $this->patchJson("/api/v1/workshops/{$workshop->id}", workshopData(['capacity' => 4]))->assertOk();
});

it('validates workshops', function (array $data, string $field) {
    apiAs(manager());
    Workshop::factory()->create(['code' => 'TAKEN-1']);

    $this->postJson('/api/v1/workshops', workshopData($data))->assertJsonValidationErrors($field);
})->with([
    'duplicate code' => [['code' => 'taken-1'], 'code'],
    'bad code' => [['code' => 'pot 101!'], 'code'],
    'end before start' => [['ends_at' => now()->addDays(2)->format('Y-m-d H:i')], 'ends_at'],
    'zero capacity' => [['capacity' => 0], 'capacity'],
    'unknown location' => [['location' => 'Mars'], 'location'],
    'unknown status' => [['status' => 'postponed'], 'status'],
]);

it('creates and edits through the web forms', function () {
    $this->actingAs(manager());

    $this->post(route('workshops.store'), workshopData())->assertRedirect();
    $workshop = Workshop::firstWhere('code', 'POT-101');

    $this->get(route('workshops.edit', $workshop))->assertOk()->assertSee('POT-101');

    $this->patch(route('workshops.update', $workshop), workshopData(['title' => 'Wheel Throwing II']))
        ->assertRedirect(route('workshops.show', $workshop));

    expect($workshop->refresh()->title)->toBe('Wheel Throwing II');
});

it('renders the workshop page with seats and attendees', function () {
    $this->actingAs(staff());
    $workshop = Workshop::factory()->capacity(3)->create();
    $registration = Registration::factory()->for($workshop)->create();

    $this->get(route('workshops.show', $workshop))
        ->assertOk()
        ->assertSee($workshop->title)
        ->assertSee($registration->attendee_name)
        ->assertSee('2 <span', false)
        ->assertDontSee('Edit</span>', false);
});
