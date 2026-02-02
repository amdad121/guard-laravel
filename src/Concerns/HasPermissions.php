<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

/**
 * @mixin Model
 *
 * @property-read Collection<int, Model> $roles
 * @property-read Collection<int, Model> $permissions
 */
trait HasPermissions
{
    use ResolvesModels;

    /**
     * Get the permissions relation.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(config('guard.models.permission'));
    }

    /**
     * Give a permission to the role.
     */
    public function givePermissionTo(Model|string $permission): Model
    {
        $permission = $this->resolvePermission($permission);

        return $this->permissions()->save($permission);
    }

    /**
     * Sync permissions to role.
     */
    public function syncPermissions(array $permissions): array
    {
        return $this->permissions()->sync($this->getPermissionIds($permissions));
    }

    /**
     * Revoke a permission from the role.
     */
    public function revokePermissionTo(Model|string $permission): int
    {
        $permission = $this->resolvePermission($permission);

        return $this->permissions()->detach($permission);
    }

    /**
     * Revoke all permissions from the role.
     */
    public function revokeAllPermissions(): int
    {
        return $this->permissions()->detach();
    }

    /**
     * Check if the role has a permission.
     */
    public function hasPermissionTo(Model|string $permission): bool
    {
        $permission = $this->resolvePermission($permission, false);

        return $permission !== null && $this->permissions()
            ->whereKey($permission->getKey())
            ->exists();
    }

    /**
     * Get all permissions for the model.
     */
    public function getPermissions(): Collection
    {
        // @phpstan-ignore-next-line - Defensive check for models without roles
        if (! method_exists($this, 'roles')) {
            return collect();
        }

        return $this->roles
            ->filter(fn (Model $role): bool => method_exists($role, 'permissions'))
            ->flatMap(fn (Model $role) => $role->permissions()->get())
            ->unique(fn (Model $permission) => $permission->getKey())
            ->values();
    }

    /**
     * Check if model has a permission by name.
     */
    public function hasPermissionByName(string $permission): bool
    {
        return $this->hasPermission($permission);
    }

    /**
     * Check if model has a permission by model.
     */
    public function hasPermission(Model|string $permission): bool
    {
        $name = is_string($permission)
            ? $permission
            : $permission->getAttribute('name');

        if (! is_string($name)) {
            return false;
        }

        $permissions = $this->getAllPermissionNames();

        if ($permissions->contains($name)) {
            return true;
        }

        return (bool) $this->matchesWildcardPermission($name, $permissions);
    }

    /**
     * Get the permission names.
     *
     * @return array<int, string>
     */
    public function getPermissionNames(): array
    {
        return $this->permissions->pluck('name')->toArray();
    }

    /**
     * Get permission IDs from array of IDs or names.
     */
    protected function getPermissionIds(array $permissions): array
    {
        return collect($permissions)
            ->map(fn ($permission) => is_numeric($permission)
                ? (int) $permission
                : $this->getPermissionIdByName($permission)
            )
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Get permission ID by name.
     */
    protected function getPermissionIdByName(string $permissionName): ?int
    {
        return config('guard.models.permission')::query()
            ->where('name', $permissionName)
            ->value('id');
    }

    /**
     * Get all permission names for the model.
     */
    protected function getAllPermissionNames(): Collection
    {
        // @phpstan-ignore-next-line - Defensive check for models without roles
        if (! method_exists($this, 'roles')) {
            return collect();
        }

        return $this->roles
            ->filter(fn (Model $role): bool => method_exists($role, 'permissions'))
            ->flatMap(fn (Model $role) => $role->permissions()->pluck('name'))
            ->unique()
            ->values();
    }

    /**
     * Check if a permission matches a wildcard permission.
     */
    protected function matchesWildcardPermission(string $permission, Collection $allPermissions): bool
    {
        $permissionParts = explode('.', $permission);

        return $allPermissions->contains(function (string $perm) use ($permissionParts): bool {
            if (! str_ends_with($perm, '*')) {
                return false;
            }

            $wildcardParts = array_filter(explode('.', mb_rtrim($perm, '*')));

            foreach ($wildcardParts as $index => $part) {
                if (! isset($permissionParts[$index]) || $permissionParts[$index] !== $part) {
                    return false;
                }
            }

            return true;
        });
    }

    /**
     * Scope a query to include models with specific permissions.
     */
    protected function scopeWithPermissions(Builder $query, string|array $permissions): Builder
    {
        return $query->whereHas('roles.permissions', fn (Builder $q) => $q->whereIn('name', (array) $permissions));
    }
}
