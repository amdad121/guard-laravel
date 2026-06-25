<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Models;

use AmdadulHaq\Guard\Concerns\HasGuardHelpers;
use AmdadulHaq\Guard\Contracts\Permissionable as PermissionableContract;
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
class Role extends Model implements PermissionableContract
{
    use HasGuardHelpers;

    protected $guarded = [];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_guarded' => 'boolean',
        ];
    }

    /**
     * Get the role name.
     */
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
        $permissionIds = $this->getModelIds('permission', $this->flattenArgs($permissions));

        $this->permissions()->syncWithoutDetaching($permissionIds);
        $this->clearGuardCache();

        return $this;
    }

    /**
     * Sync permissions to the role.
     */
    public function syncPermissions(array $permissions): array
    {
        $synced = $this->permissions()->sync($this->getModelIds('permission', $permissions));
        $this->clearGuardCache();

        return $synced;
    }

    /**
     * Revoke a permission from the role.
     */
    public function revokePermissionTo(Model|string $permission): int
    {
        $permission = $this->resolveModel('permission', $permission);

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
    public function hasPermission(Model|string $permission): bool
    {
        $permission = $this->resolveModel('permission', $permission, false);

        return $permission instanceof Model && $this->permissions()
            ->whereKey($permission->getKey())
            ->exists();
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
}
