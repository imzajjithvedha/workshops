<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequests\StoreRequest;
use App\Http\Requests\UserRequests\UpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        return UserResource::collection(
            User::query()->filter($request->all())->orderBy('name')->paginate(15)->withQueryString()
        );
    }

    public function show(User $user)
    {
        Gate::authorize('view', $user);

        return new UserResource($user);
    }

    public function store(StoreRequest $request)
    {
        $user = $this->userService->create($request->validated());

        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function update(UpdateRequest $request, User $user)
    {
        return new UserResource($this->userService->update($user, $request->validated(), $request->user()));
    }
}
