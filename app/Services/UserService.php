<?php

namespace App\Services;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Exceptions\BusinessRuleException;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserService
{
    public function create(array $data): User
    {
        return User::create([
            ...$data,
            'email' => Str::lower($data['email']),
        ]);
    }

    public function update(User $user, array $data, User $by): User
    {
        if (empty($data['password'])) {
            unset($data['password']);
        }

        return DB::transaction(function () use ($user, $data, $by) {
            $user = User::query()->lockForUpdate()->findOrFail($user->id);

            $role = UserRole::from($data['role']);
            $status = Status::from($data['status']);

            if ($user->is($by) && ($role !== UserRole::ADMIN || $status !== Status::ACTIVE)) {
                throw new BusinessRuleException("You can't change your own role or deactivate your own account.");
            }

            $user->update([
                ...$data,
                'email' => Str::lower($data['email']),
            ]);

            if ($user->status === Status::INACTIVE) {
                $user->tokens()->delete();
            }

            return $user;
        });
    }
}
