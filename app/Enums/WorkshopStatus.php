<?php

namespace App\Enums;

enum WorkshopStatus: string
{
    case SCHEDULED = 'scheduled';
    case CANCELLED = 'cancelled';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::SCHEDULED => 'Scheduled',
            self::CANCELLED => 'Cancelled',
            self::COMPLETED => 'Completed',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::SCHEDULED => 'status-active',
            self::CANCELLED => 'status-cancelled',
            self::COMPLETED => 'status-muted',
        };
    }
}
