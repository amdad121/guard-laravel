<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpgradeCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'guard:upgrade';

    /**
     * The console command description.
     */
    protected $description = 'Upgrade Guard package models from V1 to V2 architecture';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting Guard package upgrade process...');
        $this->newLine();

        $modelsPath = app_path('Models');

        if (! File::isDirectory($modelsPath)) {
            $this->error('App/Models directory not found.');

            return self::FAILURE;
        }

        $files = File::allFiles($modelsPath);
        $upgradedCount = 0;

        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }

            $content = File::get($file->getRealPath());
            $originalContent = $content;

            // 1. Update Traits
            $content = str_replace(
                'use AmdadulHaq\Guard\Concerns\HasRoles;',
                'use AmdadulHaq\Guard\Concerns\Roleable;',
                $content
            );

            // Remove HasPermissions trait usage entirely
            $content = str_replace("use AmdadulHaq\Guard\Concerns\HasPermissions;\n", '', $content);
            $content = str_replace("use AmdadulHaq\Guard\Concerns\HasPermissions;", '', $content);

            // Replace actual trait uses inside the class
            $content = str_replace('use HasRoles;', 'use Roleable;', $content);
            $content = str_replace("use HasPermissions;\n", '', $content);
            $content = str_replace('use HasPermissions, HasRoles;', 'use Roleable;', $content);
            $content = str_replace('use HasRoles, HasPermissions;', 'use Roleable;', $content);
            $content = str_replace('use HasPermissions;', '', $content);

            // 2. Update Contracts
            $content = str_replace(
                'use AmdadulHaq\Guard\Contracts\Roles as RolesContract;',
                'use AmdadulHaq\Guard\Contracts\Roleable as RoleableContract;',
                $content
            );
            $content = str_replace(
                'use AmdadulHaq\Guard\Contracts\Roles;',
                'use AmdadulHaq\Guard\Contracts\Roleable as RoleableContract;',
                $content
            );
            $content = str_replace(
                'use AmdadulHaq\Guard\Contracts\User as UserContract;',
                'use AmdadulHaq\Guard\Contracts\Roleable as RoleableContract;',
                $content
            );
            $content = str_replace(
                'use AmdadulHaq\Guard\Contracts\User;',
                'use AmdadulHaq\Guard\Contracts\Roleable as RoleableContract;',
                $content
            );

            // Remove Permissions contract entirely
            $content = str_replace("use AmdadulHaq\Guard\Contracts\Permissions;\n", '', $content);
            $content = str_replace("use AmdadulHaq\Guard\Contracts\Permissions;", '', $content);

            // Deduplicate the RoleableContract import if both User and Roles were imported originally
            $content = preg_replace(
                "/(use AmdadulHaq\\\\Guard\\\\Contracts\\\\Roleable as RoleableContract;\r?\n){2,}/",
                "use AmdadulHaq\\Guard\\Contracts\\Roleable as RoleableContract;\n",
                $content
            );

            // Replace implementation declarations anywhere in the file
            $content = preg_replace('/\bRolesContract\b/', 'RoleableContract', $content);
            $content = preg_replace('/\bUserContract\b/', 'RoleableContract', $content);
            
            // For 'User' and 'Roles', only match them if they are followed by a comma, opening brace, or end of line
            // This prevents replacing random uses of the word 'User'
            $content = preg_replace('/(?<=implements\s|,\s)\bUser\b(?=\s*,|\s*\{|\s*$)/', 'RoleableContract', $content);
            $content = preg_replace('/(?<=implements\s|,\s)\bRoles\b(?=\s*,|\s*\{|\s*$)/', 'RoleableContract', $content);

            // Clean up any double RoleableContract caused by previous replacements (e.g. implements User, Roles)
            $content = preg_replace('/RoleableContract\s*,\s*RoleableContract/', 'RoleableContract', $content);

            if ($content !== $originalContent) {
                File::put($file->getRealPath(), $content);
                $this->line("Upgraded: {$file->getFilename()}");
                $upgradedCount++;
            }
        }

        $this->newLine();

        if ($upgradedCount > 0) {
            $this->info("Successfully upgraded {$upgradedCount} models to V2 architecture!");
        } else {
            $this->info("No models required upgrading. You're already up to date.");
        }

        $this->newLine();

        $migrationExists = ! empty(glob(database_path('migrations/*_create_permissions_table.php'))) || ! empty(glob(database_path('migrations/*_create_roles_table.php')));

        if ($migrationExists) {
            $this->info('Existing Guard migrations found. Publishing V2 schema upgrade migrations...');
            $this->call('vendor:publish', [
                '--tag' => 'guard-upgrade-migrations',
            ]);
            $this->info('Upgrade migrations successfully published! Please run `php artisan migrate`.');
        } else {
            $this->info('No existing Guard migrations found. You can publish them using:');
            $this->line('  php artisan vendor:publish --tag="guard-migrations"');
        }

        return self::SUCCESS;
    }
}
