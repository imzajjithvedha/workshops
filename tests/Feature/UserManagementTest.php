<?php

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

function accountData(array $overrides = []): array
{
    return array_merge([
        'name' => 'Riley Front Desk',
        'email' => 'Riley@Example.com',
        'role' => 'staff',
        'status' => 'active',
        'password' => 'secret-password',
        'password_confirmation' => 'secret-password',
    ], $overrides);
}

it('lets an admin create an account with a role', function () {
    $admin = apiAs(admin());

    $this->postJson('/api/v1/users', accountData(['role' => 'manager']))
        ->assertCreated()
        ->assertJsonPath('data.email', 'riley@example.com')
        ->assertJsonPath('data.role', 'manager')
        ->assertJsonMissingPath('data.password');

    $user = User::firstWhere('email', 'riley@example.com');
    expect(Hash::check('secret-password', $user->password))->toBeTrue();
    expect(AuditLog::where('action', 'user.created')->where('user_id', $admin->id)->exists())->toBeTrue();
});

it('records role changes in the audit trail', function () {
    $admin = apiAs(admin());
    $user = staff();

    $this->patchJson("/api/v1/users/{$user->id}", accountData(['email' => $user->email, 'name' => $user->name, 'role' => 'manager', 'password' => '', 'password_confirmation' => '']))
        ->assertOk()
        ->assertJsonPath('data.role', 'manager');

    $log = AuditLog::where('action', 'user.updated')->sole();
    expect($log->user_id)->toBe($admin->id)
        ->and($log->changes)->toBe(['role' => ['staff', 'manager']]);
});

it('keeps the password when left blank on edit', function () {
    apiAs(admin());
    $user = staff();
    $hash = $user->password;

    $this->patchJson("/api/v1/users/{$user->id}", accountData(['email' => $user->email, 'password' => null, 'password_confirmation' => null]))->assertOk();

    expect($user->refresh()->password)->toBe($hash);
});

it('stops an admin demoting or deactivating themselves', function (array $change) {
    $admin = apiAs(admin());

    $this->patchJson("/api/v1/users/{$admin->id}", accountData(['email' => $admin->email, 'password' => null, 'password_confirmation' => null, ...$change]))
        ->assertUnprocessable()
        ->assertJsonPath('message', "You can't change your own role or deactivate your own account.");

    expect($admin->refresh()->role)->toBe(UserRole::ADMIN)
        ->and($admin->status)->toBe(Status::ACTIVE);
})->with([
    'demote' => [['role' => 'staff']],
    'deactivate' => [['role' => 'admin', 'status' => 'inactive']],
]);

it('revokes API tokens when an account is deactivated', function () {
    $user = staff();
    $token = $user->createToken('desk')->plainTextToken;
    apiAs(admin());

    $this->patchJson("/api/v1/users/{$user->id}", accountData(['email' => $user->email, 'status' => 'inactive', 'password' => null, 'password_confirmation' => null]))->assertOk();

    expect($user->tokens()->count())->toBe(0);

    app('auth')->forgetGuards();
    $this->withToken($token)->getJson('/api/v1/workshops')->assertUnauthorized();
});

it('refuses a duplicate email', function () {
    apiAs(admin());
    User::factory()->create(['email' => 'riley@example.com']);

    $this->postJson('/api/v1/users', accountData(['email' => 'riley@example.com']))->assertJsonValidationErrors('email');
});

it('manages accounts through the web pages', function () {
    $this->actingAs(admin());

    $this->get(route('admin.users.index'))->assertOk();
    $this->get(route('admin.users.create'))->assertOk();
    $this->post(route('admin.users.store'), accountData())->assertRedirect(route('admin.users.index'));

    $user = User::firstWhere('email', 'riley@example.com');
    $this->get(route('admin.users.edit', $user))->assertOk()->assertSee('Riley Front Desk');
    $this->get(route('admin.activity-logs.index'))->assertOk()->assertSee('User created');
});

it('has no public sign-up route', function () {
    $this->get('/register')->assertNotFound();
    $this->postJson('/api/v1/register', accountData())->assertNotFound();
});
