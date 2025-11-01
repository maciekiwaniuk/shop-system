<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Event\Payments\TransactionCanceledEvent;

readonly class TransactionCanceledEvent
{
    public function __construct(
        public string $transactionId,
        public string $canceledAt,
    ) {
    }
}
