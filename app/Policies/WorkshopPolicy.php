<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Workshop;

class WorkshopPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canHandleRegistrations();
    }

    public function view(User $user, Workshop $workshop): bool
    {
        return $user->canHandleRegistrations();
    }

    public function create(User $user): bool
    {
        return $user->canManageWorkshops();
    }

    public function update(User $user, Workshop $workshop): bool
    {
        return $user->canManageWorkshops();
    }

    public function register(User $user, Workshop $workshop): bool
    {
        return $user->canHandleRegistrations();
    }
}
