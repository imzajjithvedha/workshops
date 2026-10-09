<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Coarse gate per route group, e.g. role:manager,staff. Policies do the
 * fine-grained checks; this refuses a wrong role or a deactivated account
 * before a controller runs. Unlike Bookiora/Cricora it answers 403 instead of
 * redirecting, so the API gets a real status code.
 */
class EnsureUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(401);
        }

        if (! $user->isActive()) {
            return $this->refuseInactive($request);
        }

        $allowed = array_map(fn (string $role) => UserRole::from($role), $roles);

        abort_unless(in_array($user->role, $allowed, true), 403, 'Your role does not allow this.');

        return $next($request);
    }

    private function refuseInactive(Request $request): Response
    {
        if ($request->expectsJson() || $request->is('api/*')) {
            abort(403, 'Your account is inactive.');
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->withErrors([
            'email' => 'Your account is inactive. Please contact an administrator.',
        ]);
    }
}
