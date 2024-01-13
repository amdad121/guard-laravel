<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Commands;

use AmdadulHaq\Guard\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;

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
    public function handle()
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

        /** @phpstan-ignore-next-line */
        $role = Role::firstOrCreate([
            'name' => $name,
        ], [
            'name' => $name,
            'label' => $label,
        ]);

        $message = '';

        if ($userId) {
            /** @phpstan-ignore-next-line */
            $user = User::find($userId);

            if ($user) {
                $user->assignRole($role);
                $message = 'Assign to the user ID of #'.$user->id.'.';
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
