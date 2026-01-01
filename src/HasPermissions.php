<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard;

use AmdadulHaq\Guard\Contracts\Permission as PermissionContract;
use Illuminate\Database\Eloquent\Model;

trait HasPermissions
{
    /**
     * Give a permission to the role.
     *
     * @return Model The role instance
     */
    public function givePermissionTo(PermissionContract $permission): Model
    {
        return $this->permissions()->save($permission);
    }

    /**
     * Sync permissions to role.
     *
     * @param  array<int, int|string>  $permissions  Array of permission IDs or names
     * @return array<string, array<int, int|string>> Array of synced permission IDs
     */
    public function syncPermissions(array $permissions): array
    {
        return $this->permissions()->sync($permissions);
    }

    /**
     * Revoke a permission from the role.
     *
     * @return int Number of permissions remaining
     */
    public function revokePermissionTo(PermissionContract $permission): int
    {
        return $this->permissions()->detach($permission);
    }
}
