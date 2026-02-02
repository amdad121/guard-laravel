<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

// @phpstan-ignore trait.unused
trait HasRoles
{
    use ChecksRoles;
    use ResolvesModels;

    /**
     * Get the roles relation.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(config('guard.models.role'));
    }

    /**
     * Assign a role to the model.
     */
    public function assignRole(Model|string $role): self
    {
        $role = $this->resolveRole($role);
        $this->roles()->attach($role);

        return $this;
    }

    /**
     * Sync roles to the model.
     */
    public function syncRoles(array $roles, bool $detach = true): array
    {
        $roleIds = $this->getRoleIds($roles);

        return $detach
            ? $this->roles()->sync($roleIds)
            : $this->roles()->syncWithoutDetaching($roleIds);
    }

    /**
     * Sync roles to the model without detaching.
     */
    public function syncRolesWithoutDetaching(array $roles): array
    {
        return $this->syncRoles($roles, false);
    }

    /**
     * Revoke a role from the model.
     */
    public function revokeRole(Model|string $role): int
    {
        $role = $this->resolveRole($role, false);

        return $this->roles()->detach($role);
    }

    /**
     * Revoke all roles from the model.
     */
    public function revokeRoles(): int
    {
        return $this->roles()->detach();
    }

    /**
     * Get the role names.
     */
    public function getRoleNames(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    /**
     * Get role IDs from array of IDs or names.
     */
    protected function getRoleIds(array $roles): array
    {
        return collect($roles)
            ->map(fn ($role) => is_numeric($role) ? (int) $role : $this->getRoleIdByName($role))
            ->filter()
            ->values()
            ->toArray();
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
     * Scope a query to include models with specific roles.
     */
    protected function scopeWithRoles(Builder $query, string|array $roles): Builder
    {
        return $query->whereHas('roles', fn (Builder $q) => $q->whereIn('name', (array) $roles));
    }
}
