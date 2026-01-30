<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Contracts;

use AmdadulHaq\Guard\Contracts\Role as RoleContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

interface User
{
    /**
     * Get the roles associated with the user.
     */
    public function roles(): BelongsToMany;

    /**
     * Assign a role to the user.
     *
     * @param  Model|string  $role  Role instance or name
     * @return Model The user instance
     */
    public function assignRole(Model|string $role): Model;

    /**
     * Sync roles to the user.
     *
     * @param  array<int, int|string>  $roles  Array of role IDs or names
     * @return array<string, array<int, int|string>> Array of synced role IDs
     */
    public function syncRoles(array $roles): array;

    /**
     * Revoke a role from the user.
     *
     * @param  RoleContract|Model|string  $role  Role instance, contract, or name
     * @return int Number of roles remaining
     */
    public function revokeRole(RoleContract|Model|string $role): int;

    /**
     * Check if the user has a specific role.
     *
     * @param  string|array|Collection  $role  Role name(s) or collection
     */
    public function hasRole(string|array|Collection $role): bool;

    /**
     * Check if the user has all specified roles.
     *
     * @param  string|array|Collection  $roles  Role name(s) or collection
     */
    public function hasAllRoles(string|array|Collection ...$roles): bool;

    /**
     * Check if the user has any of the specified roles.
     *
     * @param  string|array|Collection  $roles  Role name(s) or collection
     */
    public function hasAnyRole(string|array|Collection ...$roles): bool;

    /**
     * Check if the user has a specific permission by name.
     *
     * @return bool True if user has the permission
     */
    public function hasPermissionByName(string $permission): bool;
}
