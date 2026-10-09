<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use App\Models\Concerns\Auditable;
use Database\Factories\RegistrationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['workshop_id', 'attendee_name', 'attendee_email', 'status', 'registered_by', 'registered_at', 'cancelled_by', 'cancelled_at', 'cancellation_reason'])]
class Registration extends Model
{
    use Auditable, HasFactory {
        Auditable::auditAction as baseAuditAction;
    }

    public function auditAction(string $event): string
    {
        if ($event === 'updated' && $this->wasChanged('status') && $this->status === RegistrationStatus::CANCELLED) {
            return 'registration.cancelled';
        }

        return $this->baseAuditAction($event);
    }

    protected function casts(): array
    {
        return [
            'status' => RegistrationStatus::class,
            'registered_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function workshop(): BelongsTo
    {
        return $this->belongsTo(Workshop::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function isActive(): bool
    {
        return $this->status === RegistrationStatus::ACTIVE;
    }
}
