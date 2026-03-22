<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Models;

use AmdadulHaq\Guard\Concerns\HasPermissions;
use AmdadulHaq\Guard\Contracts\Permissions as PermissionsContract;
use AmdadulHaq\Guard\Facades\Guard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

/**
 * @property string $name
 * @property string|null $label
 * @property string|null $description
 * @property bool $is_guarded
 */
class Role extends Model implements PermissionsContract
{
    use HasPermissions;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'is_guarded' => 'boolean',
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get table name.
     */
    public function getTable(): string
    {
        return config('guard.tables.roles', parent::getTable());
    }

    /**
     * Permissions relation.
     */
    public function permissions(): BelongsToMany
    {
        $permissionModel = config('guard.models.permission');

        return $this->belongsToMany(
            $permissionModel,
            Guard::getPivotTableName(Arr::only(config('guard.models'), ['permission', 'role'])),
            Guard::getSingularName($this->getTable()).'_id',
            Guard::getSingularName(Guard::getTableName($permissionModel)).'_id'
        );
    }

    /**
     * Users relation.
     */
    public function users(): BelongsToMany
    {
        $userModel = config('guard.models.user');

        return $this->belongsToMany(
            $userModel,
            Guard::getPivotTableName(Arr::only(config('guard.models'), ['role', 'user'])),
            Guard::getSingularName($this->getTable()).'_id',
            Guard::getSingularName(Guard::getTableName($userModel)).'_id'
        );
    }

    /**
     * Check if role is protected.
     */
    public function isProtectedRole(): bool
    {
        return $this->is_guarded ?? false;
    }

    /**
     * Get permission names.
     */
    public function getPermissionNames(): array
    {
        return $this->permissions->pluck('name')->toArray();
    }

    /**
     * Give a permission to the role.
     */
    public function givePermissionTo(Model|string|int|array ...$permissions): Model
    {
        $permissionIds = $this->getPermissionIds($this->normalizePermissions($permissions));

        $this->permissions()->syncWithoutDetaching($permissionIds);
        $this->clearGuardCache();

        return $this;
    }

    /**
     * Sync permissions to the role.
     */
    public function syncPermissions(array $permissions): array
    {
        $synced = $this->permissions()->sync($this->getPermissionIds($permissions));
        $this->clearGuardCache();

        return $synced;
    }

    /**
     * Revoke a permission from the role.
     */
    public function revokePermissionTo(Model|string $permission): int
    {
        $permission = $this->resolvePermissionModel($permission);

        $detached = $this->permissions()->detach($permission);
        $this->clearGuardCache();

        return $detached;
    }

    /**
     * Revoke all permissions from the role.
     */
    public function revokeAllPermissions(): int
    {
        $detached = $this->permissions()->detach();
        $this->clearGuardCache();

        return $detached;
    }

    /**
     * Check if the role has a permission assigned directly.
     */
    public function hasPermissionTo(Model|string $permission): bool
    {
        $permission = $this->resolvePermissionModel($permission, false);

        return $permission instanceof Model && $this->permissions()
            ->whereKey($permission->getKey())
            ->exists();
    }

    public function hasPermission(Model|string $permission): bool
    {
        return $this->hasPermissionTo($permission);
    }

    /**
     * Scope a query to only include guarded roles.
     */
    protected function scopeGuarded(Builder $query): Builder
    {
        return $query->where('is_guarded', true);
    }

    /**
     * Scope a query to only include unguarded roles.
     */
    protected function scopeUnguarded(Builder $query): Builder
    {
        return $query->where('is_guarded', false);
    }

    protected function getPermissionIds(array $permissions): array
    {
        return collect($permissions)
            ->map(function ($permission): ?int {
                if ($permission instanceof Model) {
                    return (int) $permission->getKey();
                }

                return is_numeric($permission)
                    ? (int) $permission
                    : $this->getPermissionIdByName($permission);
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function normalizePermissions(array $permissions): array
    {
        return collect($permissions)
            ->flatMap(fn ($permission): array => is_array($permission) ? $permission : [$permission])
            ->values()
            ->all();
    }

    protected function getPermissionIdByName(string $permissionName): ?int
    {
        return config('guard.models.permission')::query()
            ->where('name', $permissionName)
            ->value('id');
    }

    protected function resolvePermissionModel(Model|string $permission, bool $throw = true): ?Model
    {
        if (! is_string($permission)) {
            return $permission;
        }

        $query = config('guard.models.permission')::query()->where('name', $permission);

        return $throw ? $query->firstOrFail() : $query->first();
    }

    protected function clearGuardCache(): void
    {
        if (! config('guard.cache.enabled', true)) {
            return;
        }

        Guard::clearCache();
    }
}
