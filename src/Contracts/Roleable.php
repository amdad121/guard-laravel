<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * Contract for models that use Guard with role and permission checks.
 */
interface Roleable
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
    public function assignRole(Model|string|int|array ...$roles): self;

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

    /**
     * Get all permissions inherited through roles.
     */
    public function getPermissions(): Collection;

    /**
     * Get all inherited permission names.
     */
    public function getPermissionNames(): array;

    /**
     * Check if the user has a specific permission through roles.
     */
    public function hasPermission(Model|string $permission): bool;
}
