<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Contracts;

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
}
