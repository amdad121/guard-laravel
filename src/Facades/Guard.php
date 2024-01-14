<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AmdadulHaq\Guard\Guard
 */
class Guard extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \AmdadulHaq\Guard\Guard::class;
    }
}
