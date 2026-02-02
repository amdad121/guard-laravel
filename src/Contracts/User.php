<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Contracts;

/**
 * Contract for user models that use Guard with full ACL support.
 * Extends both Roles and Permissions contracts for complete role-based access control.
 */
interface User extends Permissions, Roles
{
    // Combines all methods from Roles and Permissions contracts
}
