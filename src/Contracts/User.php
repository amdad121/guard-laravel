<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Contract for user models that use Guard with role-based permission checks.
 */
interface User extends Roles
{
    /**
     * Get all permissions inherited through roles.
     */
    public function getPermissions(): Collection;

    /**
     * Get all inherited permission names.
     */
    public function getPermissionNames(): array;

    /**
     * Check if the user has a specific permission through roles.
     */
    public function hasPermission(Model|string $permission): bool;
}
