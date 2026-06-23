<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Concerns;

trait ParsesMiddlewareParameters
{
    /**
     * Parse comma-separated parameters into a flat array.
     */
    protected function parseParameters(array $params): array
    {
        $parsed = [];
        foreach ($params as $param) {
            foreach (explode(',', $param) as $item) {
                $parsed[] = trim($item);
            }
        }

        return $parsed;
    }
}
