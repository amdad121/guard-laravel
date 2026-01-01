<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Exceptions;

use Exception;

class PermissionDeniedException extends Exception
{
    public static function create(string $permission): self
    {
        return new self(sprintf('User does not have permission: %s', $permission));
    }

    public static function roleNotAssigned(string $role): self
    {
        return new self(sprintf('User does not have role: %s', $role));
    }
}
