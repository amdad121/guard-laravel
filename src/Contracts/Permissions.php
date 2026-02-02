<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Contract for entities that have and can manage permissions.
 */
interface Permissions
{
    /**
     * Get the permissions associated with the entity.
     */
    public function permissions(): BelongsToMany;

    /**
     * Get the names of all permissions.
     *
     * @return array<int, string>
     */
    public function getPermissionNames(): array;

    /**
     * Give permission to the entity.
     *
     * @param  Model|string  $permission  Permission instance or name
     * @return Model The entity instance
     */
    public function givePermissionTo(Model|string $permission): Model;

    /**
     * Sync permissions to the entity.
     *
     * @param  array<int, int|string>  $permissions
     * @return array<string, array<int, int|string>> Array of synced permission IDs
     */
    public function syncPermissions(array $permissions): array;

    /**
     * Revoke permission from the entity.
     *
     * @param  Model|string  $permission  Permission instance or name
     * @return int Number of permissions remaining
     */
    public function revokePermissionTo(Model|string $permission): int;

    /**
     * Check if the entity has a specific permission by model or name.
     *
     * @param  Model|string  $permission  Permission instance or name
     * @return bool True if entity has the permission
     */
    public function hasPermission(Model|string $permission): bool;
}
