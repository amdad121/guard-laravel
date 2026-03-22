<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Concerns;

use AmdadulHaq\Guard\Facades\Guard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

trait HasRoles
{
    /**
     * Get the roles relation.
     */
    public function roles(): BelongsToMany
    {
        $roleModel = config('guard.models.role');

        return $this->belongsToMany(
            $roleModel,
            Guard::getPivotTableName(Arr::only(config('guard.models'), ['role', 'user'])),
            Guard::getSingularName($this->getTable()).'_id',
            Guard::getSingularName(Guard::getTableName($roleModel)).'_id'
        );
    }

    /**
     * Assign a role to the model.
     */
    public function assignRole(Model|string|int|array ...$roles): self
    {
        $roleIds = $this->getRoleIds($this->normalizeRoles($roles));

        $this->roles()->syncWithoutDetaching($roleIds);

        return $this;
    }

    /**
     * Sync roles to the model.
     */
    public function syncRoles(array $roles, bool $detach = true): array
    {
        $roleIds = $this->getRoleIds($roles);

        return $detach
            ? $this->roles()->sync($roleIds)
            : $this->roles()->syncWithoutDetaching($roleIds);
    }

    /**
     * Sync roles to the model without detaching.
     */
    public function syncRolesWithoutDetaching(array $roles): array
    {
        return $this->syncRoles($roles, false);
    }

    /**
     * Revoke a role from the model.
     */
    public function revokeRole(Model|string $role): int
    {
        $role = $this->resolveRoleModel($role, false);

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
     * Get the role names.
     */
    public function getRoleNames(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * Check if entity has the given role.
     */
    public function hasRole(string|array|Collection $role): bool
    {
        if (is_string($role)) {
            return $this->roles->contains('name', $role);
        }

        return $this->normalizeRoleNames($role)
            ->intersect($this->getAssignedRoleNames())
            ->isNotEmpty();
    }

    /**
     * Check if entity has all of the given roles.
     */
    public function hasAllRoles(string|array|Collection ...$roles): bool
    {
        if (count($roles) === 1 && is_array($roles[0]) && $roles[0] !== []) {
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
     * Check if entity has any of the given roles.
     */
    public function hasAnyRole(string|array|Collection ...$roles): bool
    {
        return $this->normalizeRoleNames($roles)
            ->intersect($this->getAssignedRoleNames())
            ->isNotEmpty();
    }

    /**
     * Get role IDs from array of IDs or names.
     */
    protected function getRoleIds(array $roles): array
    {
        return collect($roles)
            ->map(function ($role): ?int {
                if ($role instanceof Model) {
                    return (int) $role->getKey();
                }

                return is_numeric($role) ? (int) $role : $this->getRoleIdByName($role);
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function normalizeRoles(array $roles): array
    {
        return collect($roles)
            ->flatMap(fn ($role): array => is_array($role) ? $role : [$role])
            ->values()
            ->all();
    }

    protected function getAssignedRoleNames(): Collection
    {
        return $this->roles->pluck('name');
    }

    protected function normalizeRoleNames(string|array|Collection $roles): Collection
    {
        if (is_string($roles)) {
            return collect([$roles]);
        }

        if ($roles instanceof Collection) {
            $roles = $roles->pluck('name')->all();
        }

        return collect($roles)->flatten()->filter();
    }

    /**
     * Get role ID by name.
     */
    protected function getRoleIdByName(string $roleName): ?int
    {
        return config('guard.models.role')::query()
            ->where('name', $roleName)
            ->first()?->id;
    }

    /**
     * Resolve a role model from a string name or return the provided model.
     */
    protected function resolveRoleModel(Model|string $role, bool $throw = true): ?Model
    {
        if (! is_string($role)) {
            return $role;
        }

        $query = config('guard.models.role')::query()->where('name', $role);

        return $throw ? $query->firstOrFail() : $query->first();
    }

    /**
     * Scope a query to include models with specific roles.
     */
    protected function scopeWithRoles(Builder $query, string|array $roles): Builder
    {
        return $query->whereHas('roles', fn (Builder $q) => $q->whereIn('name', (array) $roles));
    }
}
