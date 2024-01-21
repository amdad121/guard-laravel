<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard;

use AmdadulHaq\Guard\Contracts\Role as RoleContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('guard.models.role'));
    }

    public function assignRole(Model $role): Model
    {
        return $this->roles()->save($role);
    }

    public function syncRoles(array $roles): array
    {
        return $this->roles()->sync($roles);
    }

    public function revokeRole(RoleContract $role): int
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

    public function hasPermission(Model $permission): bool
    {
        return $this->hasRole(role: $permission->roles);
    }
}
