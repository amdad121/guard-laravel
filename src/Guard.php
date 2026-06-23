<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard;

use AmdadulHaq\Guard\Enums\CacheKey;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Guard
{
    /**
     * Get singular name of a string.
     */
    public function getSingularName(string $string): string
    {
        return Str::singular($string);
    }

    /**
     * Get table name of a model.
     */
    public function getTableName(string $class): string
    {
        return resolve($class)->getTable();
    }

    /**
     * Get pivot table name for a given array of models.
     */
    public function getPivotTableName(array $array): string
    {
        $names = array_map(
            fn (string $value): string => $this->getSingularName($this->getTableName($value)),
            $array
        );

        sort($names);

        return implode('_', $names);
    }

    /**
     * Clear all Guard cache entries.
     */
    public function clearCache(): void
    {
        Cache::forget(CacheKey::PERMISSIONS->value);
        Cache::forget(CacheKey::ROLES->value);
    }
}
