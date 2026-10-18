<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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

        if (! $user || ! $user->is_active) {
            abort(403, 'Your account is inactive or you are not authenticated.');
        }

        if (empty($roles)) {
            return $next($request);
        }

        $userRoleValue = $user->role->value;

        if (! in_array($userRoleValue, $roles, true)) {
            abort(403, 'Unauthorized access for your account role.');
        }

        return $next($request);
    }
}
