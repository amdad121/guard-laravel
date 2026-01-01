<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Enums;

enum CacheKey: string
{
    case PERMISSIONS = 'guard_permissions';
    case ROLES = 'guard_roles';
    case USER_PERMISSIONS = 'guard_user_permissions_';
    case USER_ROLES = 'guard_user_roles_';
    case ROLE_PERMISSIONS = 'guard_role_permissions_';
}
