<?php

declare(strict_types=1);

namespace App\Module\User\Domain\Enum;

enum UserRole: string
{
    case USER = 'ROLE_USER';
    case ADMIN = 'ROLE_ADMIN';
}
