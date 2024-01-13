<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Contracts;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

interface Permission
{
    public function roles(): BelongsToMany;
}
