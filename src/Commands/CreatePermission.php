<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use function Laravel\Prompts\text;

class CreatePermission extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'guard:create-permission {name : The name of the permission} {label? : The name of the label} {role? : ID or name of the role}';

    /**
     * The console command description.
     */
    protected $description = 'Create a Permission';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');

        if (! $name) {
            $name = text(
                label: 'The name of the permission',
                required: true
            );
        }

        $label = $this->argument('label');

        if (! $label) {
            $label = text(
                label: 'The name of the label',
                required: false
            );
        }

        $roleIdentifier = $this->argument('role');

        if (! $roleIdentifier) {
            $roleIdentifier = text(
                label: 'ID or name of the role',
                required: false
            );
        }

        $permissionModel = $this->resolvePermissionModel();
        $permission = $permissionModel::query()->firstOrCreate(['name' => $name], ['name' => $name, 'label' => $label]);

        $message = '';

        if ($roleIdentifier) {
            $role = $this->findRole($roleIdentifier);

            if ($role instanceof Model) {
                if (method_exists($role, 'givePermissionTo')) {
                    $role->givePermissionTo($permission);
                    $message = 'Permission give to the role ID of #'.$role->getKey().'.';
                } else {
                    $this->error('Role model must support permission management.');
                    $this->newLine();

                    return self::INVALID;
                }
            } else {
                $this->error('Role does not exist. Use a valid role ID or name.');
                $this->newLine();

                return self::INVALID;
            }
        }

        if ($permission->wasRecentlyCreated) {
            $this->info('Permission created successfully. ID of the permission is #'.$permission->getKey().'. '.$message);
            $this->newLine();

            return self::SUCCESS;
        }

        $this->info('Permission already exist. ID of the permission is #'.$permission->getKey().'. '.$message);
        $this->newLine();

        return self::SUCCESS;
    }

    protected function resolvePermissionModel(): Model
    {
        return resolve(config('guard.models.permission'));
    }

    protected function resolveRoleModel(): Model
    {
        return resolve(config('guard.models.role'));
    }

    protected function findRole(string $identifier): ?Model
    {
        $roleModel = $this->resolveRoleModel();

        return $roleModel::query()
            ->where(function (Builder $query) use ($identifier, $roleModel): void {
                if (is_numeric($identifier)) {
                    $query->where($roleModel->getKeyName(), (int) $identifier);
                }

                $query->orWhere('name', $identifier);
            })
            ->first();
    }
}
