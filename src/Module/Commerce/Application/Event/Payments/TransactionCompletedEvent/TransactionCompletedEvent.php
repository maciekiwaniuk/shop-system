<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Event\Payments\TransactionCompletedEvent;

readonly class TransactionCompletedEvent
{
    public function __construct(
        public string $transactionId,
        public string $completedAt,
    ) {
    }
}
