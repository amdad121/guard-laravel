<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard;

use Illuminate\Support\Str;

class Guard
{
    public function getSingularName(string $string): string
    {
        return Str::singular($string);
    }

    public function getTableName(string $class): string
    {
        static $cache = [];

        if (! isset($cache[$class])) {
            $cache[$class] = resolve($class)->getTable();
        }

        return $cache[$class];
    }

    /**
     * @param  array<int, string>  $array
     * @return array<int, string>
     */
    public function getSortPivotTable(array $array): array
    {
        return collect($array)
            ->sort()
            ->values()
            ->all();
    }

    /**
     * @param  array<int, string>  $array
     */
    public function getPivotTableName(array $array): string
    {
        return collect($array)
            ->map(fn (string $value): string => self::getSingularName(self::getTableName($value)))
            ->sort()
            ->values()
            ->implode('_');
    }
}
