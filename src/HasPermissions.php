<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard;

use AmdadulHaq\Guard\Models\Permission;
use Illuminate\Database\Eloquent\Model;

trait HasPermissions
{
    public function givePermissionTo(Permission $permission): Model
    {
        return $this->permissions()->save($permission);
    }

    public function syncPermissions(array $permissions)
    {
        return $this->permissions()->sync($permissions);
    }

    public function revokePermissionTo(Permission $permission)
    {
        return $this->permissions()->detach($permission);
    }
}
