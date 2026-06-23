<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Commands;

use AmdadulHaq\Guard\Contracts\Roleable;
use Illuminate\Database\Eloquent\Model;

class CreateRole extends BaseCommand
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'guard:create-role {name : The name of the role} {label? : The name of the label} {user? : ID, email, or name of the user}';

    /**
     * The console command description.
     */
    protected $description = 'Create a Role';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $label = $this->argument('label');
        $userIdentifier = $this->argument('user');

        $roleModel = $this->resolveModel('role');
        $role = $roleModel::query()->firstOrCreate(['name' => $name], ['name' => $name, 'label' => $label]);

        $message = '';

        if ($userIdentifier) {
            $userModel = $this->resolveModel('user');
            $user = $this->findByIdentifier($userModel, $userIdentifier, ['email', 'name']);

            if ($user instanceof Model) {
                if ($user instanceof Roleable) {
                    $user->assignRole($role);
                    $message = 'Assigned to the user ID of #'.$user->getKey().'.';
                } else {
                    $this->error('User model must implement Roleable contract.');
                    $this->newLine();

                    return self::INVALID;
                }
            } else {
                $this->error('User does not exist. Use a valid user ID, email, or name.');
                $this->newLine();

                return self::INVALID;
            }
        }

        if ($role->wasRecentlyCreated) {
            $this->info('Role created successfully. ID of the role is #'.$role->getKey().'. '.$message);
            $this->newLine();

            return self::SUCCESS;
        }

        $this->info('Role already exists. ID of the role is #'.$role->getKey().'. '.$message);
        $this->newLine();

        return self::SUCCESS;
    }
}
