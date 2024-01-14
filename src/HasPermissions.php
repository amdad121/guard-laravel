<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard;

use AmdadulHaq\Guard\Contracts\Permission as PermissionContract;
use Illuminate\Database\Eloquent\Model;

trait HasPermissions
{
    public function givePermissionTo(PermissionContract $permission): Model
    {
        return $this->permissions()->save($permission);
    }

    public function syncPermissions(array $permissions): array
    {
        return $this->permissions()->sync($permissions);
    }

    public function revokePermissionTo(PermissionContract $permission): int
    {
        return $this->permissions()->detach($permission);
    }
}
