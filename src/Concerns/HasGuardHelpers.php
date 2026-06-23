<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Concerns;

use AmdadulHaq\Guard\Facades\Guard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasGuardHelpers
{
    /**
     * Clear cached permissions/roles if caching is enabled.
     */
    protected function clearGuardCache(): void
    {
        if (! config('guard.cache.enabled', true)) {
            return;
        }

        Guard::clearCache();
    }

    /**
     * Resolve a model from an identifier (name or ID) or return the provided model.
     */
    protected function resolveModel(string $configKey, Model|string|int $identifier, bool $throw = true): ?Model
    {
        if ($identifier instanceof Model) {
            return $identifier;
        }

        $modelClass = config("guard.models.{$configKey}");
        $query = $modelClass::query();

        if (is_numeric($identifier)) {
            $query->whereKey((int) $identifier);
        } else {
            $query->where('name', $identifier);
        }

        return $throw ? $query->firstOrFail() : $query->first();
    }

    /**
     * Get IDs from array of models, IDs, or names.
     */
    protected function getModelIds(string $configKey, array $items): array
    {
        return collect($items)
            ->map(function ($item) use ($configKey): ?int {
                if ($item instanceof Model) {
                    return (int) $item->getKey();
                }

                if (is_numeric($item)) {
                    return (int) $item;
                }

                return $this->resolveModel($configKey, $item, false)?->getKey();
            })
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Flatten arguments into a simple array.
     */
    protected function flattenArgs(array $args): array
    {
        return Arr::flatten($args);
    }
}
