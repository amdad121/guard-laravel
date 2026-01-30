<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Contracts;

use AmdadulHaq\Guard\Contracts\Permission as PermissionContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Role
{
    /**
     * Get the name of the role.
     */
    public function getName(): string;

    /**
     * Get the permissions associated with the role.
     */
    public function permissions(): BelongsToMany;

    /**
     * Give permission to the role.
     *
     * @return Model The role instance
     */
    public function givePermissionTo(PermissionContract $permission): Model;

    /**
     * Sync permissions to the role.
     *
     * @param  array<int, int|string>  $permissions
     * @return array<string, array<int, int|string>> Array of synced permission IDs
     */
    public function syncPermissions(array $permissions): array;

    /**
     * Revoke permission from the role.
     *
     * @return int Number of permissions remaining
     */
    public function revokePermissionTo(PermissionContract $permission): int;

    /**
     * Get the names of all permissions for the role.
     *
     * @return array<int, string>
     */
    public function getPermissionNames(): array;
}
