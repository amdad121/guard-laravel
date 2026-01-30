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
     *
     * @param  string  $string  The input string
     * @return string The singular form of the string
     */
    public function getSingularName(string $string): string
    {
        return Str::singular($string);
    }

    /**
     * Get table name of a model.
     *
     * @param  string  $class  The fully qualified class name
     * @return string The table name for the model
     */
    public function getTableName(string $class): string
    {
        static $cache = [];

        return $cache[$class] ??= resolve($class)->getTable();
    }

    /**
     * Sort an array of strings.
     *
     * @param  array<int, string>  $array  The array to sort
     * @return array<int, string> The sorted array
     */
    public function getSortPivotTable(array $array): array
    {
        return collect($array)
            ->sort()
            ->values()
            ->all();
    }

    /**
     * Get pivot table name for a given array of models.
     *
     * @param  array<int, string>  $array  The array of model classes
     * @return string The pivot table name
     */
    public function getPivotTableName(array $array): string
    {
        return collect($array)
            ->map(fn (string $value): string => self::getSingularName(self::getTableName($value)))
            ->sort()
            ->values()
            ->implode('_');
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
