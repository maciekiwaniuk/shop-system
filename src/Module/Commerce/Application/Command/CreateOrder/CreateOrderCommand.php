<?php

declare(strict_types=1);

namespace App\Module\Commerce\Application\Command\CreateOrder;

use App\Module\Commerce\Application\DTO\CreateOrderDTO;
use App\Common\Application\Command\CommandInterface;

readonly class CreateOrderCommand implements CommandInterface
{
    public function __construct(
        public CreateOrderDTO $dto,
    ) {
    }
}
