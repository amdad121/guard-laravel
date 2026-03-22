<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Models;

use AmdadulHaq\Guard\Concerns\HasRoles;
use AmdadulHaq\Guard\Contracts\Roles as RolesContract;
use AmdadulHaq\Guard\Enums\PermissionType;
use AmdadulHaq\Guard\Facades\Guard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Arr;

/**
 * @property string $name
 * @property string|null $label
 * @property string|null $description
 * @property string|null $group
 * @property bool $is_wildcard
 */
class Permission extends Model implements RolesContract
{
    use HasRoles;

    protected $guarded = [];

    /**
     * Get the permission name.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the permission label.
     */
    protected function casts(): array
    {
        return [
            'is_wildcard' => 'boolean',
        ];
    }

    /**
     * Get the permission label.
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * Get the permission description.
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * Get the permission table.
     */
    public function getTable(): string
    {
        return config('guard.tables.permissions', parent::getTable());
    }

    /**
     * Get the permission roles.
     */
    public function roles(): BelongsToMany
    {
        $roleModel = config('guard.models.role');

        return $this->belongsToMany(
            $roleModel,
            Guard::getPivotTableName(Arr::only(config('guard.models'), ['permission', 'role'])),
            Guard::getSingularName($this->getTable()).'_id',
            Guard::getSingularName(Guard::getTableName($roleModel)).'_id'
        );
    }

    /**
     * Check if the permission is a wildcard permission.
     */
    public function isWildcard(): bool
    {
        return str_ends_with($this->name, '*');
    }

    /**
     * Get the permission group.
     */
    public function getGroup(): string
    {
        return explode('.', $this->name)[0];
    }

    /**
     * Get the permission action type (e.g., 'read', 'write', 'delete').
     */
    public function getType(): ?PermissionType
    {
        $parts = explode('.', $this->name);
        $action = end($parts);

        return PermissionType::tryFrom($action);
    }

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::creating(function (self $permission): void {
            $permission->is_wildcard = str_ends_with($permission->name, '*');
        });
    }

    /**
     * Scope for wildcard permissions.
     */
    protected function scopeWildcard(Builder $query): Builder
    {
        return $query->where('name', 'like', '%*');
    }

    /**
     * Scope for permissions by group.
     */
    protected function scopeByGroup(Builder $query, string $group): Builder
    {
        return $query->where('name', 'like', $group.'.%');
    }

    /**
     * Get permission names - a permission only has itself.
     */
    public function getPermissionNames(): array
    {
        return [$this->name];
    }

    /**
     * Give role to the permission.
     */
    public function giveRoleTo(Model|string|int|array ...$roles): Model
    {
        return $this->assignRole(...$roles);
    }

    /**
     * Clear cached permissions/roles if caching is enabled.
     */
    protected function clearGuardCache(): void
    {
        if (! config('guard.cache.enabled', true)) {
            return;
        }

        Guard::clearCache();
    }
}
