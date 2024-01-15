<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Contracts;

use AmdadulHaq\Guard\Contracts\Permission as PermissionContract;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Role
{
    public function permissions(): BelongsToMany;

    public function users(): BelongsToMany;

    public function givePermissionTo(PermissionContract $permission): Model;

    public function syncPermissions(array $permissions): array;

    public function revokePermissionTo(PermissionContract $permission): int;
}
