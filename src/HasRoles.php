<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard;

use AmdadulHaq\Guard\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function assignRole($role): Model
    {
        return $this->roles()->save(
            Role::whereSlug($role)->firstOrFail()
        );
    }

    public function hasRole($role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('slug', $role);
        }

        return (bool) $role->intersect($this->roles)->count();
    }

    public function hasPermission($permission): bool
    {
        return $this->hasRole($permission->roles);
    }
}
