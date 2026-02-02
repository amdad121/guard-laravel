<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Concerns;

use Illuminate\Support\Collection;

/**
 * Trait for checking role relationships.
 */
trait ChecksRoles
{
    /**
     * Check if entity has the given role.
     */
    public function hasRole(string|array|Collection $role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        if ($role instanceof Collection) {
            $role = $role->pluck('name');
        }

        if (is_array($role) && $role !== [] && is_array($role[0])) {
            $role = collect($role)->flatten()->all();
        }

        return (bool) collect($role)->intersect($this->roles->pluck('name'))->count();
    }

    /**
     * Check if entity has all of the given roles.
     */
    public function hasAllRoles(string|array|Collection ...$roles): bool
    {
        if (count($roles) === 1 && is_array($roles[0]) && $roles[0] !== []) {
            $roles = $roles[0];
        }

        foreach ($roles as $role) {
            if (! $this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if entity has any of the given roles.
     */
    public function hasAnyRole(string|array|Collection ...$roles): bool
    {
        if (count($roles) === 1 && is_array($roles[0]) && $roles[0] !== []) {
            $roles = $roles[0];
        }

        $roleNames = collect($roles);

        if (count($roleNames) > 1) {
            $roleNames = $roleNames->flatten();
        }

        return (bool) $roleNames->intersect($this->roles->pluck('name'))->count();
    }
}
