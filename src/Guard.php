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
        return app($class)->getTable();
    }

    public function getSortPivotTable(array $array): array
    {
        $collection = collect($array);

        $sorted = $collection->sort();

        return $sorted->values()->all();
    }

    public function getPivotTableName(array $array): string
    {
        $collection = collect($array)->map(function ($value) {
            return self::getSingularName(self::getTableName($value));
        });

        $sorted = $collection->sort();

        return $sorted->values()->implode('_');
    }
}
