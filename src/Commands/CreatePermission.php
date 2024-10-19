<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Commands;

use AmdadulHaq\Guard\Models\Permission;
use AmdadulHaq\Guard\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

use function Laravel\Prompts\text;

class CreatePermission extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guard:create-permission {name : The name of the permission} {label? : The name of the label} {role? : ID of the role}';

    /**
     * The console command description.
     *
     * @var string
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
                label: 'The name of the role',
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

        $roleId = $this->argument('role');

        if (! $roleId) {
            $roleId = text(
                label: 'ID of the role',
                required: false
            );
        }

        $permission = Permission::firstOrCreate(['name' => $name], ['name' => $name, 'label' => $label]);

        $message = '';

        if ($roleId) {
            $role = Role::find($roleId);

            if ($role) {
                $role->givePermissionTo($permission);
                $message = 'Permission give to the role ID of #'.$role->id.'.';
            } else {
                $this->error('Role is not exists. Try to using with correct role ID.');
                $this->newLine();

                return self::INVALID;
            }
        }

        if ($permission->wasRecentlyCreated) {
            $this->info('Permission created successfully. ID of the permission is #'.$permission->id.'. '.$message);
            $this->newLine();

            return self::SUCCESS;
        }

        $this->info('Permission already exist. ID of the permission is #'.$permission->id.'. '.$message);
        $this->newLine();

        return self::SUCCESS;
    }
}
