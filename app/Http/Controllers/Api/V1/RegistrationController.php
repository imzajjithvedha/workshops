<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\RegistrationStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegistrationRequests\CancelRequest;
use App\Http\Requests\RegistrationRequests\StoreRequest;
use App\Http\Resources\RegistrationResource;
use App\Models\Registration;
use App\Models\Workshop;
use App\Services\RegistrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class RegistrationController extends Controller
{
    public function __construct(
        protected RegistrationService $registrationService
    ) {}

    public function index(Request $request, Workshop $workshop)
    {
        Gate::authorize('view', $workshop);

        $items = $workshop->registrations()
                ->with(['registeredBy', 'cancelledBy'])
                ->when(RegistrationStatus::tryFrom((string) $request->query('status')),
                    fn ($q, $status) => $q->where('status', $status))
                ->latest('registered_at')
                ->latest('id')
                ->get();

        return RegistrationResource::collection($items);
    }

    public function store(StoreRequest $request, Workshop $workshop)
    {
        $registration = $this->registrationService->register($workshop, $request->validated(), $request->user());

        return (new RegistrationResource($registration->load('registeredBy')))
            ->response()
            ->setStatusCode(201);
    }

    public function cancel(CancelRequest $request, Registration $registration)
    {
        $registration = $this->registrationService->cancel($registration, $request->user(), $request->validated('reason'));

        return new RegistrationResource($registration->load(['registeredBy', 'cancelledBy']));
    }
}
