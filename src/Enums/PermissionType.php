<?php

declare(strict_types=1);

namespace AmdadulHaq\Guard\Enums;

enum PermissionType: string
{
    case READ = 'read';
    case WRITE = 'write';
    case DELETE = 'delete';
    case MANAGE = 'manage';
    case VIEW_ANY = 'view_any';
    case VIEW = 'view';
    case CREATE = 'create';
    case UPDATE = 'update';
    case RESTORE = 'restore';
    case FORCE_DELETE = 'force_delete';

    public function label(): string
    {
        return str_replace('_', ' ', ucfirst($this->value));
    }
}
