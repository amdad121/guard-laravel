<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Permission checks for user-like models where permissions are inherited from roles.
 *
 * @mixin Model
 */
trait HasPermissions
{
    /**
     * Get all permissions inherited through roles.
     */
    public function getPermissions(): Collection
    {
        return $this->getPermissionsViaRoles()
            ->unique(fn (Model $permission) => $permission->getKey())
            ->values();
    }

    /**
     * Check if model has a permission by model or name.
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

        if (! config('guard.wildcard.enabled', true)) {
            return false;
        }

        return (bool) $this->matchesWildcardPermission($name, $permissions);
    }

    /**
     * Get the permission names inherited through roles.
     */
    public function getPermissionNames(): array
    {
        return $this->getAllPermissionNames()->all();
    }

    /**
     * Scope a query to include models with specific permissions.
     */
    protected function scopeWithPermissions(Builder $query, string|array $permissions): Builder
    {
        return $query->whereHas('roles.permissions', fn (Builder $q) => $q->whereIn('name', (array) $permissions));
    }

    /**
     * Get all permission names for the model.
     */
    protected function getAllPermissionNames(): Collection
    {
        return $this->getPermissionsViaRoles()
            ->pluck('name')
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
     * Get permissions inherited through roles.
     */
    protected function getPermissionsViaRoles(): Collection
    {
        if (! method_exists($this, 'roles')) {
            return collect();
        }

        $roles = $this->roles()->with('permissions')->get();

        return $roles
            ->filter(fn (Model $role): bool => method_exists($role, 'permissions'))
            ->flatMap(function (Model $role): Collection {
                $permissions = $role->getRelationValue('permissions');

                return $permissions instanceof Collection ? $permissions : collect();
            });
    }
}
