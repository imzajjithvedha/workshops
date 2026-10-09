<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegistrationRequests\CancelRequest;
use App\Http\Requests\RegistrationRequests\StoreRequest;
use App\Models\Registration;
use App\Models\Workshop;
use App\Services\RegistrationService;
use App\Traits\ResponseRedirectTrait;

class RegistrationController extends Controller
{
    use ResponseRedirectTrait;

    public function __construct(
        protected RegistrationService $registrationService
    ) {}

    public function store(StoreRequest $request, Workshop $workshop)
    {
        $registration = $this->registrationService->register($workshop, $request->validated(), $request->user());

        return $this->redirectWithMessage(
            'workshops.show',
            'success',
            'Attendee registered',
            "{$registration->attendee_name} has a seat on {$workshop->title}.",
            $workshop
        );
    }

    public function cancel(CancelRequest $request, Workshop $workshop, Registration $registration)
    {
        $this->registrationService->cancel($registration, $request->user(), $request->validated('reason'));

        return $this->redirectWithMessage(
            'workshops.show',
            'success',
            'Registration cancelled',
            "{$registration->attendee_name}'s seat has been freed. The registration stays in the history.",
            $workshop
        );
    }
}
