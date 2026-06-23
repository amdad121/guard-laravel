<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class BaseCommand extends Command implements PromptsForMissingInput
{
    /**
     * Resolve a model from the config key.
     */
    protected function resolveModel(string $configKey): Model
    {
        return resolve(config("guard.models.{$configKey}"));
    }

    /**
     * Find an entity by identifier (ID or specific columns).
     */
    protected function findByIdentifier(Model $model, string $identifier, array $searchColumns): ?Model
    {
        return $model::query()
            ->where(function (Builder $query) use ($identifier, $model, $searchColumns): void {
                if (is_numeric($identifier)) {
                    $query->where($model->getKeyName(), (int) $identifier);
                }

                foreach ($searchColumns as $column) {
                    $query->orWhere($column, $identifier);
                }
            })
            ->first();
    }
}
