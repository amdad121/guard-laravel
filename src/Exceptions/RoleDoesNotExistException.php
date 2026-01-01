<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Exceptions;

use Exception;

class RoleDoesNotExistException extends Exception
{
    public static function named(string $roleName): self
    {
        return new self(sprintf('There is no role named `%s`.', $roleName));
    }

    public static function withId(int $roleId): self
    {
        return new self(sprintf('There is no role with ID `%d`.', $roleId));
    }
}
