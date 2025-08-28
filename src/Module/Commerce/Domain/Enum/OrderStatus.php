<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Enum;

enum OrderStatus: string
{
    case WAITING_FOR_PAYMENT = 'waiting_for_payment';
    case PREPARING_FOR_DELIVERY = 'preparing_for_delivery';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
}
