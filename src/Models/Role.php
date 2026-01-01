<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Models;

use AmdadulHaq\Guard\Contracts\Role as RoleContract;
use AmdadulHaq\Guard\HasPermissions;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $name
 * @property bool $is_guarded
 */
class Role extends Model implements RoleContract
{
    use HasPermissions;

    /** @var array<int, string> */
    protected $guarded = [];

    public function getName(): string
    {
        return $this->name;
    }

    protected function casts(): array
    {
        return [
            'is_guarded' => 'boolean',
        ];
    }

    public function getTable(): string
    {
        static $table;

        if ($table === null) {
            $table = config('guard.tables.roles', parent::getTable());
        }

        return $table;
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(config('guard.models.permission'));
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(config('guard.models.user'), 'role_user');
    }

    protected function scopeGuarded(Builder $query): Builder
    {
        return $query->where('is_guarded', true);
    }

    protected function scopeUnguarded(Builder $query): Builder
    {
        return $query->where('is_guarded', false);
    }

    public function isProtectedRole(): bool
    {
        return $this->is_guarded ?? false;
    }

    public function getPermissionNames(): array
    {
        return $this->permissions->pluck('name')->toArray();
    }
}
