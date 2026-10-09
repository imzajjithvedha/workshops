<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\WorkshopRequests\BaseRequest;
use App\Http\Resources\AuditLogResource;
use App\Http\Resources\WorkshopResource;
use App\Models\Workshop;
use App\Services\WorkshopService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WorkshopController extends Controller
{
    public function __construct(
        protected WorkshopService $workshopService
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Workshop::class);

        $items = Workshop::query()
                ->withSeatsTaken()
                ->filter($request->all())
                ->orderBy('starts_at')
                ->paginate(min(100, max(1, $request->integer('per_page', 15))))
                ->withQueryString();

        return WorkshopResource::collection($items);
    }

    public function show(Workshop $workshop)
    {
        Gate::authorize('view', $workshop);

        return new WorkshopResource(
            $workshop->loadCount(['activeRegistrations as seats_taken'])->load(['creator', 'editor'])
        );
    }

    public function store(BaseRequest $request)
    {
        $workshop = $this->workshopService->create($request->validated(), $request->user());

        return (new WorkshopResource($workshop->loadCount(['activeRegistrations as seats_taken'])))
            ->response()
            ->setStatusCode(201);
    }

    public function update(BaseRequest $request, Workshop $workshop)
    {
        $workshop = $this->workshopService->update($workshop, $request->validated(), $request->user());

        return new WorkshopResource($workshop->loadCount(['activeRegistrations as seats_taken']));
    }

    public function history(Workshop $workshop)
    {
        Gate::authorize('view', $workshop);

        return AuditLogResource::collection($workshop->auditLogs()->with('user')->get());
    }
}
