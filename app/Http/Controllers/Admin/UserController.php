<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequests\StoreRequest;
use App\Http\Requests\UserRequests\UpdateRequest;
use App\Models\User;
use App\Services\UserService;
use App\Traits\ResponseRedirectTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class UserController extends Controller
{
    use ResponseRedirectTrait;

    public function __construct(
        protected UserService $userService
    ) {}

    public function index(Request $request)
    {
        Gate::authorize('viewAny', User::class);

        $items = User::query()
                ->filter($request->all())
                ->orderBy('name')
                ->paginate(15)
                ->withQueryString();

        return view('admin.users.index', [
            'items' => $items,
            'filter' => $request->all(),
        ]);
    }

    public function create()
    {
        Gate::authorize('create', User::class);

        return view('admin.users.create');
    }

    public function store(StoreRequest $request)
    {
        $user = $this->userService->create($request->validated());

        return $this->redirectWithMessage(
            'admin.users.index',
            'success',
            'User created',
            "{$user->name} can now log in as {$user->role->label()}."
        );
    }

    public function edit(User $user)
    {
        Gate::authorize('update', $user);

        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateRequest $request, User $user)
    {
        $this->userService->update($user, $request->validated(), $request->user());

        return $this->redirectWithMessage(
            'admin.users.index',
            'success',
            'User updated',
            'The account has been successfully updated.'
        );
    }
}
