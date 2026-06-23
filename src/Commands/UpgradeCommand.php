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

            // Replace implementation declarations
            $content = preg_replace('/implements\s+User,\s*RolesContract/', 'implements RoleableContract', $content);
            $content = preg_replace('/implements\s+RolesContract,\s*User/', 'implements RoleableContract', $content);
            $content = preg_replace('/implements\s+User,\s*Roles/', 'implements RoleableContract', $content);
            $content = preg_replace('/implements\s+Roles,\s*User/', 'implements RoleableContract', $content);
            
            $content = str_replace('implements RolesContract', 'implements RoleableContract', $content);
            $content = str_replace('implements Roles', 'implements RoleableContract', $content);
            $content = preg_replace('/implements\s+User\b/', 'implements RoleableContract', $content);
            $content = preg_replace('/implements\s+UserContract\b/', 'implements RoleableContract', $content);

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

        return self::SUCCESS;
    }
}
