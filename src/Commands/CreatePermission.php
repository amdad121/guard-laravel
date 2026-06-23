<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Commands;

use Illuminate\Database\Eloquent\Model;

class CreatePermission extends BaseCommand
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
        $label = $this->argument('label');
        $roleIdentifier = $this->argument('role');

        $permissionModel = $this->resolveModel('permission');
        $permission = $permissionModel::query()->firstOrCreate(['name' => $name], ['name' => $name, 'label' => $label]);

        $message = '';

        if ($roleIdentifier) {
            $roleModel = $this->resolveModel('role');
            $role = $this->findByIdentifier($roleModel, $roleIdentifier, ['name']);

            if ($role instanceof Model) {
                if (method_exists($role, 'givePermissionTo')) {
                    $role->givePermissionTo($permission);
                    $message = 'Permission given to the role ID of #'.$role->getKey().'.';
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

        $this->info('Permission already exists. ID of the permission is #'.$permission->getKey().'. '.$message);
        $this->newLine();

        return self::SUCCESS;
    }
}
