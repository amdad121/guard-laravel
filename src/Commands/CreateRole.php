<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Commands;

use AmdadulHaq\Guard\Contracts\User as UserContract;
use AmdadulHaq\Guard\Models\Role;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Database\Eloquent\Model;

use function Laravel\Prompts\text;

class CreateRole extends Command implements PromptsForMissingInput
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guard:create-role {name : The name of the role} {label? : The name of the label} {user? : ID of the user}';

    /**
     * The console command description.
     *
     * @var string
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

        $userId = $this->argument('user');

        if (! $userId) {
            $userId = text(
                label: 'ID of the user',
                required: false
            );
        }

        $role = Role::query()->firstOrCreate(['name' => $name], ['name' => $name, 'label' => $label]);

        $message = '';

        if ($userId) {
            /** @var Model|UserContract $userModel */
            $userModel = resolve(config('guard.models.user'));
            /** @var Model|null $user */
            $user = $userModel->find($userId);

            if ($user) {
                if ($user instanceof UserContract) {
                    $user->assignRole($role);
                    $message = 'Assign to the user ID of #'.$user->getKey().'.';
                } else {
                    $this->error('User model must implement UserContract.');
                    $this->newLine();

                    return self::INVALID;
                }
            } else {
                $this->error('User is not exists. Try to using with correct user ID.');
                $this->newLine();

                return self::INVALID;
            }
        }

        if ($role->wasRecentlyCreated) {
            $this->info('Role created successfully. ID of the role is #'.$role->id.'. '.$message);
            $this->newLine();

            return self::SUCCESS;
        }

        $this->info('Role already exist. ID of the role is #'.$role->id.'. '.$message);
        $this->newLine();

        return self::SUCCESS;
    }
}
