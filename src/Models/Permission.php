<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Models;

use AmdadulHaq\Guard\Concerns\ChecksRoles;
use AmdadulHaq\Guard\Concerns\ResolvesModels;
use AmdadulHaq\Guard\Contracts\Roles as RolesContract;
use AmdadulHaq\Guard\Enums\PermissionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $name
 * @property string|null $label
 * @property string|null $description
 * @property string|null $group
 * @property bool $is_wildcard
 */
class Permission extends Model implements RolesContract
{
    use ChecksRoles;
    use ResolvesModels;

    /** @var array<int, string> */
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
        return $this->belongsToMany(config('guard.models.role'));
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
     * Permissions relation - permissions don't have permissions, return empty relation.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(config('guard.models.permission'));
    }

    /**
     * Get permission names - a permission only has itself.
     *
     * @return array<int, string>
     */
    public function getPermissionNames(): array
    {
        return [$this->name];
    }

    /**
     * Get role names assigned to this permission.
     *
     * @return array<int, string>
     */
    public function getRoleNames(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * Give role to the permission.
     */
    public function giveRoleTo(Model|string $role): Model
    {
        $role = $this->resolveRole($role);
        $this->roles()->attach($role);

        return $this;
    }

    /**
     * Sync roles to the permission.
     *
     * @param  array<int, int|string>  $roles
     * @return array<string, array<int, int|string>>
     */
    public function syncRoles(array $roles): array
    {
        $roleIds = $this->getRoleIds($roles);

        return $this->roles()->sync($roleIds);
    }

    /**
     * Revoke role from the permission.
     */
    public function revokeRole(Model|string $role): int
    {
        $role = $this->resolveRole($role, false);

        return $this->roles()->detach($role);
    }

    /**
     * Get role IDs from array of IDs or names.
     *
     * @param  array<int, int|string>  $roles
     * @return array<int, int>
     */
    protected function getRoleIds(array $roles): array
    {
        return collect($roles)
            ->map(fn ($role): ?int => is_numeric($role) ? (int) $role : $this->getRoleIdByName($role))
            ->filter()
            ->values()
            ->all();
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
     * Alias for giveRoleTo - assigns role to permission.
     */
    public function assignRole(Model|string $role): Model
    {
        return $this->giveRoleTo($role);
    }
}
