<?php

declare(strict_types=1);

namespace App\Domain\Enum;

enum UserRole: string
{
    case USER = 'user';
    case ADMIN = 'admin';
}
