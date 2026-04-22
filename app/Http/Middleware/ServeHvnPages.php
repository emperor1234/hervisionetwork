<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Web\HvnController;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ServeHvnPages
{
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();

        // Quick prefix check — skip controller instantiation for non-HVN requests
        $isHvn = str_starts_with($path, 'community') ||
                 str_starts_with($path, 'creators')  ||
                 $path === 'creator-signup'           ||
                 $path === 'logout';

        if (!$isHvn) {
            return $next($request);
        }

        $c = app(HvnController::class);

        if ($request->isMethod('GET')) {
            if ($path === 'community')      return $this->r($c->community($request));
            if ($path === 'creator-signup') return $this->r($c->creatorSignup());
            if ($path === 'creators')       return $this->r($c->creators($request));

            if (preg_match('/^community\/(\d+)$/', $path, $m))
                return $this->r($c->communityShow($request, (int) $m[1]));

            if (preg_match('/^creators\/(\d+)$/', $path, $m))
                return $this->r($c->creatorProfile((int) $m[1]));
        }

        if ($request->isMethod('POST')) {
            if ($path === 'community/posts')
                return $this->r($c->communityStore($request));

            if (preg_match('/^community\/(\d+)\/comments$/', $path, $m))
                return $this->r($c->commentStore($request, (int) $m[1]));

            if ($path === 'logout')
                return $this->r($c->logout($request));
        }

        return $next($request);
    }

    // Ensures views are wrapped in a real HTTP Response so downstream middleware
    // (VerifyCsrfToken::addCookieToResponse) can set cookies on the response headers.
    private function r($result)
    {
        return ($result instanceof Response) ? $result : response($result);
    }
}
