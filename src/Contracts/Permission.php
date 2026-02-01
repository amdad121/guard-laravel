<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Contracts;

use AmdadulHaq\Guard\Enums\PermissionType;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Permission
{
    /**
     * Get the name of the permission.
     */
    public function getName(): string;

    /**
     * Get the roles associated with the permission.
     */
    public function roles(): BelongsToMany;

    /**
     * Check if the permission is a wildcard (ends with '*').
     */
    public function isWildcard(): bool;

    /**
     * Get the permission group (prefix before the last dot).
     */
    public function getGroup(): string;

    /**
     * Get the permission type (e.g., 'read', 'write', 'delete').
     */
    public function getType(): ?PermissionType;

    /**
     * Get the permission label.
     */
    public function getLabel(): ?string;

    /**
     * Get the permission description.
     */
    public function getDescription(): ?string;
}
