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
     * @param  PermissionContract|string  $permission  Permission model or name
     * @return Model The role instance
     */
    public function givePermissionTo(PermissionContract|string $permission): Model
    {
        if (is_string($permission)) {
            $permission = config('guard.models.permission')::where('name', $permission)->firstOrFail();
        }

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
        $permissionIds = $this->getPermissionIds($permissions);

        return $this->permissions()->sync($permissionIds);
    }

    /**
     * Get permission IDs from array of IDs or names.
     *
     * @param  array<int, int|string>  $permissions
     * @return array<int, int>
     */
    protected function getPermissionIds(array $permissions): array
    {
        return collect($permissions)
            ->map(function ($permission) {
                if (is_numeric($permission)) {
                    return (int) $permission;
                }

                return config('guard.models.permission')::where('name', $permission)->first()?->id;
            })
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Revoke a permission from the role.
     *
     * @param  PermissionContract|string  $permission  Permission model or name
     * @return int Number of permissions remaining
     */
    public function revokePermissionTo(PermissionContract|string $permission): int
    {
        if (is_string($permission)) {
            $permission = config('guard.models.permission')::where('name', $permission)->firstOrFail();
        }

        return $this->permissions()->detach($permission);
    }
}
