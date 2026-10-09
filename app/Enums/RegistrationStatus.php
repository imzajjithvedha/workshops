<?php

namespace App\Enums;

enum RegistrationStatus: string
{
    case ACTIVE = 'active';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Active',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::ACTIVE => 'status-active',
            self::CANCELLED => 'status-cancelled',
        };
    }
}
