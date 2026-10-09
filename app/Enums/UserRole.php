<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case MANAGER = 'manager';
    case STAFF = 'staff';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'Admin',
            self::MANAGER => 'Manager',
            self::STAFF => 'Staff',
        };
    }

    public function dashboardRoute(): string
    {
        return match ($this) {
            self::ADMIN => 'admin.users.index',
            self::MANAGER, self::STAFF => 'workshops.index',
        };
    }
}
