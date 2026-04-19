<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Web\HvnController;
use Closure;
use Illuminate\Http\Request;

class ServeHvnPages
{
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();
        $c    = app(HvnController::class);

        if ($request->isMethod('GET')) {
            if ($path === 'community')       return $c->community($request);
            if ($path === 'creator-signup')  return $c->creatorSignup();
            if ($path === 'creators')        return $c->creators($request);

            if (preg_match('/^community\/(\d+)$/', $path, $m))
                return $c->communityShow($request, (int) $m[1]);

            if (preg_match('/^creators\/(\d+)$/', $path, $m))
                return $c->creatorProfile((int) $m[1]);
        }

        if ($request->isMethod('POST')) {
            if ($path === 'community/posts')
                return $c->communityStore($request);

            if (preg_match('/^community\/(\d+)\/comments$/', $path, $m))
                return $c->commentStore($request, (int) $m[1]);

            if ($path === 'logout')
                return $c->logout($request);
        }

        return $next($request);
    }
}
