<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequireRole
{
    /**
     * Enforce that the authenticated user has one of the allowed roles.
     *
     * Usage in routes:
     *   ->middleware('role:creator')
     *   ->middleware('role:creator,admin')
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!in_array($user->role, $roles, true)) {
            return response()->json(['message' => 'Forbidden. Insufficient role.'], 403);
        }

        return $next($request);
    }
}
