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
    /**
     * Get the roles relation.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('guard.models.role'));
    }

    /**
     * Assign a role to the model.
     */
    public function assignRole(Model|string $role): self
    {
        if (is_string($role)) {
            $role = config('guard.models.role')::query()
                ->where('name', $role)
                ->firstOrFail();
        }

        $this->roles()->attach($role);

        return $this;
    }

    /**
     * Sync roles to the model.
     */
    public function syncRoles(array $roles): array
    {
        $roleIds = $this->getRoleIds($roles);

        return $this->roles()->sync($roleIds);
    }

    /**
     * Sync roles to the model without detaching.
     */
    public function syncRolesWithoutDetaching(array $roles): array
    {
        $roleIds = $this->getRoleIds($roles);

        return $this->roles()->syncWithoutDetaching($roleIds);
    }

    /**
     * Revoke a role from the model.
     */
    public function revokeRole(RoleContract|Model|string $role): int
    {
        if (is_string($role)) {
            $role = config('guard.models.role')::query()
                ->where('name', $role)
                ->first();
        }

        return $this->roles()->detach($role);
    }

    /**
     * Revoke all roles from the model.
     */
    public function revokeRoles(): int
    {
        return $this->roles()->detach();
    }

    /**
     * Check if model has the given role.
     */
    public function hasRole(string|array|Collection $role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        if ($role instanceof Collection) {
            $role = $role->pluck('name');
        }

        if (is_array($role) && $role !== [] && is_array($role[0])) {
            $role = collect($role)->flatten()->all();
        }

        return (bool) collect($role)->intersect($this->roles->pluck('name'))->count();
    }

    /**
     * Check if model has all of the given roles.
     */
    public function hasAllRoles(string|array|Collection ...$roles): bool
    {
        if (count($roles) === 1 && is_array($roles[0]) && $roles[0] !== [] && ! $roles[0] instanceof Collection) {
            $roles = $roles[0];
        }

        foreach ($roles as $role) {
            if (! $this->hasRole($role)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if model has any of the given roles.
     */
    public function hasAnyRole(string|array|Collection ...$roles): bool
    {
        if (count($roles) === 1 && is_array($roles[0]) && $roles[0] !== [] && ! $roles[0] instanceof Collection) {
            $roles = $roles[0];
        }

        $roleNames = collect($roles);

        if (count($roleNames) > 1) {
            $roleNames = $roleNames->flatten();
        }

        return (bool) $roleNames->intersect($this->roles->pluck('name'))->count();
    }

    /**
     * Get the role names.
     */
    public function getRoleNames(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * Get role IDs from array of IDs or names.
     */
    protected function getRoleIds(array $roles): array
    {
        return collect($roles)
            ->map(fn ($role) => is_numeric($role) ? (int) $role : $this->getRoleIdByName($role))
            ->filter()
            ->values()
            ->toArray();
    }

    /**
     * Get role ID by name.
     */
    protected function getRoleIdByName(string $roleName): ?int
    {
        $roleModel = config('guard.models.role');

        return $roleModel::query()
            ->where('name', $roleName)
            ->first()?->id;
    }

    /**
     * Scope a query to include models with specific roles.
     */
    protected function scopeWithRoles(Builder $query, string|array $roles): Builder
    {
        return $query->whereHas('roles', fn (Builder $q) => $q->whereIn('name', (array) $roles));
    }
}
