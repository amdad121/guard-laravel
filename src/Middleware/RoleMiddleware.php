<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Middleware;

use AmdadulHaq\Guard\Concerns\ParsesMiddlewareParameters;
use AmdadulHaq\Guard\Contracts\Roleable;
use AmdadulHaq\Guard\Exceptions\PermissionDeniedException;
use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    use ParsesMiddlewareParameters;

    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        abort_unless($user instanceof Roleable, 403, 'Unauthenticated.');

        $flattenedRoles = $this->parseParameters($roles);

        if (! $user->hasAnyRole(...$flattenedRoles)) {
            throw PermissionDeniedException::roleNotAssigned(implode(', ', $flattenedRoles));
        }

        return $next($request);
    }
}
