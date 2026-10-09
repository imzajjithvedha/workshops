<?php

use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;

/*
| The access matrix from the brief, enforced by the backend (not just hidden
| in the UI). Each row is checked against both the web routes and the API.
|
|                                     Admin  Manager  Staff
| Create user accounts & set roles     yes     no      no
| Add & edit workshops                  no     yes     no
| Register & cancel attendees           no     yes     yes
| View workshops, registrations, history no    yes     yes
*/

function workshopPayload(array $overrides = []): array
{
    return array_merge([
        'code' => 'TEST-1',
        'title' => 'Test workshop',
        'instructor' => 'Someone',
        'location' => 'Central',
        'starts_at' => now()->addDays(3)->format('Y-m-d H:i'),
        'ends_at' => now()->addDays(3)->addHours(2)->format('Y-m-d H:i'),
        'capacity' => 10,
        'status' => 'scheduled',
    ], $overrides);
}

function userPayload(array $overrides = []): array
{
    return array_merge([
        'name' => 'New Person',
        'email' => 'new.person@example.com',
        'role' => 'staff',
        'status' => 'active',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
    ], $overrides);
}

describe('creating accounts', function () {
    it('allows only admins (API)', function (Closure $role, int $expected) {
        apiAs($role());

        $this->postJson('/api/v1/users', userPayload())->assertStatus($expected);
        $this->getJson('/api/v1/users')->assertStatus($expected === 201 ? 200 : 403);
    })->with([
        'admin' => [fn () => admin(), 201],
        'manager' => [fn () => manager(), 403],
        'staff' => [fn () => staff(), 403],
    ]);

    it('allows only admins (web)', function (Closure $role, bool $allowed) {
        $this->actingAs($role());

        $response = $this->post(route('admin.users.store'), userPayload());

        $allowed ? $response->assertRedirect(route('admin.users.index')) : $response->assertForbidden();
        expect(User::where('email', 'new.person@example.com')->exists())->toBe($allowed);
    })->with([
        'admin' => [fn () => admin(), true],
        'manager' => [fn () => manager(), false],
        'staff' => [fn () => staff(), false],
    ]);
});

describe('adding and editing workshops', function () {
    it('allows only managers (API)', function (Closure $role, int $create, int $update) {
        $workshop = Workshop::factory()->create();
        apiAs($role());

        $this->postJson('/api/v1/workshops', workshopPayload())->assertStatus($create);
        $this->patchJson("/api/v1/workshops/{$workshop->id}", workshopPayload(['code' => $workshop->code]))->assertStatus($update);
    })->with([
        'admin' => [fn () => admin(), 403, 403],
        'manager' => [fn () => manager(), 201, 200],
        'staff' => [fn () => staff(), 403, 403],
    ]);

    it('allows only managers (web)', function (Closure $role, bool $allowed) {
        $this->actingAs($role());

        $response = $this->post(route('workshops.store'), workshopPayload());

        $allowed ? $response->assertRedirect() : $response->assertForbidden();
        expect(Workshop::where('code', 'TEST-1')->exists())->toBe($allowed);
    })->with([
        'admin' => [fn () => admin(), false],
        'manager' => [fn () => manager(), true],
        'staff' => [fn () => staff(), false],
    ]);

    it('hides the create and edit pages from staff', function () {
        $workshop = Workshop::factory()->create();
        $this->actingAs(staff());

        $this->get(route('workshops.create'))->assertForbidden();
        $this->get(route('workshops.edit', $workshop))->assertForbidden();
    });
});

describe('registering and cancelling attendees', function () {
    it('allows managers and staff but not admins (API)', function (Closure $role, int $register, int $cancel) {
        $workshop = Workshop::factory()->create();
        $registration = Registration::factory()->for($workshop)->create();
        apiAs($role());

        $this->postJson("/api/v1/workshops/{$workshop->id}/registrations", [
            'attendee_name' => 'Ada',
            'attendee_email' => 'ada@example.com',
        ])->assertStatus($register);

        $this->patchJson("/api/v1/registrations/{$registration->id}/cancel")->assertStatus($cancel);
    })->with([
        'admin' => [fn () => admin(), 403, 403],
        'manager' => [fn () => manager(), 201, 200],
        'staff' => [fn () => staff(), 201, 200],
    ]);

    it('refuses admins on the web routes', function () {
        $workshop = Workshop::factory()->create();
        $registration = Registration::factory()->for($workshop)->create();
        $this->actingAs(admin());

        $this->post(route('registrations.store', $workshop), [
            'attendee_name' => 'Ada',
            'attendee_email' => 'ada@example.com',
        ])->assertForbidden();

        $this->patch(route('registrations.cancel', [$workshop, $registration]))->assertForbidden();

        expect($workshop->activeRegistrations()->count())->toBe(1);
    });
});

describe('viewing workshops, registrations and history', function () {
    it('allows managers and staff but not admins', function (Closure $role, int $expected) {
        $workshop = Workshop::factory()->create();
        apiAs($role());

        $this->getJson('/api/v1/workshops')->assertStatus($expected);
        $this->getJson("/api/v1/workshops/{$workshop->id}")->assertStatus($expected);
        $this->getJson("/api/v1/workshops/{$workshop->id}/registrations")->assertStatus($expected);
        $this->getJson("/api/v1/workshops/{$workshop->id}/history")->assertStatus($expected);
    })->with([
        'admin' => [fn () => admin(), 403],
        'manager' => [fn () => manager(), 200],
        'staff' => [fn () => staff(), 200],
    ]);

    it('keeps admins off the workshop pages', function () {
        $workshop = Workshop::factory()->create();
        $this->actingAs(admin());

        $this->get(route('workshops.index'))->assertForbidden();
        $this->get(route('workshops.show', $workshop))->assertForbidden();
    });

    it('keeps managers and staff off the admin pages', function (Closure $role) {
        $this->actingAs($role());

        $this->get(route('admin.users.index'))->assertForbidden();
        $this->get(route('admin.activity-logs.index'))->assertForbidden();
    })->with([
        'manager' => [fn () => manager()],
        'staff' => [fn () => staff()],
    ]);
});

describe('authentication', function () {
    it('requires a token for the API', function () {
        $this->getJson('/api/v1/workshops')->assertUnauthorized();
    });

    it('sends guests to the login page', function () {
        $this->get(route('workshops.index'))->assertRedirect(route('login'));
    });

    it('issues a token for valid credentials and refuses bad ones', function () {
        $user = staff();

        $this->postJson('/api/v1/login', ['email' => $user->email, 'password' => 'wrong'])->assertUnprocessable();

        $token = $this->postJson('/api/v1/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()
            ->assertJsonPath('user.role', 'staff')
            ->json('token');

        $this->withToken($token)->getJson('/api/v1/me')->assertOk()->assertJsonPath('data.email', $user->email);
    });

    it('refuses inactive accounts on login, web and API', function () {
        $user = User::factory()->staff()->inactive()->create();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->postJson('/api/v1/login', ['email' => $user->email, 'password' => 'password'])->assertForbidden();

        apiAs($user);
        $this->getJson('/api/v1/workshops')->assertForbidden();
    });

    it('logs out a user whose account is deactivated mid-session', function () {
        $user = staff();
        $this->actingAs($user)->get(route('workshops.index'))->assertOk();

        $user->update(['status' => 'inactive']);

        $this->get(route('workshops.index'))->assertRedirect(route('login'));
        $this->assertGuest();
    });

    it('sends each role to its own home page after login', function (Closure $role, string $route) {
        $user = $role();

        $this->post(route('login.store'), ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route($route));
    })->with([
        'admin' => [fn () => admin(), 'admin.users.index'],
        'manager' => [fn () => manager(), 'workshops.index'],
        'staff' => [fn () => staff(), 'workshops.index'],
    ]);
});
