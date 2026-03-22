<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Commands;

use AmdadulHaq\Guard\Contracts\Roles as RolesContract;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

use function Laravel\Prompts\text;

class CreateRole extends Command implements PromptsForMissingInput
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

        $userIdentifier = $this->argument('user');

        if (! $userIdentifier) {
            $userIdentifier = text(
                label: 'ID, email, or name of the user',
                required: false
            );
        }

        $roleModel = $this->resolveRoleModel();
        $role = $roleModel::query()->firstOrCreate(['name' => $name], ['name' => $name, 'label' => $label]);

        $message = '';

        if ($userIdentifier) {
            $user = $this->findUser($userIdentifier);

            if ($user instanceof Model) {
                if ($user instanceof RolesContract) {
                    $user->assignRole($role);
                    $message = 'Assign to the user ID of #'.$user->getKey().'.';
                } else {
                    $this->error('User model must implement Roles contract.');
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

        $this->info('Role already exist. ID of the role is #'.$role->getKey().'. '.$message);
        $this->newLine();

        return self::SUCCESS;
    }

    protected function resolveUserModel(): Model
    {
        return resolve(config('guard.models.user'));
    }

    protected function resolveRoleModel(): Model
    {
        return resolve(config('guard.models.role'));
    }

    protected function findUser(string $identifier): ?Model
    {
        $userModel = $this->resolveUserModel();

        return $userModel::query()
            ->where(function (Builder $query) use ($identifier, $userModel): void {
                if (is_numeric($identifier)) {
                    $query->where($userModel->getKeyName(), (int) $identifier);
                }

                $query->orWhere('email', $identifier)
                    ->orWhere('name', $identifier);
            })
            ->first();
    }
}
