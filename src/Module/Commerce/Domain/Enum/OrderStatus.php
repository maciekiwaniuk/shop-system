<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Enum;

enum OrderStatus: string
{
    case WAITING_FOR_PAYMENT = 'waiting_for_payment';
    case CANCELED = 'canceled';
    case COMPLETED = 'completed';
}
