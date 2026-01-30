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
 * @property string|null $label
 * @property string|null $description
 * @property string|null $group
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

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getTable(): string
    {
        return config('guard.tables.permissions', parent::getTable());
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('guard.models.role'));
    }

    public function isWildcard(): bool
    {
        return str_ends_with($this->name, '*');
    }

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

    protected static function booted(): void
    {
        static::creating(function (self $permission): void {
            $permission->is_wildcard = str_ends_with($permission->name, '*');
        });
    }

    protected function scopeWildcard(Builder $query): Builder
    {
        return $query->where('name', 'like', '%*');
    }

    protected function scopeByGroup(Builder $query, string $group): Builder
    {
        return $query->where('name', 'like', $group.'.%');
    }
}
