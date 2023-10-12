<?php

declare(strict_types=1);

namespace App\Module\Order\Domain\Enum;

enum OrderStatus: string
{
    case WAITING_FOR_PAYMENT = 'waiting_for_payment';
    case IN_DELIVERY = 'in_delivery';
    case DELIVERED = 'delivered';
}
