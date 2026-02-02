<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Middleware;

use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\Exceptions\PermissionDeniedException;
use Closure;
use Illuminate\Http\Request;

class RoleOrPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roleOrPermissions): mixed
    {
        $user = $request->user();

        abort_unless($user instanceof UserContract, 403, 'Unauthenticated.');

        // Flatten role/permission array (handles both 'admin,edit-posts' and 'admin', 'edit-posts' formats)
        $flattenedItems = collect($roleOrPermissions)->flatMap(fn ($item): array => explode(',', $item))->all();

        // Check if user has any of the items as a role OR any as a permission
        foreach ($flattenedItems as $item) {
            if ($user->hasRole($item) || $user->hasPermissionByName($item)) {
                return $next($request);
            }
        }

        throw PermissionDeniedException::roleNotAssigned(implode(', ', $flattenedItems));
    }
}
