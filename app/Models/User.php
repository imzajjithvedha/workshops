<?php

namespace App\Models;

use App\Enums\Status;
use App\Enums\UserRole;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'status'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    use Auditable, HasApiTokens, HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'role' => UserRole::class,
            'status' => Status::class,
        ];
    }

    public function canManageUsers(): bool
    {
        return $this->role === UserRole::ADMIN;
    }

    public function canManageWorkshops(): bool
    {
        return $this->role === UserRole::MANAGER;
    }

    public function canHandleRegistrations(): bool
    {
        return in_array($this->role, [UserRole::MANAGER, UserRole::STAFF], true);
    }

    public function isActive(): bool
    {
        return $this->status === Status::ACTIVE;
    }

    public function scopeStatus(Builder $query, Status $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeRole(Builder $query, UserRole $role): Builder
    {
        return $query->where('role', $role);
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['search'] ?? null,
                fn ($q, $value) =>
                    $q->where(fn ($q) => $q
                        ->where('name', 'like', "%{$value}%")
                        ->orWhere('email', 'like', "%{$value}%")))

            ->when(UserRole::tryFrom((string) ($filters['role'] ?? '')),
                fn ($q, $value) =>
                    $q->where('role', $value))

            ->when(Status::tryFrom((string) ($filters['status'] ?? '')),
                fn ($q, $value) =>
                    $q->where('status', $value));
    }
}
