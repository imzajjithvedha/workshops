<?php

namespace App\Enums;

enum Status: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'status-active',
            self::INACTIVE => 'status-inactive',
        };
    }
}
