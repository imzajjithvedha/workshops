<?php

namespace App\Http\Controllers;

use App\Enums\RegistrationStatus;
use App\Http\Requests\WorkshopRequests\BaseRequest;
use App\Models\Workshop;
use App\Services\WorkshopService;
use App\Traits\ResponseRedirectTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WorkshopController extends Controller
{
    use ResponseRedirectTrait;

    public function __construct(
        protected WorkshopService $workshopService
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('viewAny', Workshop::class);

        $filter = $request->all();
        if (! $request->has('from') && ! $request->has('to')) {
            $filter['from'] = today()->toDateString();
        }

        $items = Workshop::query()
                ->withSeatsTaken()
                ->filter($filter)
                ->orderBy('starts_at')
                ->paginate(15)
                ->withQueryString();

        return view('workshops.index', [
            'items' => $items,
            'filter' => $filter,
        ]);
    }

    public function show(Request $request, Workshop $workshop)
    {
        Gate::authorize('view', $workshop);

        $workshop->loadCount(['activeRegistrations as seats_taken'])
            ->load(['creator', 'editor']);

        $tab = $request->query('tab') === 'history' ? 'history' : 'attendees';

        $registrations = $workshop->registrations()
            ->with(['registeredBy', 'cancelledBy'])
            ->when($tab === 'attendees', fn ($q) => $q->where('status', RegistrationStatus::ACTIVE))
            ->latest('registered_at')
            ->latest('id')
            ->get();

        $changes = $tab === 'history'
            ? $workshop->auditLogs()->with('user')->get()
            : collect();

        return view('workshops.show', compact('workshop', 'registrations', 'changes', 'tab'));
    }

    public function create()
    {
        Gate::authorize('create', Workshop::class);

        return view('workshops.create');
    }

    public function store(BaseRequest $request)
    {
        $workshop = $this->workshopService->create($request->validated(), $request->user());

        return $this->redirectWithMessage(
            'workshops.show',
            'success',
            'Workshop created',
            "{$workshop->code} is now in the catalogue.",
            $workshop
        );
    }

    public function edit(Workshop $workshop)
    {
        Gate::authorize('update', $workshop);

        return view('workshops.edit', compact('workshop'));
    }

    public function update(BaseRequest $request, Workshop $workshop)
    {
        $this->workshopService->update($workshop, $request->validated(), $request->user());

        return $this->redirectWithMessage(
            'workshops.show',
            'success',
            'Workshop updated',
            'Your changes have been saved.',
            $workshop
        );
    }
}
