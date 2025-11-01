<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class OrderStatusChangedEvent extends Event
{
    public function __construct(
        public readonly string $orderId,
    ) {
    }
}
