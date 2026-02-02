<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait for resolving models from string names.
 */
trait ResolvesModels
{
    /**
     * Resolve a role from string or return the model.
     */
    protected function resolveRole(Model|string $role, bool $throw = true): ?Model
    {
        if (! is_string($role)) {
            return $role;
        }

        $query = config('guard.models.role')::query()->where('name', $role);

        return $throw ? $query->firstOrFail() : $query->first();
    }

    /**
     * Resolve a permission from string or return the model.
     */
    protected function resolvePermission(Model|string $permission, bool $throw = true): ?Model
    {
        if (! is_string($permission)) {
            return $permission;
        }

        $query = config('guard.models.permission')::query()->where('name', $permission);

        return $throw ? $query->firstOrFail() : $query->first();
    }
}
