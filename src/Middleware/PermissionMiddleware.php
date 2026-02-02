<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Middleware;

use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\Exceptions\PermissionDeniedException;
use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string ...$permissions): mixed
    {
        $user = $request->user();

        abort_unless($user instanceof UserContract, 403, 'Unauthenticated.');

        // Flatten permissions array (handles both 'users.create,users.edit' and 'users.create', 'users.edit' formats)
        $flattenedPermissions = collect($permissions)->flatMap(fn ($permission): array => explode(',', $permission))->all();

        // Check if user has ANY of the provided permissions
        $hasAnyPermission = false;
        foreach ($flattenedPermissions as $permission) {
            if ($user->hasPermission($permission)) {
                $hasAnyPermission = true;
                break;
            }
        }

        if (! $hasAnyPermission) {
            throw PermissionDeniedException::create(implode(', ', $flattenedPermissions));
        }

        return $next($request);
    }
}
