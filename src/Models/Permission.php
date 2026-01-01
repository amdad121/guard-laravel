<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Models;

use AmdadulHaq\Guard\Contracts\Permission as PermissionContract;
use AmdadulHaq\Guard\Enums\PermissionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property string $name
 * @property bool $is_wildcard
 */
class Permission extends Model implements PermissionContract
{
    /** @var array<int, string> */
    protected $guarded = [];

    public function getName(): string
    {
        return $this->name;
    }

    protected function casts(): array
    {
        return [
            'is_wildcard' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $permission): void {
            $permission->is_wildcard = str_ends_with($permission->name, '*');
        });
    }

    public function getTable(): string
    {
        static $table;

        if ($table === null) {
            $table = config('guard.tables.permissions', parent::getTable());
        }

        return $table;
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('guard.models.role'));
    }

    protected function scopeWildcard(Builder $query): Builder
    {
        return $query->where('name', 'like', '%*');
    }

    protected function scopeByGroup(Builder $query, string $group): Builder
    {
        return $query->where('name', 'like', $group.'.%');
    }

    public function isWildcard(): bool
    {
        return str_ends_with($this->name, '*');
    }

    public function getGroup(): string
    {
        return explode('.', $this->name)[0];
    }

    public function getType(): ?PermissionType
    {
        $parts = explode('.', $this->name);
        $action = end($parts);

        return PermissionType::tryFrom($action);
    }
}
