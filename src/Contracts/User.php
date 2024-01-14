<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Contracts;

use AmdadulHaq\Guard\Contracts\Role as RoleContract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface User
{
    public function roles(): BelongsToMany;

    public function assignRole(Model $role): Model;

    public function syncRoles(array $roles): array;

    public function revokeRole(RoleContract $role): int;

    public function hasRole(string|Collection $role): bool;

    public function hasPermission(Model $permission): bool;
}
