<?php

namespace App\Models;

use App\Enums\RegistrationStatus;
use App\Enums\WorkshopStatus;
use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

#[Fillable(['code', 'title', 'description', 'instructor', 'location', 'starts_at', 'ends_at', 'capacity', 'status', 'created_by', 'updated_by'])]
class Workshop extends Model
{
    use Auditable, HasFactory;

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'capacity' => 'integer',
            'status' => WorkshopStatus::class,
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function activeRegistrations(): HasMany
    {
        return $this->registrations()->where('status', RegistrationStatus::ACTIVE);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function seatsTaken(): int
    {
        return $this->seats_taken ?? $this->activeRegistrations()->count();
    }

    public function seatsLeft(): int
    {
        return max(0, $this->capacity - $this->seatsTaken());
    }

    public function hasStarted(): bool
    {
        return $this->starts_at->isPast();
    }

    public function isOpenForRegistration(): bool
    {
        return $this->status === WorkshopStatus::SCHEDULED && ! $this->hasStarted();
    }

    public function scopeWithSeatsTaken(Builder $query): Builder
    {
        return $query->withCount(['activeRegistrations as seats_taken']);
    }

    public function scopeHasSeats(Builder $query): Builder
    {
        return $query
            ->where('status', WorkshopStatus::SCHEDULED)
            ->where('starts_at', '>', now())
            ->whereRaw(
            'capacity > (select count(*) from registrations where registrations.workshop_id = workshops.id and registrations.status = ?)',
            [RegistrationStatus::ACTIVE->value],
        );
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when($filters['search'] ?? null,
                fn ($q, $value) =>
                    $q->where(fn ($q) => $q
                        ->where('code', 'like', "%{$value}%")
                        ->orWhere('title', 'like', "%{$value}%")
                        ->orWhere('instructor', 'like', "%{$value}%")))

            ->when($filters['location'] ?? null,
                fn ($q, $value) =>
                    $q->where('location', $value))

            ->when(WorkshopStatus::tryFrom((string) ($filters['status'] ?? '')),
                fn ($q, $value) =>
                    $q->where('status', $value))

            ->when(self::parseDate($filters['from'] ?? null),
                fn ($q, $value) =>
                    $q->where('starts_at', '>=', $value->startOfDay()))

            ->when(self::parseDate($filters['to'] ?? null),
                fn ($q, $value) =>
                    $q->where('starts_at', '<=', $value->endOfDay()))

            ->when(filter_var($filters['has_seats'] ?? false, FILTER_VALIDATE_BOOLEAN),
                fn ($q) =>
                    $q->hasSeats());
    }

    private static function parseDate(?string $value): ?Carbon
    {
        if (! $value || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        try {
            return Carbon::createFromFormat('Y-m-d', $value);
        } catch (\Throwable) {
            return null;
        }
    }
}
