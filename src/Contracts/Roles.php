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
     *
     * @return array<int, string>
     */
    public function getRoleNames(): array;

    /**
     * Assign a role to the entity.
     *
     * @param  Model|string  $role  Role instance or name
     * @return Model The entity instance
     */
    public function assignRole(Model|string $role): Model;

    /**
     * Sync roles to the entity.
     *
     * @param  array<int, int|string>  $roles  Array of role IDs or names
     * @return array<string, array<int, int|string>> Array of synced role IDs
     */
    public function syncRoles(array $roles): array;

    /**
     * Revoke a role from the entity.
     *
     * @param  Model|string  $role  Role instance or name
     * @return int Number of roles remaining
     */
    public function revokeRole(Model|string $role): int;

    /**
     * Check if the entity has a specific role.
     *
     * @param  string|array|Collection  $role  Role name(s) or collection
     */
    public function hasRole(string|array|Collection $role): bool;

    /**
     * Check if the entity has all specified roles.
     *
     * @param  string|array|Collection  $roles  Role name(s) or collection
     */
    public function hasAllRoles(string|array|Collection ...$roles): bool;

    /**
     * Check if the entity has any of the specified roles.
     *
     * @param  string|array|Collection  $roles  Role name(s) or collection
     */
    public function hasAnyRole(string|array|Collection ...$roles): bool;
}
