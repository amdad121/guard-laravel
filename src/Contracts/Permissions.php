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
     */
    public function getPermissionNames(): array;

    /**
     * Give permission to the entity.
     */
    public function givePermissionTo(Model|string|int|array ...$permissions): Model;

    /**
     * Sync permissions to the entity.
     */
    public function syncPermissions(array $permissions): array;

    /**
     * Revoke permission from the entity.
     */
    public function revokePermissionTo(Model|string $permission): int;

    /**
     * Check if the entity has a specific permission by model or name.
     */
    public function hasPermission(Model|string $permission): bool;
}
