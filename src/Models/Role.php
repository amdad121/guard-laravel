<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Models;

use AmdadulHaq\Guard\Concerns\HasPermissions;
use AmdadulHaq\Guard\Contracts\Permissions as PermissionsContract;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $name
 * @property string|null $label
 * @property string|null $description
 * @property bool $is_guarded
 */
class Role extends Model implements PermissionsContract
{
    use HasPermissions;

    /** @var array<int, string> */
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
        return $this->belongsToMany(config('guard.models.permission'));
    }

    /**
     * Users relation.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(config('guard.models.user'), 'role_user');
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
     * Roles relation.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('guard.models.role'));
    }

    /**
     * Get role names - a role only has itself.
     */
    public function getRoleNames(): array
    {
        return [$this->name];
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
