<?php

use App\Enums\RegistrationStatus;
use App\Enums\WorkshopStatus;
use App\Models\AuditLog;
use App\Models\Registration;
use App\Models\Workshop;

function book(Workshop $workshop, string $email = 'ada@example.com', string $name = 'Ada Lovelace')
{
    return test()->postJson("/api/v1/workshops/{$workshop->id}/registrations", [
        'attendee_name' => $name,
        'attendee_email' => $email,
    ]);
}

it('registers an attendee and records who did it', function () {
    $user = apiAs(staff());
    $workshop = Workshop::factory()->capacity(3)->create();

    book($workshop)
        ->assertCreated()
        ->assertJsonPath('data.status', 'active')
        ->assertJsonPath('data.registered_by.id', $user->id);

    expect($workshop->activeRegistrations()->count())->toBe(1);
    expect(AuditLog::where('action', 'registration.created')->count())->toBe(1);
});

it('refuses a booking once the workshop is full, with 409', function () {
    apiAs(staff());
    $workshop = Workshop::factory()->capacity(2)->create();

    book($workshop, 'one@example.com')->assertCreated();
    book($workshop, 'two@example.com')->assertCreated();
    book($workshop, 'three@example.com')
        ->assertStatus(409)
        ->assertJsonPath('message', 'This workshop is full. No seats are left.');

    expect($workshop->activeRegistrations()->count())->toBe(2);
});

it('shows the full message on the web page instead of an error', function () {
    $this->actingAs(staff());
    $workshop = Workshop::factory()->capacity(1)->create();
    Registration::factory()->for($workshop)->create();

    $this->from(route('workshops.show', $workshop))
        ->post(route('registrations.store', $workshop), ['attendee_name' => 'Late', 'attendee_email' => 'late@example.com'])
        ->assertRedirect(route('workshops.show', $workshop))
        ->assertSessionHas('error')
        ->assertSessionHas('message', 'This workshop is full. No seats are left.');

    expect($workshop->activeRegistrations()->count())->toBe(1);
});

it('refuses the same email twice on one workshop, ignoring case', function () {
    apiAs(staff());
    $workshop = Workshop::factory()->create();

    book($workshop, 'ada@example.com')->assertCreated();
    book($workshop, 'ADA@Example.com ')->assertUnprocessable()->assertJsonPath('message', 'ada@example.com is already registered for this workshop.');
});

it('allows re-registering an email after its registration was cancelled', function () {
    apiAs(staff());
    $workshop = Workshop::factory()->create();

    $id = book($workshop)->json('data.id');
    $this->patchJson("/api/v1/registrations/{$id}/cancel")->assertOk();

    book($workshop)->assertCreated();
    expect($workshop->registrations()->count())->toBe(2);
});

it('frees the seat on cancel but keeps the record with who and when', function () {
    $user = apiAs(manager());
    $workshop = Workshop::factory()->capacity(1)->create();
    $registration = Registration::factory()->for($workshop)->create();

    book($workshop, 'waiting@example.com')->assertStatus(409);

    $this->patchJson("/api/v1/registrations/{$registration->id}/cancel", ['reason' => 'Called to cancel'])
        ->assertOk()
        ->assertJsonPath('data.status', 'cancelled')
        ->assertJsonPath('data.cancelled_by.id', $user->id)
        ->assertJsonPath('data.cancellation_reason', 'Called to cancel');

    $registration->refresh();
    expect($registration->status)->toBe(RegistrationStatus::CANCELLED)
        ->and($registration->cancelled_at)->not->toBeNull()
        ->and(Registration::count())->toBe(1);

    book($workshop, 'waiting@example.com')->assertCreated();
});

it('refuses cancelling a registration twice', function () {
    apiAs(staff());
    $registration = Registration::factory()->cancelled()->create();

    $this->patchJson("/api/v1/registrations/{$registration->id}/cancel")
        ->assertUnprocessable()
        ->assertJsonPath('message', 'This registration has already been cancelled.');
});

it('lists the full history, cancelled registrations included', function () {
    apiAs(staff());
    $workshop = Workshop::factory()->create();
    Registration::factory()->for($workshop)->count(2)->create();
    Registration::factory()->for($workshop)->cancelled()->create();

    $this->getJson("/api/v1/workshops/{$workshop->id}/registrations")->assertOk()->assertJsonCount(3, 'data');
    $this->getJson("/api/v1/workshops/{$workshop->id}/registrations?status=cancelled")
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.cancelled_by.name', fn ($name) => filled($name));
});

it('shows cancelled registrations and who cancelled them on the history tab', function () {
    $this->actingAs(staff());
    $workshop = Workshop::factory()->create();
    $registration = Registration::factory()->for($workshop)->cancelled()->create();

    $this->get(route('workshops.show', [$workshop, 'tab' => 'history']))
        ->assertOk()
        ->assertSee($registration->attendee_name)
        ->assertSee($registration->cancelledBy->name)
        ->assertSee('Cancelled');
});

it('refuses bookings for cancelled, completed or already-started workshops', function (Closure $workshop, string $message) {
    apiAs(staff());

    book($workshop())->assertUnprocessable()->assertJsonPath('message', $message);
})->with([
    'cancelled' => [fn () => Workshop::factory()->status(WorkshopStatus::CANCELLED)->create(), "This workshop is Cancelled, so it doesn't take registrations."],
    'completed' => [fn () => Workshop::factory()->status(WorkshopStatus::COMPLETED)->create(), "This workshop is Completed, so it doesn't take registrations."],
    'started' => [fn () => Workshop::factory()->startingAt(now()->subMinutes(5))->create(), 'This workshop has already started, so it no longer takes registrations.'],
]);

it('validates the attendee', function () {
    apiAs(staff());
    $workshop = Workshop::factory()->create();

    $this->postJson("/api/v1/workshops/{$workshop->id}/registrations", ['attendee_name' => '', 'attendee_email' => 'not-an-email'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['attendee_name', 'attendee_email']);
});

it('cancels through the web form and stays on the workshop page', function () {
    $this->actingAs(staff());
    $workshop = Workshop::factory()->create();
    $registration = Registration::factory()->for($workshop)->create();

    $this->patch(route('registrations.cancel', [$workshop, $registration]), ['reason' => 'No-show'])
        ->assertRedirect(route('workshops.show', $workshop))
        ->assertSessionHas('success');

    expect($registration->refresh()->status)->toBe(RegistrationStatus::CANCELLED);
});

it('404s a registration cancelled through another workshop\'s URL', function () {
    $this->actingAs(staff());
    $registration = Registration::factory()->create();
    $other = Workshop::factory()->create();

    $this->patch(route('registrations.cancel', [$other, $registration]))->assertNotFound();
});
