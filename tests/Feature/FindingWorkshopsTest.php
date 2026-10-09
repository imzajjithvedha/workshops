<?php

use App\Enums\WorkshopStatus;
use App\Models\Registration;
use App\Models\Workshop;

beforeEach(function () {
    apiAs(staff());

    $this->thisWeekOpen = Workshop::factory()->startingAt(now()->addDay()->setTime(10, 0))->capacity(2)->create(['code' => 'OPEN-1']);
    $this->thisWeekFull = Workshop::factory()->startingAt(now()->addDay()->setTime(14, 0))->capacity(1)->create(['code' => 'FULL-1']);
    Registration::factory()->for($this->thisWeekFull)->create();
    Registration::factory()->for($this->thisWeekOpen)->cancelled()->create();

    $this->later = Workshop::factory()->startingAt(now()->addWeeks(3))->create(['code' => 'LATER-1']);
    $this->cancelled = Workshop::factory()->startingAt(now()->addDays(2))->status(WorkshopStatus::CANCELLED)->create(['code' => 'CANC-1']);
});

function codes($response): array
{
    return collect($response->json('data'))->pluck('code')->sort()->values()->all();
}

it('finds workshops this week that still have seats', function () {
    $response = $this->getJson('/api/v1/workshops?' . http_build_query([
        'from' => now()->toDateString(),
        'to' => now()->addDays(6)->toDateString(),
        'has_seats' => 1,
    ]))->assertOk();

    // The cancelled registration on OPEN-1 doesn't take a seat; FULL-1 has
    // none left; CANC-1 has empty seats but can't be booked.
    expect(codes($response))->toBe(['OPEN-1']);
});

it('filters by status', function () {
    expect(codes($this->getJson('/api/v1/workshops?status=cancelled')))->toBe(['CANC-1']);
});

it('filters by date range', function () {
    $response = $this->getJson('/api/v1/workshops?' . http_build_query([
        'from' => now()->addWeeks(2)->toDateString(),
        'to' => now()->addWeeks(4)->toDateString(),
    ]));

    expect(codes($response))->toBe(['LATER-1']);
});

it('reports seats taken and left', function () {
    $this->getJson("/api/v1/workshops/{$this->thisWeekFull->id}")
        ->assertJsonPath('data.seats_taken', 1)
        ->assertJsonPath('data.seats_left', 0);
});

it('ignores malformed filter values instead of failing', function () {
    $this->getJson('/api/v1/workshops?from=next-tuesday&status=nope&has_seats=maybe')
        ->assertOk()
        ->assertJsonCount(4, 'data');
});

it('starts the web list at today and applies the same filters', function () {
    $this->actingAs(staff());
    Workshop::factory()->startingAt(now()->subDays(3))->create(['code' => 'PAST-1']);

    $this->get(route('workshops.index'))
        ->assertOk()
        ->assertSee('OPEN-1')
        ->assertDontSee('PAST-1');

    $this->get(route('workshops.index', ['from' => now()->toDateString(), 'to' => now()->addDays(6)->toDateString(), 'has_seats' => 1, 'status' => 'scheduled']))
        ->assertOk()
        ->assertSee('OPEN-1')
        ->assertDontSee('FULL-1')
        ->assertDontSee('LATER-1');

    $this->get(route('workshops.index', ['from' => '', 'to' => '']))->assertSee('PAST-1');
});
