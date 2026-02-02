<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Middleware;

use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\Exceptions\PermissionDeniedException;
use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        abort_unless($user instanceof UserContract, 403, 'Unauthenticated.');

        // Flatten roles array (handles both 'admin,editor' and 'admin', 'editor' formats)
        $flattenedRoles = collect($roles)->flatMap(fn ($role): array => explode(',', $role))->all();

        if (! $user->hasAnyRole(...$flattenedRoles)) {
            throw PermissionDeniedException::roleNotAssigned(implode(', ', $flattenedRoles));
        }

        return $next($request);
    }
}
