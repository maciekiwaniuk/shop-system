<?php

declare(strict_types=1);

namespace App\Module\Commerce\Domain\Event;

readonly class OrderStatusChangedEvent
{
    public function __construct(
        public string $orderId,
    ) {
    }
}
