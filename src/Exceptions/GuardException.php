<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Exceptions;

use Exception;

class GuardException extends Exception
{
    public static function modelNotConfigured(string $model): self
    {
        return new self(sprintf('The %s model is not configured in guard.php config.', $model));
    }

    public static function tableNotConfigured(string $table): self
    {
        return new self(sprintf('The %s table is not configured in guard.php config.', $table));
    }
}
