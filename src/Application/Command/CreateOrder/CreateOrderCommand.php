<?php

declare(strict_types=1);

namespace App\Application\Command\CreateOrder;

use App\Domain\DTO\Order\CreateOrderDTO;

class CreateOrderCommand
{
    public function __construct(
        public readonly CreateOrderDTO $dto
    ) {
    }
}