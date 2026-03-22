<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Contract for entities that have and can manage roles.
 */
interface Roles
{
    /**
     * Get the roles associated with the entity.
     */
    public function roles(): BelongsToMany;

    /**
     * Get the names of all roles.
     */
    public function getRoleNames(): array;

    /**
     * Assign a role to the entity.
     */
    public function assignRole(Model|string|int|array ...$roles): Model;

    /**
     * Sync roles to the entity.
     */
    public function syncRoles(array $roles): array;

    /**
     * Revoke a role from the entity.
     */
    public function revokeRole(Model|string $role): int;

    /**
     * Check if the entity has a specific role.
     */
    public function hasRole(string|array|Collection $role): bool;

    /**
     * Check if the entity has all specified roles.
     */
    public function hasAllRoles(string|array|Collection ...$roles): bool;

    /**
     * Check if the entity has any of the specified roles.
     */
    public function hasAnyRole(string|array|Collection ...$roles): bool;
}
