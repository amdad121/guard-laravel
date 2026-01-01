<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard;

use AmdadulHaq\Guard\Contracts\Role as RoleContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

// @phpstan-ignore trait.unused
trait HasRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('guard.models.role'));
    }

    public function assignRole(Model|string $role): Model
    {
        if (is_string($role)) {
            $role = resolve(config('guard.models.role'))->where('name', $role)->firstOrFail();
        }

        $this->roles()->save($role);

        return $this;
    }

    public function syncRoles(array $roles): array
    {
        return $this->roles()->sync($roles);
    }

    public function revokeRole(RoleContract|Model|string $role): int
    {
        if (is_string($role)) {
            $role = resolve(config('guard.models.role'))->where('name', $role)->first();
        }

        $this->roles()->detach($role);

        return $this->roles()->count();
    }

    public function hasRole(string|array|Collection $role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return (bool) $role->intersect($this->roles)->count();
    }

    public function hasAllRoles(string|array|Collection ...$roles): bool
    {
        if (count($roles) === 1 && ! is_string($roles[0]) && ! $roles[0] instanceof Collection) {
            $roles = $roles[0];
        }

        foreach ($roles as $role) {
            if (! $this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    public function hasAnyRole(string|array|Collection ...$roles): bool
    {
        if (count($roles) === 1 && ! is_string($roles[0]) && ! $roles[0] instanceof Collection) {
            $roles = $roles[0];
        }

        return (bool) collect($roles)->intersect($this->roles->pluck('name'))->count();
    }

    public function hasPermission(Model|string $permission): bool
    {
        if (is_string($permission)) {
            return $this->hasPermissionByName($permission);
        }

        return $this->hasPermissionByName($permission->name);
    }

    public function hasPermissionByName(string $permission): bool
    {
        $allPermissions = $this->getAllPermissionNames();

        if ($allPermissions->contains($permission)) {
            return true;
        }

        return $this->matchesWildcardPermission($permission, $allPermissions);
    }

    /**
     * @return Collection<int, string>
     */
    protected function getAllPermissionNames(): Collection
    {
        return $this->roles->flatMap(fn ($role) => $role->permissions->pluck('name'));
    }

    protected function matchesWildcardPermission(string $permission, Collection $allPermissions): bool
    {
        $permissionParts = explode('.', $permission);

        foreach ($allPermissions as $perm) {
            if (! str_ends_with((string) $perm, '*')) {
                continue;
            }

            $wildcardParts = array_filter(explode('.', rtrim((string) $perm, '*')));

            if (! $this->checkWildcardMatch($permissionParts, $wildcardParts)) {
                continue;
            }

            return true;
        }

        return false;
    }

    /**
     * @param  array<int, string>  $permissionParts
     * @param  array<int, string>  $wildcardParts
     */
    protected function checkWildcardMatch(array $permissionParts, array $wildcardParts): bool
    {
        foreach ($wildcardParts as $index => $part) {
            if (! isset($permissionParts[$index]) || $permissionParts[$index] !== $part) {
                return false;
            }
        }

        return true;
    }

    public function getPermissions(): Collection
    {
        return $this->roles->pluck('permissions')->flatten()->unique('id');
    }

    protected function scopeWithRoles(Builder $query, string|array $roles): Builder
    {
        return $query->whereHas('roles', fn ($q) => $q->whereIn('name', (array) $roles));
    }

    protected function scopeWithPermissions(Builder $query, string|array $permissions): Builder
    {
        return $query->whereHas('roles.permissions', fn ($q) => $q->whereIn('name', (array) $permissions));
    }
}
