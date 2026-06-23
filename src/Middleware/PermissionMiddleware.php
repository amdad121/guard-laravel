<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Middleware;

use AmdadulHaq\Guard\Concerns\ParsesMiddlewareParameters;
use AmdadulHaq\Guard\Contracts\Roleable;
use AmdadulHaq\Guard\Exceptions\PermissionDeniedException;
use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    use ParsesMiddlewareParameters;

    public function handle(Request $request, Closure $next, string ...$permissions): mixed
    {
        $user = $request->user();

        abort_unless($user instanceof Roleable, 403, 'Unauthenticated.');

        $flattenedPermissions = $this->parseParameters($permissions);

        if (! collect($flattenedPermissions)->contains(fn ($p) => $user->hasPermission($p))) {
            throw PermissionDeniedException::create(implode(', ', $flattenedPermissions));
        }

        return $next($request);
    }
}
