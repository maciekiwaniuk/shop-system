<?php

declare(strict_types=1);

namespace App\Application\Command\CreateOrder;

use App\Application\Command\CommandInterface;
use App\Application\DTO\Order\CreateOrderDTO;

class CreateOrderCommand implements CommandInterface
{
    public function __construct(
        public readonly CreateOrderDTO $dto
    ) {
    }
}
