<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Exceptions;

use Exception;

class PermissionDoesNotExistException extends Exception
{
    public static function named(string $permissionName): self
    {
        return new self(sprintf('There is no permission named `%s`.', $permissionName));
    }

    public static function withId(int $permissionId): self
    {
        return new self(sprintf('There is no permission with ID `%d`.', $permissionId));
    }
}
