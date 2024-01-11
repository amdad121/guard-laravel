<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard;

use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function assignRole(Role $role): Model
    {
        return $this->roles()->save($role);
    }

    public function syncRoles(array $roles): array
    {
        return $this->roles()->sync($roles);
    }

    public function revokeRole(Role $role): int
    {
        return $this->roles()->detach($role);
    }

    public function hasRole(string|Collection $role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return (bool) $role->intersect($this->roles)->count();
    }

    public function hasPermission(Permission $permission): bool
    {
        return $this->hasRole($permission->roles);
    }
}
