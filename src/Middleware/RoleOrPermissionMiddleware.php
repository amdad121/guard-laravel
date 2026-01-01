<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Middleware;

use AmdadulHaq\Guard\Contracts\User as UserContract;
use Closure;
use Illuminate\Http\Request;

class RoleOrPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $roleOrPermission): mixed
    {
        $user = $request->user();

        abort_unless($user instanceof UserContract, 403, 'Unauthenticated.');

        $hasRole = $user->hasRole($roleOrPermission);
        $hasPermission = $user->hasPermissionByName($roleOrPermission);

        if ($hasRole || $hasPermission) {
            return $next($request);
        }

        abort(403, 'User does not have the required role or permission.');
    }
}
