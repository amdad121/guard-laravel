<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        /** @phpstan-ignore-next-line */
        if (! $request->user()->hasRole($role)) {
            abort(401);
        }

        return $next($request);
    }
}
