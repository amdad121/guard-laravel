<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Enums;

enum CacheKey: string
{
    case PERMISSIONS = 'guard_permissions';
    case ROLES = 'guard_roles';
}
