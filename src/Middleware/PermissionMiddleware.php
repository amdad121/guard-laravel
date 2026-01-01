<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Middleware;

use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\Exceptions\PermissionDeniedException;
use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        $user = $request->user();

        abort_unless($user instanceof UserContract, 403, 'Unauthenticated.');

        if (! $user->hasPermissionByName($permission)) {
            throw PermissionDeniedException::create($permission);
        }

        return $next($request);
    }
}
