<?php

namespace App\Services;

use App\Enums\RegistrationStatus;
use App\Exceptions\BusinessRuleException;
use App\Models\Registration;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RegistrationService
{
    public function register(Workshop $workshop, array $attendee, User $by): Registration
    {
        $email = Str::lower(trim($attendee['attendee_email']));

        try {
            return DB::transaction(function () use ($workshop, $attendee, $email, $by) {
                $workshop = Workshop::query()->lockForUpdate()->findOrFail($workshop->id);

                if (! $workshop->isOpenForRegistration()) {
                    throw new BusinessRuleException($workshop->hasStarted()
                        ? 'This workshop has already started, so it no longer takes registrations.'
                        : "This workshop is {$workshop->status->label()}, so it doesn't take registrations.");
                }

                if ($workshop->activeRegistrations()->where('attendee_email', $email)->exists()) {
                    throw new BusinessRuleException("{$email} is already registered for this workshop.");
                }

                if ($workshop->activeRegistrations()->count() >= $workshop->capacity) {
                    throw BusinessRuleException::workshopFull();
                }

                return $workshop->registrations()->create([
                    'attendee_name' => trim($attendee['attendee_name']),
                    'attendee_email' => $email,
                    'status' => RegistrationStatus::ACTIVE,
                    'registered_by' => $by->id,
                    'registered_at' => now(),
                ]);
            });
        } catch (UniqueConstraintViolationException) {
            throw new BusinessRuleException("{$email} is already registered for this workshop.");
        }
    }

    public function cancel(Registration $registration, User $by, ?string $reason = null): Registration
    {
        return DB::transaction(function () use ($registration, $by, $reason) {
            $registration = Registration::query()->lockForUpdate()->findOrFail($registration->id);

            if (! $registration->isActive()) {
                throw new BusinessRuleException('This registration has already been cancelled.');
            }

            $registration->update([
                'status' => RegistrationStatus::CANCELLED,
                'cancelled_by' => $by->id,
                'cancelled_at' => now(),
                'cancellation_reason' => $reason ? trim($reason) : null,
            ]);

            return $registration;
        });
    }
}
